/**
 * OmTravel — Premium Full-Screen 3D Airplane Loading Experience
 *
 * Plays once per browser session on the homepage for at least 10 seconds:
 *   0–2s: Minimal bottom-left airport & small airliner visible; engines spool up
 *   2–4s: Airliner accelerates along the runway and lifts its nose (Vr)
 *   4–6s: Airliner physically lifts off, retracts landing gear, climbs diagonally
 *   6–10s: Continuous bottom-left -> top-right ascent with realistic perspective recession
 *   >10s: Smooth cross-fade reveal into the homepage once resources are ready
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'omtravel_intro_played';
    const MIN_DURATION_MS = 10000;
    const THREE_CDN = 'https://cdn.jsdelivr.net/npm/three@0.169.0/build/three.min.js';
    const GSAP_CDN = 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js';

    const root = document.documentElement;
    const overlay = document.getElementById('omIntroLoader');

    // Bail immediately if loader shouldn't run (reduced motion, already played, or missing DOM)
    if (!overlay || !root.classList.contains('om-intro-active')) {
        if (overlay) {
            overlay.style.display = 'none';
        }
        return;
    }

    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        finishIntroImmediately();
        return;
    }

    const cloudsCanvas = document.getElementById('omLoaderCloudsCanvas');
    const airportCanvas = document.getElementById('omLoaderAirportCanvas');
    const webglCanvas = document.getElementById('omLoaderWebglCanvas');
    const fallbackStage = document.getElementById('omLoaderFallbackStage');
    const fallbackPlane = document.getElementById('omLoaderPlaneFallback');
    const fallbackGear = document.getElementById('omFallbackGear');
    const progressFill = document.getElementById('omLoaderProgressFill');

    const startTime = performance.now();
    let resourcesReady = document.readyState === 'complete';
    let hasExited = false;
    let isDisposed = false;
    let rafId = null;

    const markResourcesLoaded = () => {
        resourcesReady = true;
    };

    if (!resourcesReady) {
        window.addEventListener('load', markResourcesLoaded, { once: true });
    }

    // Pre-decode active hero background image while loader plays
    const firstHeroSlide = document.querySelector('.hero-slide.is-active');
    if (firstHeroSlide) {
        const bgMatch = (firstHeroSlide.style.backgroundImage || '').match(/url\(['"]?(.*?)['"]?\)/);
        if (bgMatch && bgMatch[1]) {
            const preloadImg = new Image();
            preloadImg.src = bgMatch[1];
        }
    }

    function markSessionPlayed() {
        try {
            window.sessionStorage.setItem(STORAGE_KEY, '1');
        } catch (err) {
            // Ignore storage access restrictions in private browsing modes
        }
    }

    function finishIntroImmediately() {
        markSessionPlayed();
        root.classList.remove('om-intro-active', 'om-intro-revealing');
        if (overlay) {
            overlay.style.display = 'none';
        }
        window.dispatchEvent(new CustomEvent('om:intro-complete'));
    }

    // -------------------------------------------------------------------------
    // Math & Responsive Runway Geometry Helpers
    // -------------------------------------------------------------------------
    function clamp01(val) {
        return Math.max(0, Math.min(1, val));
    }

    function lerp(a, b, t) {
        return a + (b - a) * t;
    }

    function lerpPt(a, b, t) {
        return { x: lerp(a.x, b.x, t), y: lerp(a.y, b.y, t) };
    }

    function smoothstep(edge0, edge1, x) {
        const t = clamp01((x - edge0) / (edge1 - edge0));
        return t * t * (3 - 2 * t);
    }

    function cubicBezierPt(p0, p1, p2, p3, t) {
        const u = 1 - t;
        const tt = t * t;
        const uu = u * u;
        const uuu = uu * u;
        const ttt = tt * t;
        return {
            x: uuu * p0.x + 3 * uu * t * p1.x + 3 * u * tt * p2.x + ttt * p3.x,
            y: uuu * p0.y + 3 * uu * t * p1.y + 3 * u * tt * p2.y + ttt * p3.y,
        };
    }

    function cubicBezierTangent(p0, p1, p2, p3, t) {
        const u = 1 - t;
        return {
            x: 3 * u * u * (p1.x - p0.x) + 6 * u * t * (p2.x - p1.x) + 3 * t * t * (p3.x - p2.x),
            y: 3 * u * u * (p1.y - p0.y) + 6 * u * t * (p2.y - p1.y) + 3 * t * t * (p3.y - p2.y),
        };
    }

    /**
     * Shared viewport-responsive airport & runway coordinates (20-25% bottom-left footprint).
     * Both the airport canvas and the 3D airplane / fallback consume these exact points
     * so the wheels track the runway centerline pixel-for-pixel across all devices.
     */
    function getRunwayLayout(vw, vh) {
        let runwayOrigin;
        let runwayEnd;
        let terminalAnchor;

        if (vw >= 1024) {
            runwayOrigin = { x: vw * 0.028, y: vh * 0.928 };
            runwayEnd = { x: vw * 0.305, y: vh * 0.778 };
            terminalAnchor = { x: vw * 0.032, y: vh * 0.812 };
        } else if (vw >= 768) {
            runwayOrigin = { x: vw * 0.025, y: vh * 0.912 };
            runwayEnd = { x: vw * 0.385, y: vh * 0.768 };
            terminalAnchor = { x: vw * 0.030, y: vh * 0.798 };
        } else {
            // Mobile: keep bottom ~14% clear for centered tagline & progress bar
            runwayOrigin = { x: vw * 0.022, y: vh * 0.855 };
            runwayEnd = { x: vw * 0.525, y: vh * 0.735 };
            terminalAnchor = { x: vw * 0.028, y: vh * 0.745 };
        }

        const startPt = lerpPt(runwayOrigin, runwayEnd, 0.14);
        const liftoffPt = lerpPt(runwayOrigin, runwayEnd, 0.82);
        const runwayDx = liftoffPt.x - startPt.x;
        const runwayDy = liftoffPt.y - startPt.y;
        // Positive angle in radians (counter-clockwise upward-right from +X axis)
        const runwayAngle = Math.atan2(-runwayDy, runwayDx);

        return {
            runwayOrigin,
            runwayEnd,
            terminalAnchor,
            startPt,
            liftoffPt,
            runwayDx,
            runwayDy,
            runwayAngle,
        };
    }

    // -------------------------------------------------------------------------
    // 1. Multi-Layer Clouds & Minimal Bottom-Left Airport Scene
    // -------------------------------------------------------------------------
    const cloudController = createCloudLayerSystem(cloudsCanvas);
    const airportController = createAirportSceneRenderer(airportCanvas);

    // -------------------------------------------------------------------------
    // 2. Dynamic Script Loader (Non-blocking, short timeout, graceful fallback)
    // -------------------------------------------------------------------------
    function loadScript(src, timeoutMs) {
        return new Promise((resolve, reject) => {
            const existing = document.querySelector(`script[src="${src}"]`);
            if (existing && existing.dataset.loaded === '1') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.crossOrigin = 'anonymous';

            let settled = false;
            const timer = window.setTimeout(() => {
                if (!settled) {
                    settled = true;
                    reject(new Error(`Timeout loading ${src}`));
                }
            }, timeoutMs);

            script.onload = () => {
                if (!settled) {
                    settled = true;
                    window.clearTimeout(timer);
                    script.dataset.loaded = '1';
                    resolve();
                }
            };

            script.onerror = () => {
                if (!settled) {
                    settled = true;
                    window.clearTimeout(timer);
                    reject(new Error(`Failed loading ${src}`));
                }
            };

            document.head.appendChild(script);
        });
    }

    function hasWebGLSupport() {
        if (!webglCanvas) {
            return false;
        }
        try {
            const opts = { alpha: true, antialias: true, powerPreference: 'high-performance' };
            return Boolean(webglCanvas.getContext('webgl2', opts) || webglCanvas.getContext('webgl', opts));
        } catch (err) {
            return false;
        }
    }

    // -------------------------------------------------------------------------
    // 3. Continuous Bottom-Left Runway Takeoff -> Top-Right Sky Flight Kinematics
    // -------------------------------------------------------------------------
    /**
     * Timeline (minimum 10 seconds):
     *   0.0 - 0.2 (0-2s): Airport & airliner visible; engines spool up, brake release
     *   0.2 - 0.4 (2-4s): Accelerates along bottom-left runway; nose lifts before liftoff (3.3-4s)
     *   0.4 - 0.6 (4-6s): Physically leaves runway, retracts gear, climbs diagonally bottom-left -> top-right
     *   0.6 - 1.0 (6-10s): Continues climbing smoothly towards top-right, receding in depth until full exit
     */
    function sampleFlightState(tRaw, nowSec, vw, vh) {
        const t = clamp01(tRaw);
        const layout = getRunwayLayout(vw, vh);

        let screenPt;
        let climbAngle;
        let groundY = layout.startPt.y;
        let altitudePx = 0;

        if (t <= 0.4) {
            // Phase 1 & 2 (0–4s): On runway
            let sRunway = 0;
            if (t <= 0.2) {
                // 0–2s: Poised at threshold, subtle brake-release creep in last 0.4s
                sRunway = 0.03 * smoothstep(0.15, 0.2, t);
            } else {
                // 2–4s: Smooth accelerating takeoff roll along runway
                const u = (t - 0.2) / 0.2;
                sRunway = 0.03 + 0.97 * Math.pow(u, 1.62);
            }

            screenPt = lerpPt(layout.startPt, layout.liftoffPt, sRunway);
            groundY = screenPt.y;

            // Nose rotation (Vr) right before leaving the runway (3.3s -> 4.0s)
            const rotationProgress = smoothstep(0.33, 0.4, t);
            const noseLiftAngle = rotationProgress * 0.21; // ~12 deg nose-up rotation
            climbAngle = layout.runwayAngle + noseLiftAngle;
        } else {
            // Phase 3 & 4 (4–10s): C1-continuous liftoff & diagonal climb bottom-left -> top-right
            const u = (t - 0.4) / 0.6;

            // Match exact runway exit velocity at t = 0.40 for seamless zero-jump transition
            const exitSpeedInT = 0.97 * 1.62 / 0.2; // 7.857
            const exitSpeedInU = exitSpeedInT * 0.6; // 4.714

            const p0 = layout.liftoffPt;
            const p1 = {
                x: p0.x + (layout.runwayDx * exitSpeedInU) / 3,
                y: p0.y + (layout.runwayDy * exitSpeedInU) / 3,
            };
            const p2 = {
                x: vw * 0.84,
                y: vh * 0.24,
            };
            const p3 = {
                x: vw * 1.18,
                y: -vh * 0.16,
            };

            screenPt = cubicBezierPt(p0, p1, p2, p3, u);
            const tang = cubicBezierTangent(p0, p1, p2, p3, u);
            const pathSlopeAngle = Math.atan2(-tang.y, tang.x);

            // Blend smoothly from rotated takeoff attitude into climb angle of attack
            const takeoffAttitude = layout.runwayAngle + 0.21;
            const airborneAngle = pathSlopeAngle + 0.06;
            climbAngle = lerp(takeoffAttitude, airborneAngle, smoothstep(0.0, 0.22, u));

            // Ground plane projection for shadow fade-out during climb
            const groundLineT = clamp01((screenPt.x - layout.startPt.x) / Math.max(1, layout.runwayDx));
            groundY = lerp(layout.startPt.y, layout.liftoffPt.y, groundLineT);
            altitudePx = Math.max(0, groundY - screenPt.y);
        }

        const ndcX = (screenPt.x / Math.max(1, vw)) * 2 - 1;
        const ndcY = 1 - (screenPt.y / Math.max(1, vh)) * 2;

        // Subtle perspective depth recession into the sky after liftoff
        const airProgress = clamp01((t - 0.4) / 0.6);
        const depthZ = lerp(0.0, -0.45, clamp01(t / 0.4)) + lerp(0.0, -6.2, Math.pow(airProgress, 1.08));
        const depthScale = lerp(1.0, 0.96, clamp01(t / 0.4)) * lerp(1.0, 0.56, Math.pow(airProgress, 1.05));

        // Subtle landing gear retraction between 4.4s and 5.8s
        const gearDeploy = 1 - smoothstep(0.44, 0.58, t);

        // Gentle aerodynamic banking roll once airborne + subtle live flight trim
        const airborneTrim = smoothstep(0.4, 0.55, t);
        const microRoll = Math.sin(nowSec * 1.5) * 0.022 * airborneTrim;
        const microPitch = Math.cos(nowSec * 1.2) * 0.012 * airborneTrim;
        const runwayVibration = (t > 0.22 && t < 0.4) ? Math.sin(nowSec * 36) * 0.004 : 0;

        const bankRoll = lerp(0, 0.26, smoothstep(0.42, 0.68, t)) + microRoll;
        const pitchAngle = climbAngle + microPitch + runwayVibration;

        // Turbofan spool-up: idle (6) -> takeoff thrust (34)
        const fanSpeed = t < 0.2
            ? lerp(7, 18, smoothstep(0.02, 0.2, t))
            : lerp(18, 35, smoothstep(0.2, 0.34, t));

        // Ground shadow opacity & spread directly under aircraft on runway
        const shadowAlpha = t < 0.4
            ? 0.26
            : Math.max(0, 0.26 * (1 - smoothstep(0.4, 0.53, t)));

        const opacity = t > 0.96 ? 1 - smoothstep(0.96, 1.0, t) * 0.3 : 1;

        return {
            t,
            screenX: screenPt.x,
            screenY: screenPt.y,
            groundY,
            altitudePx,
            shadowAlpha,
            ndcX,
            ndcY,
            depthZ,
            depthScale,
            pitchAngle,
            bankRoll,
            gearDeploy,
            fanSpeed,
            opacity,
        };
    }

    // -------------------------------------------------------------------------
    // 4. Aircraft Renderer Initialization (Three.js 3D -> 2.5D SVG Fallback)
    // -------------------------------------------------------------------------
    let aircraftRenderer = null;

    function activateFallbackRenderer() {
        if (webglCanvas) {
            webglCanvas.style.display = 'none';
        }
        if (fallbackStage && fallbackPlane) {
            fallbackStage.hidden = false;
            aircraftRenderer = createFallbackPlaneRenderer(fallbackPlane, fallbackGear);
        }
    }

    if (hasWebGLSupport()) {
        loadScript(THREE_CDN, 3200)
            .then(() => {
                if (isDisposed || !window.THREE) {
                    activateFallbackRenderer();
                    return;
                }
                try {
                    aircraftRenderer = createThreeAirlinerRenderer(webglCanvas);
                } catch (err) {
                    activateFallbackRenderer();
                }
            })
            .catch(() => {
                if (!isDisposed) {
                    activateFallbackRenderer();
                }
            });
    } else {
        activateFallbackRenderer();
    }

    // Optional GSAP enhancement for smooth UI reveal (non-blocking)
    loadScript(GSAP_CDN, 2800)
        .then(() => {
            if (!isDisposed && window.gsap) {
                const brand = document.getElementById('omLoaderBrand');
                const bottom = document.getElementById('omLoaderBottom');
                if (brand && bottom) {
                    window.gsap.fromTo(
                        [brand, bottom],
                        { autoAlpha: 0.98 },
                        { autoAlpha: 1, duration: 0.6, ease: 'power2.out' }
                    );
                }
            }
        })
        .catch(() => {
            // Pure CSS + RAF timeline handles everything seamlessly without GSAP
        });

    // Hard safety net: never trap user if resources stall far beyond load
    const emergencyTimer = window.setTimeout(() => {
        if (!hasExited && resourcesReady) {
            beginExitTransition();
        }
    }, 26000);

    // -------------------------------------------------------------------------
    // 5. Master 60 FPS Animation Loop
    // -------------------------------------------------------------------------
    let lastFrameTime = performance.now();

    function tick(now) {
        if (isDisposed) {
            return;
        }

        const dt = Math.min((now - lastFrameTime) / 1000, 0.05);
        lastFrameTime = now;
        const elapsedMs = now - startTime;
        const timelineProgress = Math.min(elapsedMs / MIN_DURATION_MS, 1);
        const nowSec = now * 0.001;

        const vw = window.innerWidth;
        const vh = window.innerHeight;
        const flight = sampleFlightState(timelineProgress, nowSec, vw, vh);

        // Update soft sky clouds (drifting opposite the bottom-left -> top-right flight)
        if (cloudController) {
            cloudController.update(dt, nowSec);
        }

        // Update bottom-left airport, runway lights, and real-time ground shadow
        if (airportController) {
            airportController.update(nowSec, flight);
        }

        // Update 3D or fallback airliner
        if (aircraftRenderer) {
            aircraftRenderer.update(flight, dt, nowSec);
        }

        // Update thin loading progress bar
        if (progressFill) {
            let barRatio;
            if (timelineProgress < 1) {
                barRatio = 0.92 * (1 - Math.pow(1 - timelineProgress, 1.32));
            } else if (!resourcesReady) {
                const extraSec = (elapsedMs - MIN_DURATION_MS) / 1000;
                barRatio = 0.92 + 0.05 * (1 - Math.exp(-extraSec * 0.35));
            } else {
                barRatio = 1;
            }
            progressFill.style.transform = `scaleX(${barRatio.toFixed(4)})`;
        }

        // Exit only after BOTH >= 10 seconds AND essential page resources are ready
        if (!hasExited && elapsedMs >= MIN_DURATION_MS && resourcesReady) {
            beginExitTransition();
            return;
        }

        rafId = window.requestAnimationFrame(tick);
    }

    function beginExitTransition() {
        if (hasExited) {
            return;
        }
        hasExited = true;
        window.clearTimeout(emergencyTimer);

        if (progressFill) {
            progressFill.style.transform = 'scaleX(1)';
        }

        markSessionPlayed();

        root.classList.add('om-intro-revealing');
        root.classList.remove('om-intro-active');

        window.setTimeout(() => {
            window.dispatchEvent(new CustomEvent('om:intro-complete'));
        }, 180);

        window.setTimeout(() => {
            isDisposed = true;
            if (rafId) {
                window.cancelAnimationFrame(rafId);
            }
            if (aircraftRenderer && typeof aircraftRenderer.dispose === 'function') {
                aircraftRenderer.dispose();
            }
            if (airportController && typeof airportController.dispose === 'function') {
                airportController.dispose();
            }
            if (cloudController && typeof cloudController.dispose === 'function') {
                cloudController.dispose();
            }
            root.classList.remove('om-intro-revealing');
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
        }, 980);
    }

    // If WebGL hasn't initialized within 900ms, switch cleanly to 2.5D SVG fallback
    window.setTimeout(() => {
        if (!isDisposed && !aircraftRenderer) {
            activateFallbackRenderer();
        }
    }, 900);

    rafId = window.requestAnimationFrame(tick);

    // =========================================================================
    // Cloud Layer Renderer (Offscreen-Cached Realistic Soft Clouds on Canvas 2D)
    // =========================================================================
    function createCloudLayerSystem(canvas) {
        if (!canvas) {
            return null;
        }
        const ctx = canvas.getContext('2d', { alpha: true });
        if (!ctx) {
            return null;
        }

        let width = window.innerWidth;
        let height = window.innerHeight;
        let dpr = Math.min(window.devicePixelRatio || 1, 1.5);

        const resize = () => {
            width = window.innerWidth;
            height = window.innerHeight;
            dpr = Math.min(window.devicePixelRatio || 1, 1.5);
            canvas.width = Math.round(width * dpr);
            canvas.height = Math.round(height * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        };

        resize();
        window.addEventListener('resize', resize, { passive: true });

        const sprites = [
            buildCloudSprite(360, 190, 14, 0.92),
            buildCloudSprite(420, 210, 16, 0.88),
            buildCloudSprite(300, 160, 12, 0.85),
            buildCloudSprite(460, 220, 18, 0.82),
            buildCloudSprite(340, 175, 13, 0.90),
        ];

        // Gentle drift opposite bottom-left -> top-right flight (top-right -> bottom-left)
        const layers = [
            { count: 6, speedY: 6.5, speedX: -9.5, scaleMin: 0.65, scaleMax: 1.05, alphaMin: 0.22, alphaMax: 0.34 },
            { count: 7, speedY: 13.0, speedX: -18.5, scaleMin: 0.75, scaleMax: 1.25, alphaMin: 0.36, alphaMax: 0.54 },
            { count: 4, speedY: 20.0, speedX: -28.0, scaleMin: 0.95, scaleMax: 1.40, alphaMin: 0.14, alphaMax: 0.24 },
        ];

        const clouds = [];
        layers.forEach((layer, layerIdx) => {
            for (let i = 0; i < layer.count; i++) {
                const sprite = sprites[(i + layerIdx * 2) % sprites.length];
                const scale = lerp(layer.scaleMin, layer.scaleMax, ((i * 37 + layerIdx * 19) % 100) / 100);
                const respScale = Math.max(0.55, Math.min(1.15, width / 1280));
                const drawW = sprite.width * scale * respScale;
                const drawH = sprite.height * scale * respScale;

                clouds.push({
                    sprite,
                    x: ((i + 0.35) / layer.count) * (width + drawW * 0.6) - drawW * 0.3,
                    // Keep clouds concentrated in upper 78% so bottom-left airport remains clean & natural
                    y: (((i * 0.618 + layerIdx * 0.27) % 1) * (height * 0.78)) - drawH * 0.4,
                    w: drawW,
                    h: drawH,
                    speedY: layer.speedY * (0.9 + (i % 3) * 0.1),
                    speedX: layer.speedX * (0.9 + (i % 3) * 0.1),
                    alpha: lerp(layer.alphaMin, layer.alphaMax, (i % 4) / 3),
                });
            }
        });

        return {
            update(dt) {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < clouds.length; i++) {
                    const c = clouds[i];
                    c.y += c.speedY * dt;
                    c.x += c.speedX * dt;

                    if (c.y > height * 0.82) {
                        c.y = -c.h * 0.85;
                        c.x = width * 0.25 + ((c.x + width) % (width * 0.85));
                    }
                    if (c.x < -c.w * 0.95) {
                        c.x = width + c.w * 0.2;
                        c.y = ((i * 0.37) % 0.65) * height - c.h * 0.3;
                    }

                    ctx.globalAlpha = c.alpha;
                    ctx.drawImage(c.sprite, c.x, c.y, c.w, c.h);
                }
                ctx.globalAlpha = 1;
            },
            dispose() {
                window.removeEventListener('resize', resize);
            },
        };
    }

    function buildCloudSprite(w, h, puffCount, density) {
        const off = document.createElement('canvas');
        off.width = w;
        off.height = h;
        const c = off.getContext('2d');

        for (let i = 0; i < Math.floor(puffCount * 0.5); i++) {
            const t = i / Math.max(1, Math.floor(puffCount * 0.5) - 1);
            const cx = lerp(w * 0.22, w * 0.78, t) + Math.sin(i * 2.1) * 14;
            const cy = h * 0.60 + Math.cos(i * 1.7) * 8;
            const rx = lerp(w * 0.14, w * 0.23, Math.sin(t * Math.PI));

            const grad = c.createRadialGradient(cx, cy, rx * 0.05, cx, cy, rx);
            grad.addColorStop(0, `rgba(212, 232, 246, ${0.42 * density})`);
            grad.addColorStop(0.55, `rgba(224, 240, 250, ${0.18 * density})`);
            grad.addColorStop(1, 'rgba(224, 240, 250, 0)');
            c.fillStyle = grad;
            c.beginPath();
            c.arc(cx, cy, rx, 0, Math.PI * 2);
            c.fill();
        }

        for (let i = 0; i < puffCount; i++) {
            const t = i / Math.max(1, puffCount - 1);
            const bell = Math.sin(t * Math.PI);
            const cx = lerp(w * 0.18, w * 0.82, t) + Math.cos(i * 3.1) * 12;
            const cy = lerp(h * 0.56, h * 0.34, bell) + Math.sin(i * 2.4) * 10;
            const rx = lerp(w * 0.11, w * 0.22, bell) * (0.85 + (i % 3) * 0.12);

            const grad = c.createRadialGradient(cx, cy - rx * 0.12, rx * 0.05, cx, cy, rx);
            grad.addColorStop(0, `rgba(255, 255, 255, ${0.88 * density})`);
            grad.addColorStop(0.42, `rgba(255, 255, 255, ${0.48 * density})`);
            grad.addColorStop(0.76, `rgba(246, 251, 255, ${0.14 * density})`);
            grad.addColorStop(1, 'rgba(246, 251, 255, 0)');
            c.fillStyle = grad;
            c.beginPath();
            c.arc(cx, cy, rx, 0, Math.PI * 2);
            c.fill();
        }

        return off;
    }

    // =========================================================================
    // Minimal Realistic Bottom-Left Airport Renderer (Terminal, Runway, Lights)
    // =========================================================================
    function createAirportSceneRenderer(canvas) {
        if (!canvas) {
            return null;
        }
        const ctx = canvas.getContext('2d', { alpha: true });
        if (!ctx) {
            return null;
        }

        let width = window.innerWidth;
        let height = window.innerHeight;
        let dpr = Math.min(window.devicePixelRatio || 1, 2);

        const resize = () => {
            width = window.innerWidth;
            height = window.innerHeight;
            dpr = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.round(width * dpr);
            canvas.height = Math.round(height * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        };

        resize();
        window.addEventListener('resize', resize, { passive: true });

        function drawGlowDot(x, y, radius, rgb, alpha) {
            const grad = ctx.createRadialGradient(x, y, 0, x, y, radius * 3.2);
            grad.addColorStop(0, `rgba(${rgb}, ${alpha})`);
            grad.addColorStop(0.35, `rgba(${rgb}, ${alpha * 0.55})`);
            grad.addColorStop(1, `rgba(${rgb}, 0)`);
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.arc(x, y, radius * 3.2, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = `rgba(255, 255, 255, ${Math.min(1, alpha * 1.15)})`;
            ctx.beginPath();
            ctx.arc(x, y, Math.max(0.9, radius * 0.42), 0, Math.PI * 2);
            ctx.fill();
        }

        return {
            update(nowSec, flight) {
                ctx.clearRect(0, 0, width, height);

                const layout = getRunwayLayout(width, height);
                const o = layout.runwayOrigin;
                const e = layout.runwayEnd;
                const term = layout.terminalAnchor;

                const dx = e.x - o.x;
                const dy = e.y - o.y;
                const len = Math.hypot(dx, dy) || 1;
                const ux = dx / len;
                const uy = dy / len;
                // Perpendicular upward-left unit normal to runway
                const nx = uy;
                const ny = -ux;

                const uiScale = Math.max(0.68, Math.min(1.12, width / 1366));

                // 1. Soft atmospheric horizon ground wedge blending cleanly into sky
                const groundSpanX = e.x + width * 0.12;
                const groundTopY = Math.min(e.y, term.y) - 34 * uiScale;
                const groundGrad = ctx.createRadialGradient(
                    o.x + dx * 0.35,
                    height * 0.94,
                    len * 0.1,
                    o.x + dx * 0.35,
                    height * 0.94,
                    groundSpanX * 0.92
                );
                groundGrad.addColorStop(0, 'rgba(173, 203, 220, 0.68)');
                groundGrad.addColorStop(0.55, 'rgba(198, 224, 238, 0.42)');
                groundGrad.addColorStop(0.85, 'rgba(225, 242, 250, 0.16)');
                groundGrad.addColorStop(1, 'rgba(235, 247, 254, 0)');

                ctx.fillStyle = groundGrad;
                ctx.beginPath();
                ctx.moveTo(0, height);
                ctx.lineTo(0, groundTopY + 26 * uiScale);
                ctx.quadraticCurveTo(
                    e.x * 0.55,
                    groundTopY - 8 * uiScale,
                    groundSpanX,
                    height
                );
                ctx.closePath();
                ctx.fill();

                // 2. Minimal Modern Airport Terminal & Air Traffic Control Tower (upper-left of runway)
                const tx = term.x;
                const ty = term.y;

                // Subtle apron tarmac pad connecting terminal to runway
                ctx.fillStyle = 'rgba(120, 146, 162, 0.32)';
                ctx.beginPath();
                ctx.moveTo(tx + 8 * uiScale, ty + 14 * uiScale);
                ctx.lineTo(tx + 122 * uiScale, ty + 14 * uiScale);
                ctx.lineTo(o.x + dx * 0.42, o.y + dy * 0.42);
                ctx.lineTo(o.x + dx * 0.16, o.y + dy * 0.16);
                ctx.closePath();
                ctx.fill();

                // Sleek Air Traffic Control Tower
                const towerX = tx + 16 * uiScale;
                const towerBaseY = ty + 8 * uiScale;
                const towerH = 44 * uiScale;

                ctx.fillStyle = 'rgba(72, 102, 118, 0.85)';
                ctx.beginPath();
                ctx.moveTo(towerX - 5 * uiScale, towerBaseY);
                ctx.lineTo(towerX - 3.2 * uiScale, towerBaseY - towerH);
                ctx.lineTo(towerX + 3.2 * uiScale, towerBaseY - towerH);
                ctx.lineTo(towerX + 5 * uiScale, towerBaseY);
                ctx.closePath();
                ctx.fill();

                // ATC observation cab
                ctx.fillStyle = 'rgba(42, 74, 88, 0.9)';
                ctx.beginPath();
                ctx.moveTo(towerX - 8.5 * uiScale, towerBaseY - towerH - 8 * uiScale);
                ctx.lineTo(towerX + 8.5 * uiScale, towerBaseY - towerH - 8 * uiScale);
                ctx.lineTo(towerX + 6 * uiScale, towerBaseY - towerH);
                ctx.lineTo(towerX - 6 * uiScale, towerBaseY - towerH);
                ctx.closePath();
                ctx.fill();

                // Illuminated ATC glass strip
                ctx.fillStyle = 'rgba(162, 224, 245, 0.85)';
                ctx.fillRect(
                    towerX - 6.5 * uiScale,
                    towerBaseY - towerH - 6.2 * uiScale,
                    13 * uiScale,
                    3.2 * uiScale
                );

                // ATC Roof & Antenna + Pulsing Aviation Beacon
                ctx.fillStyle = 'rgba(235, 245, 250, 0.9)';
                ctx.fillRect(towerX - 9.5 * uiScale, towerBaseY - towerH - 9.8 * uiScale, 19 * uiScale, 2 * uiScale);
                ctx.strokeStyle = 'rgba(72, 102, 118, 0.85)';
                ctx.lineWidth = 1.2;
                ctx.beginPath();
                ctx.moveTo(towerX, towerBaseY - towerH - 9.8 * uiScale);
                ctx.lineTo(towerX, towerBaseY - towerH - 18 * uiScale);
                ctx.stroke();

                const towerPulse = 0.45 + 0.55 * Math.pow(Math.max(0, Math.sin(nowSec * 2.6)), 6);
                drawGlowDot(towerX, towerBaseY - towerH - 18.5 * uiScale, 2.5 * uiScale, '255, 190, 110', towerPulse * 0.9);

                // Minimalist Curved-Roof Glass Terminal Concourse
                const concX = tx + 34 * uiScale;
                const concY = ty + 10 * uiScale;
                const concW = 92 * uiScale;
                const concH = 22 * uiScale;

                // Main facade
                const termGrad = ctx.createLinearGradient(concX, concY - concH, concX, concY);
                termGrad.addColorStop(0, 'rgba(65, 98, 115, 0.90)');
                termGrad.addColorStop(1, 'rgba(92, 128, 146, 0.86)');
                ctx.fillStyle = termGrad;
                ctx.beginPath();
                ctx.moveTo(concX, concY);
                ctx.lineTo(concX, concY - concH * 0.72);
                ctx.quadraticCurveTo(concX + concW * 0.5, concY - concH * 1.15, concX + concW, concY - concH * 0.68);
                ctx.lineTo(concX + concW, concY);
                ctx.closePath();
                ctx.fill();

                // Glass curtain wall with subtle warm terminal glow
                const glassGrad = ctx.createLinearGradient(concX, concY - concH * 0.7, concX + concW, concY);
                glassGrad.addColorStop(0, 'rgba(255, 224, 172, 0.42)');
                glassGrad.addColorStop(0.55, 'rgba(155, 218, 240, 0.58)');
                glassGrad.addColorStop(1, 'rgba(195, 234, 248, 0.45)');
                ctx.fillStyle = glassGrad;
                ctx.fillRect(concX + 6 * uiScale, concY - concH * 0.58, concW - 14 * uiScale, concH * 0.42);

                // Clean white cantilevered canopy roofline
                ctx.strokeStyle = 'rgba(250, 253, 255, 0.92)';
                ctx.lineWidth = Math.max(2, 2.6 * uiScale);
                ctx.lineCap = 'round';
                ctx.beginPath();
                ctx.moveTo(concX - 4 * uiScale, concY - concH * 0.72);
                ctx.quadraticCurveTo(concX + concW * 0.5, concY - concH * 1.18, concX + concW + 5 * uiScale, concY - concH * 0.68);
                ctx.stroke();

                // 3. Perspective Runway Strip (aligned exactly to takeoff vector)
                const halfWidthStart = 20 * uiScale;
                const halfWidthEnd = 9.5 * uiScale;

                const pStartTop = { x: o.x + nx * halfWidthStart, y: o.y + ny * halfWidthStart };
                const pStartBot = { x: o.x - nx * halfWidthStart, y: o.y - ny * halfWidthStart };
                const pEndTop = { x: e.x + nx * halfWidthEnd, y: e.y + ny * halfWidthEnd };
                const pEndBot = { x: e.x - nx * halfWidthEnd, y: e.y - ny * halfWidthEnd };

                const rwyGrad = ctx.createLinearGradient(o.x, o.y, e.x, e.y);
                rwyGrad.addColorStop(0, 'rgba(54, 73, 86, 0.92)');
                rwyGrad.addColorStop(0.65, 'rgba(78, 100, 114, 0.85)');
                rwyGrad.addColorStop(0.92, 'rgba(116, 142, 158, 0.52)');
                rwyGrad.addColorStop(1, 'rgba(182, 212, 228, 0.08)');

                ctx.fillStyle = rwyGrad;
                ctx.beginPath();
                ctx.moveTo(pStartTop.x, pStartTop.y);
                ctx.lineTo(pEndTop.x, pEndTop.y);
                ctx.lineTo(pEndBot.x, pEndBot.y);
                ctx.lineTo(pStartBot.x, pStartBot.y);
                ctx.closePath();
                ctx.fill();

                // Runway edge lines
                ctx.strokeStyle = 'rgba(242, 249, 253, 0.42)';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(pStartTop.x, pStartTop.y);
                ctx.lineTo(pEndTop.x, pEndTop.y);
                ctx.moveTo(pStartBot.x, pStartBot.y);
                ctx.lineTo(pEndBot.x, pEndBot.y);
                ctx.stroke();

                // Threshold "piano-key" bars near bottom-left start
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.72)';
                ctx.lineWidth = Math.max(1.4, 2.0 * uiScale);
                const threshBase = lerpPt(o, e, 0.04);
                const threshTip = lerpPt(o, e, 0.085);
                for (let k = -2; k <= 2; k++) {
                    if (k === 0) {
                        continue;
                    }
                    const offset = k * 4.5 * uiScale;
                    ctx.beginPath();
                    ctx.moveTo(threshBase.x + nx * offset, threshBase.y + ny * offset);
                    ctx.lineTo(threshTip.x + nx * offset * 0.92, threshTip.y + ny * offset * 0.92);
                    ctx.stroke();
                }

                // Dashed perspective centerline markings
                const dashCount = 9;
                for (let d = 0; d < dashCount; d++) {
                    const t0 = 0.13 + d * 0.09;
                    const t1 = Math.min(0.95, t0 + 0.045);
                    const a = lerpPt(o, e, t0);
                    const b = lerpPt(o, e, t1);
                    const fade = 1 - t0 * 0.55;
                    ctx.strokeStyle = `rgba(255, 255, 255, ${0.72 * fade})`;
                    ctx.lineWidth = lerp(2.2, 1.0, t0) * uiScale;
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.stroke();
                }

                // 4. Sequenced Runway Edge Lights
                const lightCount = 10;
                const chaseIndex = (nowSec * 3.4) % lightCount;
                for (let i = 0; i <= lightCount; i++) {
                    const frac = i / lightCount;
                    const cPt = lerpPt(o, e, frac);
                    const hw = lerp(halfWidthStart, halfWidthEnd, frac) + 2.5 * uiScale;
                    const fade = 1 - frac * 0.35;
                    const pulseDist = Math.abs(i - chaseIndex);
                    const boost = pulseDist < 1.3 ? 1.25 : 0.85;
                    const r = lerp(2.2, 1.3, frac) * uiScale;

                    const lightColor = i === 0 ? '130, 235, 175' : '255, 224, 158';
                    drawGlowDot(cPt.x + nx * hw, cPt.y + ny * hw, r, lightColor, 0.72 * fade * boost);
                    drawGlowDot(cPt.x - nx * hw, cPt.y - ny * hw, r, lightColor, 0.72 * fade * boost);
                }

                // 5. Real-time Aircraft Ground Shadow on Runway (fades away after liftoff)
                if (flight && flight.shadowAlpha > 0.01) {
                    const shadowScale = Math.max(0.35, 1 - flight.altitudePx / 120);
                    const rx = 24 * uiScale * shadowScale;
                    const ry = 5.5 * uiScale * shadowScale;

                    ctx.save();
                    ctx.translate(flight.screenX, flight.groundY + 4 * uiScale);
                    ctx.rotate(-layout.runwayAngle);
                    const sGrad = ctx.createRadialGradient(0, 0, rx * 0.1, 0, 0, rx);
                    sGrad.addColorStop(0, `rgba(26, 46, 58, ${flight.shadowAlpha})`);
                    sGrad.addColorStop(0.65, `rgba(26, 46, 58, ${flight.shadowAlpha * 0.38})`);
                    sGrad.addColorStop(1, 'rgba(26, 46, 58, 0)');
                    ctx.fillStyle = sGrad;
                    ctx.scale(1, ry / rx);
                    ctx.beginPath();
                    ctx.arc(0, 0, rx, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.restore();
                }
            },
            dispose() {
                window.removeEventListener('resize', resize);
            },
        };
    }

    // =========================================================================
    // Three.js Procedural 3D Commercial Passenger Airliner Renderer
    // =========================================================================
    function createThreeAirlinerRenderer(canvas) {
        const THREE = window.THREE;
        const renderer = new THREE.WebGLRenderer({
            canvas,
            alpha: true,
            antialias: true,
            powerPreference: 'high-performance',
        });

        let width = window.innerWidth;
        let height = window.innerHeight;
        renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
        renderer.setSize(width, height, false);
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.08;

        const scene = new THREE.Scene();
        const FOV = 36;
        const CAM_Z = 12;
        const camera = new THREE.PerspectiveCamera(FOV, width / height, 0.1, 60);
        camera.position.set(0, 0, CAM_Z);

        const hemiLight = new THREE.HemisphereLight(0xffffff, 0x9ac6e2, 1.38);
        scene.add(hemiLight);

        const sunLight = new THREE.DirectionalLight(0xfffaf0, 1.85);
        sunLight.position.set(7, 11, 9);
        scene.add(sunLight);

        const rimLight = new THREE.DirectionalLight(0xd4eefc, 0.78);
        rimLight.position.set(-7, -2, -5);
        scene.add(rimLight);

        const { aircraftGroup, fans, gearParts, navBeacon } = buildCommercialAirlinerModel(THREE);

        // Outer rig controls position, scale, and screen-space climb pitch (Z rotation)
        const flightRig = new THREE.Group();
        // Orient local +Z nose along +X (right) and slightly away (-Z), local +Y up
        aircraftGroup.rotation.order = 'YXZ';
        aircraftGroup.rotation.y = Math.PI / 2 + 0.24;
        flightRig.add(aircraftGroup);
        scene.add(flightRig);

        const handleResize = () => {
            width = window.innerWidth;
            height = window.innerHeight;
            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
            renderer.setSize(width, height, false);
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
        };

        window.addEventListener('resize', handleResize, { passive: true });

        const getTargetScreenWidthRatio = () => {
            if (width >= 1024) {
                return 0.098; // Small, realistic 8–12% of screen width on desktop
            }
            if (width >= 768) {
                return 0.12; // Proportional on tablet
            }
            return 0.15; // Proportional on mobile
        };

        const fovRad = THREE.MathUtils.degToRad(FOV);

        return {
            update(flight, dt, nowSec) {
                const aspect = width / Math.max(1, height);
                const distFromCam = CAM_Z - flight.depthZ;
                const halfH = distFromCam * Math.tan(fovRad / 2);
                const halfW = halfH * aspect;

                // Keep fuselage ~10% of screen width at depth=0, shrinking smoothly with distance
                const baseHalfW = CAM_Z * Math.tan(fovRad / 2) * aspect;
                const baseWorldLen = baseHalfW * 2 * getTargetScreenWidthRatio();
                const finalScale = (baseWorldLen / 1.92) * flight.depthScale;
                flightRig.scale.setScalar(finalScale);

                // Offset perpendicular to runway while landing gear is extended so tires rest on runway
                const gearLiftWorld = 0.185 * finalScale * flight.gearDeploy;
                flightRig.position.set(
                    flight.ndcX * halfW - Math.sin(flight.pitchAngle) * gearLiftWorld,
                    flight.ndcY * halfH + Math.cos(flight.pitchAngle) * gearLiftWorld,
                    flight.depthZ
                );

                // Pitch along runway -> Vr rotation -> diagonal climb bottom-left -> top-right
                flightRig.rotation.z = flight.pitchAngle;

                // Subtle airborne banking roll around aircraft's own longitudinal (+Z) nose axis
                aircraftGroup.rotation.z = flight.bankRoll;

                // Retractable landing gear animation
                for (let i = 0; i < gearParts.length; i++) {
                    const g = gearParts[i];
                    g.group.visible = flight.gearDeploy > 0.03;
                    g.group.scale.y = Math.max(0.01, flight.gearDeploy);
                    g.group.rotation.x = (1 - flight.gearDeploy) * 1.15;
                }

                // Turbofan engine spool-up & continuous spin
                for (let i = 0; i < fans.length; i++) {
                    fans[i].rotation.z += dt * flight.fanSpeed;
                }

                // Subtle anti-collision beacon pulse
                if (navBeacon) {
                    const pulse = Math.pow(Math.max(0, Math.sin(nowSec * 3.8)), 14);
                    navBeacon.material.opacity = 0.25 + pulse * 0.75;
                }

                renderer.render(scene, camera);
            },
            dispose() {
                window.removeEventListener('resize', handleResize);
                scene.traverse((obj) => {
                    if (obj.geometry) {
                        obj.geometry.dispose();
                    }
                    if (obj.material) {
                        const mats = Array.isArray(obj.material) ? obj.material : [obj.material];
                        mats.forEach((m) => {
                            if (m.map) {
                                m.map.dispose();
                            }
                            m.dispose();
                        });
                    }
                });
                renderer.dispose();
            },
        };
    }

    function buildCommercialAirlinerModel(THREE) {
        const group = new THREE.Group();
        const fans = [];
        const gearParts = [];

        const fuselageTex = createAirlinerLiveryTexture(THREE);

        const bodyMat = new THREE.MeshPhysicalMaterial({
            map: fuselageTex,
            roughness: 0.25,
            metalness: 0.08,
            clearcoat: 0.78,
            clearcoatRoughness: 0.14,
        });

        const wingMat = new THREE.MeshPhysicalMaterial({
            color: 0xf4f8fc,
            roughness: 0.28,
            metalness: 0.12,
            clearcoat: 0.65,
            clearcoatRoughness: 0.18,
        });

        const accentMat = new THREE.MeshPhysicalMaterial({
            color: 0x189ec0,
            roughness: 0.28,
            metalness: 0.15,
            clearcoat: 0.7,
        });

        const navyMat = new THREE.MeshPhysicalMaterial({
            color: 0x113d48,
            roughness: 0.3,
            metalness: 0.15,
            clearcoat: 0.6,
        });

        const metalMat = new THREE.MeshStandardMaterial({
            color: 0xc8d5e0,
            roughness: 0.22,
            metalness: 0.82,
        });

        const darkFanMat = new THREE.MeshStandardMaterial({
            color: 0x1a2730,
            roughness: 0.5,
            metalness: 0.55,
        });

        // 1. Sleek Aerodynamic Fuselage (length ~ 1.92 along Z)
        const profile = [];
        const numPts = 42;
        for (let i = 0; i <= numPts; i++) {
            const u = i / numPts;
            const z = lerp(0.98, -0.94, u);
            let r;
            if (u < 0.18) {
                const k = u / 0.18;
                r = 0.095 * Math.pow(Math.sin((k * Math.PI) / 2), 0.72);
            } else if (u < 0.66) {
                r = 0.095;
            } else {
                const k = (u - 0.66) / 0.34;
                r = 0.095 * Math.pow(1 - Math.pow(k, 1.45), 0.78);
            }
            profile.push(new THREE.Vector2(Math.max(0.001, r), z));
        }

        const fuselageGeo = new THREE.LatheGeometry(profile, 36);
        fuselageGeo.rotateX(Math.PI / 2);
        fuselageGeo.scale(0.94, 1.04, 1.0);
        const fuselage = new THREE.Mesh(fuselageGeo, bodyMat);
        group.add(fuselage);

        // 2. Swept Supercritical Main Wings (Span = 2.0)
        const wingShape = new THREE.Shape();
        wingShape.moveTo(0.06, 0.24);
        wingShape.lineTo(0.98, -0.26);
        wingShape.lineTo(1.0, -0.34);
        wingShape.lineTo(0.06, -0.14);
        wingShape.closePath();

        const wingExtrudeOpts = {
            depth: 0.016,
            bevelEnabled: true,
            bevelThickness: 0.006,
            bevelSize: 0.008,
            bevelSegments: 2,
        };

        const rightWingGeo = new THREE.ExtrudeGeometry(wingShape, wingExtrudeOpts);
        rightWingGeo.rotateX(Math.PI / 2);
        const rightWing = new THREE.Mesh(rightWingGeo, wingMat);
        rightWing.position.set(0, -0.012, 0.04);
        rightWing.rotation.z = 0.075;
        group.add(rightWing);

        const leftWing = rightWing.clone();
        leftWing.scale.x = -1;
        leftWing.rotation.z = -0.075;
        group.add(leftWing);

        // Wingtip Sharklets
        const wingletShape = new THREE.Shape();
        wingletShape.moveTo(0, 0);
        wingletShape.lineTo(0.035, 0.085);
        wingletShape.lineTo(0.055, 0.085);
        wingletShape.lineTo(0.065, 0);
        wingletShape.closePath();

        const wingletGeo = new THREE.ExtrudeGeometry(wingletShape, { depth: 0.005, bevelEnabled: false });
        wingletGeo.rotateY(Math.PI / 2);

        const rightWinglet = new THREE.Mesh(wingletGeo, accentMat);
        rightWinglet.position.set(0.975, 0.054, -0.22);
        rightWinglet.rotation.z = -0.22;
        group.add(rightWinglet);

        const leftWinglet = rightWinglet.clone();
        leftWinglet.position.x = -0.975;
        leftWinglet.rotation.z = 0.22;
        group.add(leftWinglet);

        // 3. Twin Underwing High-Bypass Turbofan Engines
        [-0.33, 0.33].forEach((xPos) => {
            const engineGroup = new THREE.Group();
            engineGroup.position.set(xPos, -0.048, 0.14);

            const nacelleGeo = new THREE.CylinderGeometry(0.048, 0.042, 0.22, 24, 1, true);
            nacelleGeo.rotateX(Math.PI / 2);
            engineGroup.add(new THREE.Mesh(nacelleGeo, wingMat));

            const lipGeo = new THREE.TorusGeometry(0.047, 0.006, 12, 24);
            const lip = new THREE.Mesh(lipGeo, metalMat);
            lip.position.z = 0.11;
            engineGroup.add(lip);

            const fanDisk = new THREE.Mesh(new THREE.CircleGeometry(0.044, 20), darkFanMat);
            fanDisk.position.z = 0.092;
            engineGroup.add(fanDisk);

            const spinnerGeo = new THREE.ConeGeometry(0.014, 0.032, 16);
            spinnerGeo.rotateX(Math.PI / 2);
            const spinner = new THREE.Mesh(spinnerGeo, metalMat);
            spinner.position.z = 0.105;
            engineGroup.add(spinner);

            const nozzleGeo = new THREE.ConeGeometry(0.034, 0.07, 18);
            nozzleGeo.rotateX(-Math.PI / 2);
            const nozzle = new THREE.Mesh(nozzleGeo, darkFanMat);
            nozzle.position.z = -0.125;
            engineGroup.add(nozzle);

            const pylon = new THREE.Mesh(new THREE.BoxGeometry(0.016, 0.055, 0.14), wingMat);
            pylon.position.set(0, 0.045, -0.02);
            engineGroup.add(pylon);

            fans.push(fanDisk);
            group.add(engineGroup);
        });

        // 4. Swept Horizontal Tail Stabilizers
        const hTailShape = new THREE.Shape();
        hTailShape.moveTo(0.02, 0.06);
        hTailShape.lineTo(0.34, -0.13);
        hTailShape.lineTo(0.35, -0.18);
        hTailShape.lineTo(0.02, -0.09);
        hTailShape.closePath();

        const hTailGeo = new THREE.ExtrudeGeometry(hTailShape, {
            depth: 0.01,
            bevelEnabled: true,
            bevelThickness: 0.004,
            bevelSize: 0.004,
            bevelSegments: 2,
        });
        hTailGeo.rotateX(Math.PI / 2);

        const rightHTail = new THREE.Mesh(hTailGeo, wingMat);
        rightHTail.position.set(0, 0.018, -0.76);
        rightHTail.rotation.z = 0.08;
        group.add(rightHTail);

        const leftHTail = rightHTail.clone();
        leftHTail.scale.x = -1;
        leftHTail.rotation.z = -0.08;
        group.add(leftHTail);

        // 5. Tall Swept Vertical Tail Fin (OmTravel Navy & Cyan Livery)
        const vTailShape = new THREE.Shape();
        vTailShape.moveTo(0, 0);
        vTailShape.lineTo(0.24, 0.30);
        vTailShape.lineTo(0.35, 0.30);
        vTailShape.lineTo(0.22, 0);
        vTailShape.closePath();

        const vTailGeo = new THREE.ExtrudeGeometry(vTailShape, {
            depth: 0.014,
            bevelEnabled: true,
            bevelThickness: 0.004,
            bevelSize: 0.004,
            bevelSegments: 2,
        });
        vTailGeo.rotateY(Math.PI / 2);
        const vTail = new THREE.Mesh(vTailGeo, navyMat);
        vTail.position.set(-0.007, 0.035, -0.64);
        group.add(vTail);

        const vBandShape = new THREE.Shape();
        vBandShape.moveTo(0.08, 0.05);
        vBandShape.lineTo(0.24, 0.27);
        vBandShape.lineTo(0.29, 0.27);
        vBandShape.lineTo(0.14, 0.05);
        vBandShape.closePath();
        const vBandGeo = new THREE.ExtrudeGeometry(vBandShape, { depth: 0.016, bevelEnabled: false });
        vBandGeo.rotateY(Math.PI / 2);
        const vBand = new THREE.Mesh(vBandGeo, accentMat);
        vBand.position.set(-0.008, 0.035, -0.64);
        group.add(vBand);

        // 6. Retractable Tricycle Landing Gear (Nose + Twin Main Bogies)
        const gearConfigs = [
            { x: 0, y: -0.085, z: 0.62, len: 0.085, wheelRadius: 0.021 },
            { x: -0.14, y: -0.055, z: -0.03, len: 0.115, wheelRadius: 0.028 },
            { x: 0.14, y: -0.055, z: -0.03, len: 0.115, wheelRadius: 0.028 },
        ];

        gearConfigs.forEach((cfg) => {
            const gearGroup = new THREE.Group();
            gearGroup.position.set(cfg.x, cfg.y, cfg.z);

            const strut = new THREE.Mesh(
                new THREE.CylinderGeometry(0.008, 0.007, cfg.len, 10),
                metalMat
            );
            strut.position.y = -cfg.len * 0.5;
            gearGroup.add(strut);

            const wheelGeo = new THREE.CylinderGeometry(cfg.wheelRadius, cfg.wheelRadius, 0.016, 14);
            wheelGeo.rotateZ(Math.PI / 2);

            const leftWheel = new THREE.Mesh(wheelGeo, darkFanMat);
            leftWheel.position.set(-0.013, -cfg.len, 0);
            gearGroup.add(leftWheel);

            const rightWheel = new THREE.Mesh(wheelGeo, darkFanMat);
            rightWheel.position.set(0.013, -cfg.len, 0);
            gearGroup.add(rightWheel);

            gearParts.push({ group: gearGroup });
            group.add(gearGroup);
        });

        // 7. Navigation Wingtip Lights & Belly Beacon
        const portLight = new THREE.Mesh(
            new THREE.SphereGeometry(0.011, 8, 8),
            new THREE.MeshBasicMaterial({ color: 0xff5a5a })
        );
        portLight.position.set(-0.985, 0.048, -0.29);
        group.add(portLight);

        const stbdLight = new THREE.Mesh(
            new THREE.SphereGeometry(0.011, 8, 8),
            new THREE.MeshBasicMaterial({ color: 0x35e58a })
        );
        stbdLight.position.set(0.985, 0.048, -0.29);
        group.add(stbdLight);

        const navBeacon = new THREE.Mesh(
            new THREE.SphereGeometry(0.012, 8, 8),
            new THREE.MeshBasicMaterial({ color: 0xff6247, transparent: true, opacity: 0.7 })
        );
        navBeacon.position.set(0, -0.095, 0.08);
        group.add(navBeacon);

        return { aircraftGroup: group, fans, gearParts, navBeacon };
    }

    function createAirlinerLiveryTexture(THREE) {
        const texCanvas = document.createElement('canvas');
        texCanvas.width = 1024;
        texCanvas.height = 512;
        const ctx = texCanvas.getContext('2d');

        const grad = ctx.createLinearGradient(0, 0, texCanvas.width, 0);
        grad.addColorStop(0.0, '#D7E3EC');
        grad.addColorStop(0.24, '#F7FAFD');
        grad.addColorStop(0.5, '#FFFFFF');
        grad.addColorStop(0.76, '#F7FAFD');
        grad.addColorStop(1.0, '#D7E3EC');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, texCanvas.width, texCanvas.height);

        ctx.fillStyle = '#1CA8CB';
        ctx.fillRect(texCanvas.width * 0.255, texCanvas.height * 0.26, 5, texCanvas.height * 0.56);
        ctx.fillRect(texCanvas.width * 0.74, texCanvas.height * 0.26, 5, texCanvas.height * 0.56);

        ctx.fillStyle = '#1A3444';
        for (let y = texCanvas.height * 0.28; y < texCanvas.height * 0.78; y += 11) {
            ctx.beginPath();
            ctx.roundRect(texCanvas.width * 0.285, y, 6, 4.5, 2);
            ctx.roundRect(texCanvas.width * 0.71, y, 6, 4.5, 2);
            ctx.fill();
        }

        ctx.fillStyle = '#142733';
        ctx.beginPath();
        ctx.roundRect(texCanvas.width * 0.37, texCanvas.height * 0.865, texCanvas.width * 0.26, 15, 5);
        ctx.fill();

        const texture = new THREE.CanvasTexture(texCanvas);
        texture.colorSpace = THREE.SRGBColorSpace;
        texture.anisotropy = 4;
        return texture;
    }

    // =========================================================================
    // Fallback 2.5D Perspective SVG Airliner Renderer (Non-WebGL Devices)
    // =========================================================================
    function createFallbackPlaneRenderer(el, gearEl) {
        return {
            update(flight) {
                const rotZ = -flight.pitchAngle * (180 / Math.PI);
                const rotX = flight.bankRoll * 32;
                const scale = flight.depthScale;
                const gearLiftPx = 12 * scale * flight.gearDeploy;

                if (gearEl) {
                    gearEl.style.opacity = flight.gearDeploy.toFixed(2);
                }

                el.style.opacity = flight.opacity.toFixed(3);
                el.style.transform =
                    `translate3d(calc(-50% + ${flight.screenX.toFixed(1)}px), calc(-50% + ${(flight.screenY - gearLiftPx).toFixed(1)}px), 0) ` +
                    `rotateX(${rotX.toFixed(1)}deg) ` +
                    `rotateZ(${rotZ.toFixed(2)}deg) ` +
                    `scale(${scale.toFixed(3)})`;
            },
            dispose() {},
        };
    }
})();
