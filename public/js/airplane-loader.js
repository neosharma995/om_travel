/**
 * OmTravel — Photorealistic Red & White AS350 Three-Blade Tourist Helicopter Loader
 *
 * Loads the dedicated GLB model (/models/as350-tourist.glb) via Three.js GLTFLoader,
 * animates all three 120-degree main rotor blades and the tail rotor independently,
 * and drives the vertical helipad takeoff -> diagonal Himalayan mountain climb.
 */

(function () {
    'use strict';

    const STORAGE_KEY = 'omtravel_intro_played';
    const MIN_DURATION_MS = 10400;
    const LIFTOFF_T = 0.385; // ~4.0 seconds
    const EXIT_T = 0.96;     // ~9.98 seconds
    const BG_NAT_W = 1376;
    const BG_NAT_H = 768;
    const HELIPAD_U = 0.3015;
    const HELIPAD_V = 0.8700;
    const MODEL_URL = '/models/as350-tourist.glb';
    const THREE_CDN = 'https://cdn.jsdelivr.net/npm/three@0.147.0/build/three.min.js';
    const GLTF_LOADER_CDN = 'https://cdn.jsdelivr.net/npm/three@0.147.0/examples/js/loaders/GLTFLoader.js';
    const GSAP_CDN = 'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js';

    const root = document.documentElement;
    const overlay = document.getElementById('omIntroLoader');

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

    const sceneryImg = document.getElementById('omLoaderSceneryImg');
    const cloudsCanvas = document.getElementById('omLoaderCloudsCanvas');
    const airportCanvas = document.getElementById('omLoaderAirportCanvas');
    const rotorWashCanvas = document.getElementById('omLoaderRotorWashCanvas');
    const webglCanvas = document.getElementById('omLoaderWebglCanvas');
    const fallbackStage = document.getElementById('omLoaderFallbackStage');
    const fallbackHeli = document.getElementById('omLoaderHeliFallback');
    const fallbackMainRotor = document.getElementById('omFallbackMainRotor');
    const fallbackTailRotor = document.getElementById('omFallbackTailRotor');
    const progressFill = document.getElementById('omLoaderProgressFill');

    const startTime = performance.now();
    let resourcesReady = document.readyState === 'complete';
    let sceneryReady = !sceneryImg || sceneryImg.complete;
    let modelReady = false;
    let hasExited = false;
    let isDisposed = false;
    let rafId = null;

    if (!resourcesReady) {
        window.addEventListener('load', () => {
            resourcesReady = true;
        }, { once: true });
    }

    if (sceneryImg && !sceneryReady) {
        sceneryImg.addEventListener('load', () => {
            sceneryReady = true;
        }, { once: true });
        sceneryImg.addEventListener('error', () => {
            sceneryReady = true;
        }, { once: true });
    }

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
            // Ignore storage access restrictions
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
    // Math & Helipad Camera-Tracked Coordinate Mapper
    // -------------------------------------------------------------------------
    function clamp01(val) {
        return Math.max(0, Math.min(1, val));
    }

    function lerp(a, b, t) {
        return a + (b - a) * t;
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

    function getSceneLayout(t, vw, vh) {
        const natW = (sceneryImg && sceneryImg.naturalWidth) ? sceneryImg.naturalWidth : BG_NAT_W;
        const natH = (sceneryImg && sceneryImg.naturalHeight) ? sceneryImg.naturalHeight : BG_NAT_H;
        const isMobile = vw < 768;
        const basePosX = isMobile ? 0.18 : 0.0;
        const basePosY = 1.0;

        const coverScale = Math.max(vw / natW, vh / natH);
        const renW = natW * coverScale;
        const renH = natH * coverScale;
        const baseOffsetX = (vw - renW) * basePosX;
        const baseOffsetY = (vh - renH) * basePosY;

        const trackProg = smoothstep(LIFTOFF_T, EXIT_T, t);
        const camZoom = 1.0 + 0.048 * trackProg;
        const camShiftX = -vw * (isMobile ? 0.042 : 0.032) * trackProg;
        const camShiftY = -vh * 0.022 * trackProg;

        if (sceneryImg) {
            sceneryImg.style.transformOrigin = '0px 0px';
            sceneryImg.style.transform =
                `translate3d(${camShiftX.toFixed(2)}px, ${camShiftY.toFixed(2)}px, 0) scale(${camZoom.toFixed(5)})`;
        }

        const uvToScreen = (u, v) => ({
            x: (baseOffsetX + u * renW) * camZoom + camShiftX,
            y: (baseOffsetY + v * renH) * camZoom + camShiftY,
        });

        const helipadPt = uvToScreen(HELIPAD_U, HELIPAD_V);

        return {
            helipadPt,
            coverScale,
            isMobile,
        };
    }

    // -------------------------------------------------------------------------
    // 1. Drifting Clouds, Helipad Shadow & Rotor-Wash Dust System
    // -------------------------------------------------------------------------
    const cloudController = createCloudLayerSystem(cloudsCanvas);
    const shadowController = createHelipadShadowRenderer(airportCanvas);
    const rotorWashController = createRotorWashSystem(rotorWashCanvas);

    // -------------------------------------------------------------------------
    // 2. Dynamic Script Loader
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
    // 3. Helicopter Takeoff -> Forward Tilt -> Himalayan Mountain Flight State
    // -------------------------------------------------------------------------
    function sampleFlightState(tRaw, nowSec, vw, vh) {
        const t = clamp01(tRaw);
        const layout = getSceneLayout(t, vw, vh);
        const pad = layout.helipadPt;

        let mainRotorSpeed;
        if (t < 0.19) {
            mainRotorSpeed = lerp(0.9, 10.5, smoothstep(0.02, 0.19, t));
        } else if (t < LIFTOFF_T) {
            mainRotorSpeed = lerp(10.5, 34.0, smoothstep(0.19, LIFTOFF_T, t));
        } else {
            mainRotorSpeed = 35.0;
        }
        const tailRotorSpeed = mainRotorSpeed * 4.5;
        const rotorBlurFactor = smoothstep(0.18, 0.36, t);

        let screenX = pad.x;
        let screenY = pad.y;
        let forwardPitch = 0;
        let bankRoll = 0;
        let climbYaw = 0;

        const hoverHeightPx = Math.min(vh * 0.21, 165);

        if (t <= LIFTOFF_T) {
            const skidPreLift = smoothstep(LIFTOFF_T - 0.025, LIFTOFF_T, t) * 4.0;
            screenY = pad.y - skidPreLift;
            forwardPitch = -0.02 * smoothstep(LIFTOFF_T - 0.04, LIFTOFF_T, t);
        } else {
            const p0 = { x: pad.x, y: pad.y - 4.0 };
            const p1 = { x: pad.x + vw * 0.018, y: pad.y - hoverHeightPx * 1.18 };
            const p2 = { x: vw * (layout.isMobile ? 0.68 : 0.62), y: vh * 0.41 };
            const p3 = { x: vw * (layout.isMobile ? 0.98 : 0.94), y: vh * 0.20 };

            const airU = clamp01((t - LIFTOFF_T) / (EXIT_T - LIFTOFF_T));
            const pt = cubicBezierPt(p0, p1, p2, p3, airU);
            screenX = pt.x;
            screenY = pt.y;

            forwardPitch = lerp(0, 0.22, smoothstep(LIFTOFF_T + 0.04, LIFTOFF_T + 0.22, t))
                * (1 - smoothstep(0.75, 0.96, t) * 0.25);

            bankRoll = lerp(0, -0.18, smoothstep(LIFTOFF_T + 0.06, LIFTOFF_T + 0.30, t));
            climbYaw = lerp(0, 0.14, smoothstep(LIFTOFF_T + 0.05, LIFTOFF_T + 0.35, t));
        }

        const altitudePx = Math.max(0, pad.y - screenY);

        const vibAmp = (t > 0.12 && t < 0.48)
            ? 0.004 * smoothstep(0.12, 0.25, t) * (1 - smoothstep(0.38, 0.48, t))
            : 0;
        const vibY = Math.sin(nowSec * 42) * vibAmp * 160;
        const vibPitch = Math.cos(nowSec * 38) * vibAmp;

        const airTrim = smoothstep(LIFTOFF_T + 0.05, LIFTOFF_T + 0.2, t);
        const microRoll = Math.sin(nowSec * 1.55) * 0.018 * airTrim;
        const microPitch = Math.cos(nowSec * 1.25) * 0.012 * airTrim;

        const finalScreenY = screenY + vibY;
        const ndcX = (screenX / Math.max(1, vw)) * 2 - 1;
        const ndcY = 1 - (finalScreenY / Math.max(1, vh)) * 2;

        const distU = clamp01((t - LIFTOFF_T - 0.05) / (EXIT_T - LIFTOFF_T - 0.05));
        const depthZ = lerp(0.0, -14.5, Math.pow(distU, 1.15));
        const depthScale = lerp(1.0, 0.22, Math.pow(distU, 1.05));

        const shadowAlpha = Math.max(0, 0.44 * (1 - smoothstep(0, 145, altitudePx)));
        const downwashIntensity = smoothstep(0.20, 0.36, t) * (1 - smoothstep(0.45, 0.60, t));

        const hasDisappeared = t >= EXIT_T;
        const opacity = hasDisappeared ? 0 : (t > EXIT_T - 0.10 ? 1 - smoothstep(EXIT_T - 0.10, EXIT_T, t) : 1);

        return {
            t,
            helipadX: pad.x,
            helipadY: pad.y,
            screenX,
            screenY: finalScreenY,
            altitudePx,
            shadowAlpha,
            downwashIntensity,
            ndcX,
            ndcY,
            depthZ,
            depthScale,
            forwardPitch: forwardPitch + vibPitch + microPitch,
            bankRoll: bankRoll + microRoll,
            climbYaw,
            mainRotorSpeed,
            tailRotorSpeed,
            rotorBlurFactor,
            hasDisappeared,
            opacity,
        };
    }

    // -------------------------------------------------------------------------
    // 4. GLB Helicopter Renderer Initialization (Three.js GLTFLoader -> Fallback)
    // -------------------------------------------------------------------------
    let heliRenderer = null;

    function activateFallbackRenderer() {
        modelReady = true;
        if (webglCanvas) {
            webglCanvas.style.display = 'none';
        }
        if (fallbackStage && fallbackHeli) {
            fallbackStage.hidden = false;
            heliRenderer = createFallbackHeliRenderer(fallbackHeli, fallbackMainRotor, fallbackTailRotor);
        }
    }

    if (hasWebGLSupport()) {
        loadScript(THREE_CDN, 4500)
            .then(() => loadScript(GLTF_LOADER_CDN, 4500))
            .then(() => {
                if (isDisposed || !window.THREE || !window.THREE.GLTFLoader) {
                    activateFallbackRenderer();
                    return null;
                }
                return createGltfHelicopterRenderer(webglCanvas, MODEL_URL);
            })
            .then((rendererInstance) => {
                if (isDisposed) {
                    if (rendererInstance && rendererInstance.dispose) {
                        rendererInstance.dispose();
                    }
                    return;
                }
                if (rendererInstance) {
                    heliRenderer = rendererInstance;
                    modelReady = true;
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
            // Pure CSS + RAF handles timing smoothly without GSAP
        });

    const emergencyTimer = window.setTimeout(() => {
        if (!hasExited && resourcesReady && sceneryReady) {
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

        if (cloudController) {
            cloudController.update(dt, nowSec);
        }

        if (shadowController) {
            shadowController.update(flight, vw);
        }

        if (rotorWashController) {
            rotorWashController.update(dt, nowSec, flight, vw);
        }

        if (heliRenderer) {
            heliRenderer.update(flight, dt, nowSec);
        }

        if (progressFill) {
            let barRatio;
            if (timelineProgress < 1) {
                barRatio = 0.92 * (1 - Math.pow(1 - timelineProgress, 1.32));
            } else if (!resourcesReady || !sceneryReady || !modelReady) {
                const extraSec = (elapsedMs - MIN_DURATION_MS) / 1000;
                barRatio = 0.92 + 0.05 * (1 - Math.exp(-extraSec * 0.35));
            } else {
                barRatio = 1;
            }
            progressFill.style.transform = `scaleX(${barRatio.toFixed(4)})`;
        }

        if (!hasExited && elapsedMs >= MIN_DURATION_MS && resourcesReady && sceneryReady && modelReady) {
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
            if (heliRenderer && typeof heliRenderer.dispose === 'function') {
                heliRenderer.dispose();
            }
            if (rotorWashController && typeof rotorWashController.dispose === 'function') {
                rotorWashController.dispose();
            }
            if (shadowController && typeof shadowController.dispose === 'function') {
                shadowController.dispose();
            }
            if (cloudController && typeof cloudController.dispose === 'function') {
                cloudController.dispose();
            }
            root.classList.remove('om-intro-revealing');
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
        }, 980);
    }

    // If GLB + WebGL cannot initialize within 3.5s, fall back seamlessly so animation never stalls
    window.setTimeout(() => {
        if (!isDisposed && !heliRenderer) {
            activateFallbackRenderer();
        }
    }, 3500);

    rafId = window.requestAnimationFrame(tick);

    // =========================================================================
    // Subtle Drifting Sky Clouds
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
            buildCloudSprite(380, 175, 14, 0.80),
            buildCloudSprite(440, 195, 16, 0.76),
            buildCloudSprite(320, 150, 12, 0.72),
        ];

        const clouds = [];
        for (let i = 0; i < 8; i++) {
            const sprite = sprites[i % sprites.length];
            const scale = lerp(0.68, 1.28, ((i * 37) % 100) / 100);
            const respScale = Math.max(0.55, Math.min(1.15, width / 1280));
            const drawW = sprite.width * scale * respScale;
            const drawH = sprite.height * scale * respScale;

            clouds.push({
                sprite,
                x: ((i + 0.25) / 8) * (width + drawW * 0.5) - drawW * 0.25,
                y: (((i * 0.618) % 1) * (height * 0.44)) - drawH * 0.25,
                w: drawW,
                h: drawH,
                speedX: -lerp(6.5, 14.5, (i % 4) / 3),
                speedY: lerp(2.0, 5.0, (i % 3) / 2),
                alpha: lerp(0.14, 0.26, (i % 4) / 3),
            });
        }

        return {
            update(dt) {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < clouds.length; i++) {
                    const c = clouds[i];
                    c.x += c.speedX * dt;
                    c.y += c.speedY * dt;

                    if (c.y > height * 0.48) {
                        c.y = -c.h * 0.75;
                    }
                    if (c.x < -c.w * 0.95) {
                        c.x = width + c.w * 0.2;
                        c.y = ((i * 0.37) % 0.42) * height - c.h * 0.2;
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

        for (let i = 0; i < puffCount; i++) {
            const t = i / Math.max(1, puffCount - 1);
            const bell = Math.sin(t * Math.PI);
            const cx = lerp(w * 0.16, w * 0.84, t) + Math.cos(i * 3.1) * 14;
            const cy = lerp(h * 0.58, h * 0.38, bell) + Math.sin(i * 2.4) * 9;
            const rx = lerp(w * 0.12, w * 0.23, bell) * (0.85 + (i % 3) * 0.12);

            const grad = c.createRadialGradient(cx, cy - rx * 0.1, rx * 0.05, cx, cy, rx);
            grad.addColorStop(0, `rgba(255, 255, 255, ${0.86 * density})`);
            grad.addColorStop(0.45, `rgba(255, 255, 255, ${0.42 * density})`);
            grad.addColorStop(1, 'rgba(255, 255, 255, 0)');
            c.fillStyle = grad;
            c.beginPath();
            c.arc(cx, cy, rx, 0, Math.PI * 2);
            c.fill();
        }

        return off;
    }

    // =========================================================================
    // Real-Time Helipad Contact Shadow
    // =========================================================================
    function createHelipadShadowRenderer(canvas) {
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

        return {
            update(flight, vw) {
                ctx.clearRect(0, 0, width, height);

                if (!flight || flight.shadowAlpha <= 0.01) {
                    return;
                }

                const uiScale = Math.max(0.68, Math.min(1.25, vw / 1280));
                const spread = 1 + (flight.altitudePx / 160) * 0.55;
                const rx = 58 * uiScale * spread;
                const ry = 13 * uiScale * spread;

                const sx = lerp(flight.helipadX, flight.screenX, 0.35) + 10 * uiScale;
                const sy = flight.helipadY + 4 * uiScale;

                ctx.save();
                ctx.translate(sx, sy);
                const sGrad = ctx.createRadialGradient(0, 0, rx * 0.08, 0, 0, rx);
                sGrad.addColorStop(0, `rgba(10, 14, 16, ${flight.shadowAlpha})`);
                sGrad.addColorStop(0.55, `rgba(12, 16, 18, ${flight.shadowAlpha * 0.42})`);
                sGrad.addColorStop(1, 'rgba(12, 16, 18, 0)');
                ctx.fillStyle = sGrad;
                ctx.scale(1, ry / rx);
                ctx.beginPath();
                ctx.arc(0, 0, rx, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            },
            dispose() {
                window.removeEventListener('resize', resize);
            },
        };
    }

    // =========================================================================
    // Realistic Rotor-Wash Ground Dust (Clean Dark Helipad, No Grass Overlay)
    // =========================================================================
    function createRotorWashSystem(canvas) {
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

        const dustSprite = document.createElement('canvas');
        dustSprite.width = 64;
        dustSprite.height = 64;
        const dCtx = dustSprite.getContext('2d');
        const dGrad = dCtx.createRadialGradient(32, 32, 2, 32, 32, 31);
        dGrad.addColorStop(0.0, 'rgba(232, 224, 206, 0.85)');
        dGrad.addColorStop(0.45, 'rgba(224, 218, 202, 0.36)');
        dGrad.addColorStop(1.0, 'rgba(224, 218, 202, 0)');
        dCtx.fillStyle = dGrad;
        dCtx.fillRect(0, 0, 64, 64);

        const dustParticles = [];
        let spawnCarry = 0;

        return {
            update(dt, nowSec, flight, vw) {
                ctx.clearRect(0, 0, width, height);

                const uiScale = Math.max(0.66, Math.min(1.22, vw / 1280));
                const wash = flight.downwashIntensity;
                const padX = flight.helipadX;
                const padY = flight.helipadY;
                const padRx = 152 * uiScale;
                const padRy = 34 * uiScale;

                if (wash > 0.08) {
                    spawnCarry += dt * 44 * wash;
                    while (spawnCarry >= 1) {
                        spawnCarry -= 1;
                        const angle = Math.random() * Math.PI * 2;
                        const dirX = Math.cos(angle);
                        const dirY = Math.sin(angle);
                        const startR = 0.50 + Math.random() * 0.42;
                        const speed = (68 + Math.random() * 58) * uiScale * (0.55 + wash * 0.6);

                        dustParticles.push({
                            x: padX + dirX * padRx * startR,
                            y: padY + dirY * padRy * startR,
                            vx: dirX * speed,
                            vy: dirY * speed * 0.22 - (7 + Math.random() * 10) * uiScale,
                            age: 0,
                            life: 1.0 + Math.random() * 0.7,
                            rStart: (10 + Math.random() * 8) * uiScale,
                            rEnd: (28 + Math.random() * 18) * uiScale,
                            peakAlpha: 0.15 * wash,
                        });
                    }
                }

                for (let i = dustParticles.length - 1; i >= 0; i--) {
                    const p = dustParticles[i];
                    p.age += dt;
                    if (p.age >= p.life) {
                        dustParticles.splice(i, 1);
                        continue;
                    }

                    const frac = p.age / p.life;
                    const drag = Math.exp(-dt * 1.8);
                    p.vx *= drag;
                    p.x += p.vx * dt;
                    p.y += p.vy * dt;

                    const rx = lerp(p.rStart, p.rEnd, Math.pow(frac, 0.7));
                    const ry = rx * 0.44;
                    const alpha = p.peakAlpha * smoothstep(0, 0.14, frac) * (1 - Math.pow(frac, 1.3));

                    if (alpha > 0.002) {
                        ctx.globalAlpha = alpha;
                        ctx.drawImage(dustSprite, p.x - rx, p.y - ry, rx * 2, ry * 2);
                    }
                }
                ctx.globalAlpha = 1;
            },
            dispose() {
                dustParticles.length = 0;
                window.removeEventListener('resize', resize);
            },
        };
    }

    // =========================================================================
    // Three.js GLB Helicopter Renderer (Loads /models/as350-tourist.glb)
    // =========================================================================
    function applySRGB(THREE, obj, isRenderer) {
        if (isRenderer) {
            if ('outputColorSpace' in obj && THREE.SRGBColorSpace) {
                obj.outputColorSpace = THREE.SRGBColorSpace;
            } else if ('outputEncoding' in obj && THREE.sRGBEncoding) {
                obj.outputEncoding = THREE.sRGBEncoding;
            }
            return;
        }
        if ('colorSpace' in obj && THREE.SRGBColorSpace) {
            obj.colorSpace = THREE.SRGBColorSpace;
        } else if ('encoding' in obj && THREE.sRGBEncoding) {
            obj.encoding = THREE.sRGBEncoding;
        }
    }

    function createSkyEnvironmentTexture(THREE) {
        const c = document.createElement('canvas');
        c.width = 512;
        c.height = 256;
        const ctx = c.getContext('2d');

        const grad = ctx.createLinearGradient(0, 0, 0, c.height);
        grad.addColorStop(0.0, '#4E97D4');
        grad.addColorStop(0.35, '#9BD1F2');
        grad.addColorStop(0.49, '#F4F9FD');
        grad.addColorStop(0.54, '#839D6F');
        grad.addColorStop(0.75, '#4F6D45');
        grad.addColorStop(1.0, '#364A32');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, c.width, c.height);

        const sunGrad = ctx.createRadialGradient(130, 74, 4, 130, 74, 90);
        sunGrad.addColorStop(0, 'rgba(255, 250, 224, 1)');
        sunGrad.addColorStop(0.35, 'rgba(255, 240, 196, 0.55)');
        sunGrad.addColorStop(1, 'rgba(255, 240, 196, 0)');
        ctx.fillStyle = sunGrad;
        ctx.fillRect(0, 0, c.width, c.height * 0.55);

        const tex = new THREE.CanvasTexture(c);
        tex.mapping = THREE.EquirectangularReflectionMapping;
        applySRGB(THREE, tex, false);
        return tex;
    }

    function createGltfHelicopterRenderer(canvas, glbUrl) {
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
        applySRGB(THREE, renderer, true);

        const scene = new THREE.Scene();

        const pmrem = new THREE.PMREMGenerator(renderer);
        const envTexture = createSkyEnvironmentTexture(THREE);
        const envRT = pmrem.fromEquirectangular(envTexture);
        scene.environment = envRT.texture;
        envTexture.dispose();
        pmrem.dispose();

        const FOV = 35;
        const CAM_Z = 12;
        const camera = new THREE.PerspectiveCamera(FOV, width / height, 0.1, 80);
        camera.position.set(0, 0, CAM_Z);

        const hemiLight = new THREE.HemisphereLight(0xf6fbff, 0x5c724c, 1.15);
        scene.add(hemiLight);

        const sunLight = new THREE.DirectionalLight(0xfff4dc, 2.35);
        sunLight.position.set(-9, 10, 8);
        scene.add(sunLight);

        const rimLight = new THREE.DirectionalLight(0xcbe8fa, 0.8);
        rimLight.position.set(8, -2, -5);
        scene.add(rimLight);

        const loader = new THREE.GLTFLoader();

        return new Promise((resolve, reject) => {
            loader.load(
                glbUrl,
                (gltf) => {
                    const heliRoot = gltf.scene;
                    const mainRotor = heliRoot.getObjectByName('MainRotor');
                    const tailRotor = heliRoot.getObjectByName('TailRotor');
                    const trackedMaterials = [];

                    // Enhance PBR reflections & glass response on loaded GLB materials
                    heliRoot.traverse((node) => {
                        if (!node.isMesh || !node.material) {
                            return;
                        }
                        const mats = Array.isArray(node.material) ? node.material : [node.material];
                        mats.forEach((mat) => {
                            mat.envMapIntensity = mat.transparent ? 1.65 : 1.25;
                            mat.userData = mat.userData || {};
                            mat.userData.baseOpacity = mat.opacity !== undefined ? mat.opacity : 1;
                            mat.userData.baseTransparent = Boolean(mat.transparent);
                            trackedMaterials.push(mat);
                        });
                    });

                    // Add 3 subtle 3D geometric motion-blur blade echoes inside MainRotor (aligned to 0, +120, -120 deg)
                    const mainBlurMeshes = [];
                    if (mainRotor) {
                        const bladeAzimuths = [0, (2 * Math.PI) / 3, -(2 * Math.PI) / 3];
                        const trailOffsets = [
                            { angle: 0.12, opacity: 0.22, chord: 0.42 },
                            { angle: 0.28, opacity: 0.13, chord: 0.65 },
                            { angle: 0.48, opacity: 0.06, chord: 0.90 },
                        ];

                        bladeAzimuths.forEach((az) => {
                            trailOffsets.forEach((tr) => {
                                const bMat = new THREE.MeshBasicMaterial({
                                    color: 0x2a333c,
                                    transparent: true,
                                    opacity: 0,
                                    side: THREE.DoubleSide,
                                    depthWrite: false,
                                });
                                const bMesh = new THREE.Mesh(new THREE.PlaneGeometry(4.35, tr.chord), bMat);
                                bMesh.rotation.x = -Math.PI / 2;
                                bMesh.position.set(2.85, 0.14, 0);
                                bMesh.visible = false;

                                const pivot = new THREE.Group();
                                pivot.rotation.y = -az + tr.angle;
                                pivot.add(bMesh);
                                mainRotor.add(pivot);
                                mainBlurMeshes.push({ mesh: bMesh, baseOpacity: tr.opacity });
                            });
                        });
                    }

                    // Shift model slightly along Z so its fuselage center sits nicely over the helipad 'H'
                    heliRoot.position.set(0, 0, 1.5);

                    const flightRig = new THREE.Group();
                    const attitudeGroup = new THREE.Group();
                    attitudeGroup.add(heliRoot);
                    flightRig.add(attitudeGroup);
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
                            return 0.21;
                        }
                        if (width >= 768) {
                            return 0.23;
                        }
                        return 0.28;
                    };

                    const fovRad = THREE.MathUtils.degToRad(FOV);

                    resolve({
                        update(flight, dt) {
                            if (flight.hasDisappeared || flight.opacity <= 0.01) {
                                renderer.clear();
                                return;
                            }

                            const aspect = width / Math.max(1, height);
                            const distFromCam = CAM_Z - flight.depthZ;
                            const halfH = distFromCam * Math.tan(fovRad / 2);
                            const halfW = halfH * aspect;

                            // GLB full length is ~11.0m (rotor-to-tail span ~12.27m)
                            const baseHalfW = CAM_Z * Math.tan(fovRad / 2) * aspect;
                            const baseWorldSpan = baseHalfW * 2 * getTargetScreenWidthRatio();
                            const finalScale = (baseWorldSpan / 11.4) * flight.depthScale;
                            flightRig.scale.setScalar(finalScale);

                            // Skid bottom in GLB is at y = -1.107 -> offset upward so skids rest directly on helipad
                            const skidOffsetWorld = 1.107 * finalScale;
                            flightRig.position.set(
                                flight.ndcX * halfW,
                                flight.ndcY * halfH + skidOffsetWorld,
                                flight.depthZ
                            );

                            // 3/4 view matching reference helicopter profile heading right toward Himalayan peaks
                            flightRig.rotation.y = Math.PI / 2 + 0.34 + flight.climbYaw;

                            attitudeGroup.rotation.order = 'ZXY';
                            attitudeGroup.rotation.x = flight.forwardPitch;
                            attitudeGroup.rotation.z = flight.bankRoll;

                            // 1. Continuous 360-degree rotation of all THREE main rotor blades
                            if (mainRotor) {
                                mainRotor.rotation.y -= dt * flight.mainRotorSpeed;
                            }

                            for (let m = 0; m < mainBlurMeshes.length; m++) {
                                const mb = mainBlurMeshes[m];
                                mb.mesh.visible = flight.rotorBlurFactor > 0.02;
                                mb.mesh.material.opacity = mb.baseOpacity * flight.rotorBlurFactor;
                            }

                            // 2. Independent continuous rotation of tail rotor
                            if (tailRotor) {
                                tailRotor.rotation.x += dt * flight.tailRotorSpeed;
                            }

                            if (flight.opacity < 0.99) {
                                for (let i = 0; i < trackedMaterials.length; i++) {
                                    const mat = trackedMaterials[i];
                                    mat.transparent = true;
                                    mat.opacity = mat.userData.baseOpacity * flight.opacity;
                                }
                            }

                            renderer.render(scene, camera);
                        },
                        dispose() {
                            window.removeEventListener('resize', handleResize);
                            envRT.dispose();
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
                    });
                },
                undefined,
                (err) => {
                    renderer.dispose();
                    envRT.dispose();
                    reject(err || new Error('Failed to load GLB'));
                }
            );
        });
    }

    // =========================================================================
    // Non-WebGL 2.5D Perspective Red & White 3-Blade Helicopter Fallback
    // =========================================================================
    function createFallbackHeliRenderer(el, mainRotorEl, tailRotorEl) {
        let mainDeg = 0;
        let tailDeg = 0;

        return {
            update(flight, dt) {
                if (flight.hasDisappeared || flight.opacity <= 0.01) {
                    el.style.opacity = '0';
                    return;
                }

                mainDeg = (mainDeg + dt * flight.mainRotorSpeed * (180 / Math.PI)) % 360;
                tailDeg = (tailDeg + dt * flight.tailRotorSpeed * (180 / Math.PI)) % 360;

                if (mainRotorEl) {
                    mainRotorEl.setAttribute(
                        'transform',
                        `translate(183 31) scale(1 0.24) rotate(${mainDeg.toFixed(1)})`
                    );
                }

                if (tailRotorEl) {
                    tailRotorEl.setAttribute('transform', `translate(31 79) rotate(${tailDeg.toFixed(1)})`);
                }

                const tiltDeg = flight.forwardPitch * (180 / Math.PI);
                const scale = flight.depthScale;
                const elH = el.offsetHeight || 105;
                const skidOffsetPx = elH * 0.33 * scale;

                el.style.opacity = flight.opacity.toFixed(3);
                el.style.transform =
                    `translate3d(calc(-50% + ${flight.screenX.toFixed(1)}px), calc(-50% + ${(flight.screenY - skidOffsetPx).toFixed(1)}px), 0) ` +
                    `rotateZ(${tiltDeg.toFixed(2)}deg) ` +
                    `scale(${scale.toFixed(3)})`;
            },
            dispose() { },
        };
    }
})();
