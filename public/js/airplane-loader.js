/**
 * OmTravel — First Hero Slide Cinematic Infinite-Loop AS350 Helicopter Sequence
 *
 * Continuous seamless cycle inside the first hero slide:
 *   1. Take-off   (0.0s  -> 5.2s)  : Spool rotors on right helipad -> vertical liftoff -> hover
 *   2. Fly Away   (5.2s  -> 11.4s) : Accelerate smoothly diagonally LEFT -> exit completely off-screen left
 *   3. Wait Away  (11.4s -> 17.4s) : Stay off-screen ~6s while clouds/sky stay live
 *   4. Return     (17.4s -> 26.4s) : Re-enter from exact left exit side -> fly right -> decelerate to pad hover
 *   5. Landing    (26.4s -> 37.0s) : Slow 180-deg pedal-turn hover alignment -> slow vertical descent to touchdown
 *   6. Wait & Idle(37.0s -> 41.0s) : Slow rotors smoothly back to idle ~4.0s -> repeat seamlessly
 */

(function () {
    'use strict';

    // Phase boundary timestamps (ms)
    const SPOOL_END_MS = 2600;
    const TAKEOFF_END_MS = 5200;
    const FLY_AWAY_END_MS = 11400;
    const WAIT_AWAY_END_MS = 17400;  // 6.0s off-screen delay (within 5-7s)
    const RETURN_END_MS = 26400;     // 9 seconds return
    const HOVER_TURN_END_MS = 31000; // Slower 180-degree rotation
    const LANDING_END_MS = 37000;    // Slower vertical landing
    const TOTAL_CYCLE_MS = 41000;    // 4.0s ground wait before seamless repeat

    const BG_NAT_W = 1376;
    const BG_NAT_H = 768;
    const HELIPAD_U = 0.7420;
    const HELIPAD_V = 0.8900;

    // Facing headings around Y: BASE_YAW faces nose-left 3/4 climb; RETURN_YAW faces nose-right approach
    const BASE_YAW = -Math.PI / 2 - 0.34;
    const RETURN_YAW = Math.PI / 2 - 0.34;

    const MODEL_URL = '/models/as350-tourist.glb';
    const THREE_CDN = 'https://cdn.jsdelivr.net/npm/three@0.147.0/build/three.min.js';
    const GLTF_LOADER_CDN = 'https://cdn.jsdelivr.net/npm/three@0.147.0/examples/js/loaders/GLTFLoader.js';

    const overlay = document.getElementById('omIntroLoader');
    const heroSlide = overlay ? overlay.closest('.hero-slide') : null;

    if (!overlay || !heroSlide) {
        return;
    }

    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        overlay.style.display = 'none';
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
    const fallbackTailBeaconGlow = document.getElementById('omFallbackTailBeaconGlow');
    const fallbackTailBeaconCore = document.getElementById('omFallbackTailBeaconCore');

    const startTime = performance.now();
    let lastFrameTime = startTime;
    let isDisposed = false;
    let isLoopRunning = false;
    let rafId = null;

    function getContainerSize() {
        const w = Math.max(1, overlay.clientWidth || heroSlide.clientWidth || window.innerWidth);
        const h = Math.max(1, overlay.clientHeight || heroSlide.clientHeight || window.innerHeight);
        return { width: w, height: h };
    }

    // -------------------------------------------------------------------------
    // Math & Scene Coordinate Helpers
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

    function sampleBeaconPulse(nowSec) {
        const cycle = 1.35;
        const t = ((nowSec % cycle) + cycle) % cycle;
        const p1 = Math.exp(-Math.pow((t - 0.08) / 0.052, 2));
        const p2 = 0.90 * Math.exp(-Math.pow((t - 0.30) / 0.058, 2));
        return clamp01(0.08 + 0.92 * Math.max(p1, p2));
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

    function getSceneLayout(vw, vh) {
        const natW = (sceneryImg && sceneryImg.naturalWidth) ? sceneryImg.naturalWidth : BG_NAT_W;
        const natH = (sceneryImg && sceneryImg.naturalHeight) ? sceneryImg.naturalHeight : BG_NAT_H;
        const isMobile = vw <= 768;
        const basePosX = isMobile ? 0.88 : 1.0;
        const basePosY = 1.0;

        const coverScale = Math.max(vw / natW, vh / natH);
        const renW = natW * coverScale;
        const renH = natH * coverScale;
        const baseOffsetX = (vw - renW) * basePosX;
        const baseOffsetY = (vh - renH) * basePosY;

        if (sceneryImg) {
            sceneryImg.style.transform = 'none';
        }

        const helipadPt = {
            x: baseOffsetX + HELIPAD_U * renW,
            y: baseOffsetY + HELIPAD_V * renH,
        };

        const helipadEllipse = {
            cx: baseOffsetX + 0.747 * renW,
            cy: baseOffsetY + 0.909 * renH,
            rx: 0.195 * renW,
            ry: 0.041 * renH,
        };

        return {
            helipadPt,
            helipadEllipse,
            coverScale,
            isMobile,
        };
    }

    // -------------------------------------------------------------------------
    // 1. Drifting Clouds, Helipad Shadow & Rotor-Wash Dust System
    // -------------------------------------------------------------------------
    const cloudController = createCloudLayerSystem(cloudsCanvas, getContainerSize);
    const shadowController = createHelipadShadowRenderer(airportCanvas, getContainerSize);
    const rotorWashController = createRotorWashSystem(rotorWashCanvas, getContainerSize);

    // -------------------------------------------------------------------------
    // 2. Dynamic Script Loader
    // -------------------------------------------------------------------------
    function loadScript(src, timeoutMs) {
        return new Promise((resolve, reject) => {
            if (src === THREE_CDN && window.THREE) {
                resolve();
                return;
            }
            if (src === GLTF_LOADER_CDN && window.THREE && window.THREE.GLTFLoader) {
                resolve();
                return;
            }

            const existing = document.querySelector(`script[src="${src}"]`);
            if (existing && existing.dataset.loaded === '1') {
                resolve();
                return;
            }

            const script = existing || document.createElement('script');
            if (!existing) {
                script.src = src;
                script.async = true;
                script.crossOrigin = 'anonymous';
            }

            let settled = false;
            const timer = window.setTimeout(() => {
                if (!settled) {
                    settled = true;
                    reject(new Error(`Timeout loading ${src}`));
                }
            }, timeoutMs);

            const onLoad = () => {
                script.dataset.loaded = '1';
                if (!settled) {
                    settled = true;
                    window.clearTimeout(timer);
                    resolve();
                }
            };

            const onError = () => {
                if (!settled) {
                    settled = true;
                    window.clearTimeout(timer);
                    reject(new Error(`Failed loading ${src}`));
                }
            };

            script.addEventListener('load', onLoad, { once: true });
            script.addEventListener('error', onError, { once: true });

            if (!existing) {
                document.head.appendChild(script);
            }
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
    // 3. Complete Cinematic Helicopter Choreography Sampler
    // -------------------------------------------------------------------------
    function sampleFlightState(elapsedMs, nowSec, vw, vh) {
        const layout = getSceneLayout(vw, vh);
        const pad = layout.helipadPt;
        const cycleMs = elapsedMs % TOTAL_CYCLE_MS;

        const hoverHeightPx = Math.min(vh * 0.21, 160);
        const hoverY = pad.y - hoverHeightPx;
        const marginX = Math.max(240, vw * (layout.isMobile ? 0.34 : 0.24));
        const exitPt = { x: -marginX, y: vh * 0.19 };

        // Smooth right-to-left departure & left-to-right return Bezier control points
        const depP0 = { x: pad.x, y: hoverY };
        const depP1 = { x: pad.x - vw * 0.14, y: hoverY - vh * 0.045 };
        const depP2 = { x: vw * (layout.isMobile ? 0.24 : 0.30), y: vh * 0.35 };
        const depP3 = exitPt;

        const retR0 = exitPt;
        const retR1 = { x: vw * 0.30, y: vh * 0.26 };
        const retR2 = { x: pad.x - vw * 0.18, y: hoverY - vh * 0.055 };
        const retR3 = { x: pad.x, y: hoverY };

        let screenX = pad.x;
        let screenY = pad.y;
        let forwardPitch = 0;
        let bankRoll = 0;
        let yaw = BASE_YAW;
        let depthZ = 0;
        let depthScale = 1.0;
        let mainRotorSpeed = 1.0;
        let rotorBlurFactor = 0;
        let downwashIntensity = 0;
        let hasDisappeared = false;
        let phase = 'spool';

        if (cycleMs < SPOOL_END_MS) {
            // Phase 1A: On helipad, rotor smoothly spools from slow idle (1.0) to full flight RPM (34.0)
            phase = 'takeoff_spool';
            const u = clamp01(cycleMs / SPOOL_END_MS);
            mainRotorSpeed = lerp(1.0, 34.0, Math.pow(smoothstep(0.05, 1.0, u), 1.35));
            rotorBlurFactor = smoothstep(0.38, 0.92, u) * 0.9;
            downwashIntensity = smoothstep(0.35, 1.0, u) * 0.85;

            const skidPreLift = smoothstep(0.84, 1.0, u) * 3.5;
            screenY = pad.y - skidPreLift;
            forwardPitch = -0.02 * smoothstep(0.70, 1.0, u);
        } else if (cycleMs < TAKEOFF_END_MS) {
            // Phase 1B: Vertical liftoff from pad -> gentle pre-departure hover
            phase = 'takeoff_hover';
            const u = clamp01((cycleMs - SPOOL_END_MS) / (TAKEOFF_END_MS - SPOOL_END_MS));
            const climbU = smoothstep(0.0, 0.72, u);
            const hoverSettle = smoothstep(0.45, 1.0, u);

            screenX = pad.x + Math.sin(nowSec * 1.6) * 3.0 * hoverSettle;
            screenY = lerp(pad.y - 3.5, hoverY, climbU) + Math.sin(nowSec * 2.1) * 4.0 * hoverSettle;
            forwardPitch = lerp(-0.02, 0.02, smoothstep(0.35, 1.0, u));
            bankRoll = Math.sin(nowSec * 1.4) * 0.015 * hoverSettle;
            mainRotorSpeed = 34.0;
            rotorBlurFactor = 0.9;
            downwashIntensity = lerp(0.85, 0.55, climbU);
        } else if (cycleMs < FLY_AWAY_END_MS) {
            // Phase 2: Smooth acceleration and climb out left across hero -> completely off-screen left
            phase = 'fly_away';
            const u = clamp01((cycleMs - TAKEOFF_END_MS) / (FLY_AWAY_END_MS - TAKEOFF_END_MS));
            const accelU = lerp(Math.pow(u, 1.48), smoothstep(0, 1, u), 0.18);
            const pt = cubicBezierPt(depP0, depP1, depP2, depP3, accelU);

            const swayBlend = 1 - smoothstep(0.0, 0.22, u);
            const hoverCarryX = Math.sin(nowSec * 1.6) * 3.0 * swayBlend;
            const hoverCarryY = Math.sin(nowSec * 2.1) * 4.0 * swayBlend;
            const verticalWave = Math.sin(u * Math.PI * 2.2) * Math.min(vh * 0.018, 12) + Math.sin(nowSec * 1.8) * 3.0 * smoothstep(0.08, 0.35, u);

            screenX = pt.x + hoverCarryX;
            screenY = pt.y + hoverCarryY + verticalWave;

            forwardPitch = lerp(0.02, 0.21, smoothstep(0.0, 0.34, u)) + Math.cos(nowSec * 1.3) * 0.012 * smoothstep(0.1, 0.35, u);
            bankRoll = lerp(Math.sin(nowSec * 1.4) * 0.015, 0.15, smoothstep(0.05, 0.42, u)) + Math.sin(nowSec * 1.5) * 0.018 * smoothstep(0.15, 0.45, u);
            yaw = BASE_YAW - lerp(0, 0.12, smoothstep(0.05, 0.48, u));

            const distFactor = Math.pow(accelU, 1.08);
            depthZ = lerp(0, -8.5, distFactor);
            depthScale = lerp(1.0, 0.54, distFactor);

            mainRotorSpeed = lerp(34.0, 35.0, smoothstep(0.0, 0.3, u));
            rotorBlurFactor = lerp(0.9, 0.92, smoothstep(0.0, 0.3, u));
            downwashIntensity = Math.max(0, 0.55 * (1 - smoothstep(0.0, 0.22, u)));
        } else if (cycleMs < WAIT_AWAY_END_MS) {
            // Phase 3: Off-screen left pause (~6s) before returning from the exact same exit side
            phase = 'wait_away';
            hasDisappeared = true;
            screenX = exitPt.x;
            screenY = exitPt.y;
            yaw = RETURN_YAW;
            depthZ = -8.5;
            depthScale = 0.54;
            mainRotorSpeed = 35.0;
            rotorBlurFactor = 0.92;
        } else if (cycleMs < RETURN_END_MS) {
            // Phase 4: Return from the left side flying right, decelerating smoothly with nose-up flare into pad hover
            phase = 'return';
            const u = clamp01((cycleMs - WAIT_AWAY_END_MS) / (RETURN_END_MS - WAIT_AWAY_END_MS));
            const decelU = 1 - Math.pow(1 - u, 2.4);
            const pt = cubicBezierPt(retR0, retR1, retR2, retR3, decelU);

            const settleBlend = smoothstep(0.78, 1.0, u);
            const arrivalHoverX = Math.sin(nowSec * 1.7) * 3.5 * settleBlend;
            const arrivalHoverY = Math.sin(nowSec * 2.2) * 4.5 * settleBlend;
            const verticalWave = Math.sin(u * Math.PI * 2.0) * Math.min(vh * 0.016, 11) + Math.sin(nowSec * 1.7) * 3.0 * (1 - settleBlend);

            screenX = pt.x + arrivalHoverX;
            screenY = pt.y + verticalWave + arrivalHoverY;

            // Slight forward cruise pitch on entry, then smooth nose-up aerodynamic flare while slowing down into hover
            const flarePitch = lerp(0.10, -0.08, smoothstep(0.24, 0.72, u));
            forwardPitch = lerp(flarePitch, Math.cos(nowSec * 1.5) * 0.015, smoothstep(0.76, 1.0, u));
            bankRoll = lerp(-0.11, Math.sin(nowSec * 1.6) * 0.018, smoothstep(0.45, 0.96, u));
            yaw = RETURN_YAW;

            const distFactor = Math.pow(1 - decelU, 1.08);
            depthZ = lerp(0, -8.5, distFactor);
            depthScale = lerp(1.0, 0.54, distFactor);

            mainRotorSpeed = lerp(35.0, 33.5, smoothstep(0.7, 1.0, u));
            rotorBlurFactor = lerp(0.92, 0.9, smoothstep(0.7, 1.0, u));
            downwashIntensity = smoothstep(0.84, 1.0, u) * 0.52;
        } else if (cycleMs < HOVER_TURN_END_MS) {
            // Phase 5A: Hover & smooth 180-degree pedal-turn alignment directly over helipad
            phase = 'landing_hover_turn';
            const u = clamp01((cycleMs - RETURN_END_MS) / (HOVER_TURN_END_MS - RETURN_END_MS));
            const turnU = smoothstep(0.08, 0.94, u);

            screenX = pad.x + Math.sin(nowSec * 1.7) * 3.5;
            screenY = hoverY + Math.sin(nowSec * 2.2) * 4.5;
            yaw = lerp(RETURN_YAW, BASE_YAW, turnU);
            forwardPitch = Math.cos(nowSec * 1.5) * 0.015;
            bankRoll = Math.sin(nowSec * 1.6) * 0.018;

            mainRotorSpeed = 33.5;
            rotorBlurFactor = 0.9;
            downwashIntensity = lerp(0.52, 0.65, u);
        } else if (cycleMs < LANDING_END_MS) {
            // Phase 5B: Smooth vertical descent with brief pre-touchdown hover cushion onto exact pad spot
            phase = 'landing_descend';
            const u = clamp01((cycleMs - HOVER_TURN_END_MS) / (LANDING_END_MS - HOVER_TURN_END_MS));
            const descU = smoothstep(0.0, 1.0, u);
            const hoverCushion = Math.sin(u * Math.PI) * 3.2;
            const settleDecay = 1 - smoothstep(0.25, 0.85, u);

            screenX = pad.x + Math.sin(nowSec * 1.7) * 3.5 * settleDecay;
            screenY = lerp(hoverY, pad.y, descU) + Math.sin(nowSec * 2.2) * 4.5 * settleDecay - hoverCushion * (1 - u);
            yaw = BASE_YAW;
            forwardPitch = Math.cos(nowSec * 1.5) * 0.015 * settleDecay - 0.02 * Math.sin(u * Math.PI);
            bankRoll = Math.sin(nowSec * 1.6) * 0.018 * settleDecay;

            mainRotorSpeed = lerp(33.5, 28.0, smoothstep(0.55, 1.0, u));
            rotorBlurFactor = lerp(0.9, 0.78, smoothstep(0.60, 1.0, u));
            downwashIntensity = lerp(0.65, 0.82, smoothstep(0.0, 0.75, u));
        } else {
            // Phase 6: Landed at original spot (~3.6s wait); rotors gently spool down cleanly to match t=0 idle
            phase = 'wait_ground';
            const u = clamp01((cycleMs - LANDING_END_MS) / (TOTAL_CYCLE_MS - LANDING_END_MS));
            screenX = pad.x;
            screenY = pad.y;
            yaw = BASE_YAW;
            forwardPitch = 0;
            bankRoll = 0;

            mainRotorSpeed = lerp(28.0, 1.0, Math.pow(smoothstep(0.0, 0.88, u), 0.75));
            rotorBlurFactor = 0.78 * (1 - smoothstep(0.10, 0.62, u));
            downwashIntensity = 0.82 * (1 - smoothstep(0.0, 0.55, u));
        }

        const tailRotorSpeed = mainRotorSpeed * 4.5;
        const altitudePx = Math.max(0, pad.y - screenY);

        // Subtle high-frequency rotorcraft vibration near liftoff/touchdown transitions
        const nearGroundSpin = (phase === 'takeoff_spool' || phase === 'takeoff_hover' || phase === 'landing_descend') && mainRotorSpeed > 18;
        const vibY = nearGroundSpin ? Math.sin(nowSec * 38) * 0.45 : 0;
        const finalScreenY = screenY + vibY;

        const ndcX = (screenX / Math.max(1, vw)) * 2 - 1;
        const ndcY = 1 - (finalScreenY / Math.max(1, vh)) * 2;
        const shadowAlpha = hasDisappeared ? 0 : Math.max(0, 0.44 * (1 - smoothstep(0, 150, altitudePx)));
        const opacity = hasDisappeared ? 0 : 1;
        const beaconPulse = sampleBeaconPulse(nowSec);

        return {
            phase,
            cycleMs,
            helipadX: pad.x,
            helipadY: pad.y,
            helipadEllipse: layout.helipadEllipse,
            screenX,
            screenY: finalScreenY,
            altitudePx,
            shadowAlpha,
            downwashIntensity,
            ndcX,
            ndcY,
            depthZ,
            depthScale,
            forwardPitch,
            bankRoll,
            yaw,
            mainRotorSpeed,
            tailRotorSpeed,
            rotorBlurFactor,
            beaconPulse,
            hasDisappeared,
            opacity,
        };
    }

    // -------------------------------------------------------------------------
    // 4. GLB Helicopter Renderer Initialization (Three.js GLTFLoader -> Fallback)
    // -------------------------------------------------------------------------
    let heliRenderer = null;
    let fallbackTimer = null;

    if (webglCanvas) {
        webglCanvas.style.display = '';
    }
    if (fallbackStage) {
        fallbackStage.hidden = true;
    }

    function activateFallbackRenderer() {
        if (webglCanvas) {
            webglCanvas.style.display = 'none';
        }
        if (fallbackStage && fallbackHeli) {
            fallbackStage.hidden = false;
            heliRenderer = createFallbackHeliRenderer(
                fallbackHeli,
                fallbackMainRotor,
                fallbackTailRotor,
                fallbackTailBeaconGlow,
                fallbackTailBeaconCore
            );
        }
    }

    if (hasWebGLSupport()) {
        loadScript(THREE_CDN, 18000)
            .then(() => loadScript(GLTF_LOADER_CDN, 18000))
            .then(() => {
                if (isDisposed || !window.THREE || !window.THREE.GLTFLoader) {
                    activateFallbackRenderer();
                    return null;
                }
                return createGltfHelicopterRenderer(webglCanvas, MODEL_URL, getContainerSize);
            })
            .then((rendererInstance) => {
                if (fallbackTimer !== null) {
                    window.clearTimeout(fallbackTimer);
                    fallbackTimer = null;
                }
                if (isDisposed) {
                    if (rendererInstance && rendererInstance.dispose) {
                        rendererInstance.dispose();
                    }
                    return;
                }
                if (rendererInstance) {
                    if (heliRenderer && typeof heliRenderer.dispose === 'function') {
                        heliRenderer.dispose();
                    }
                    if (webglCanvas) {
                        webglCanvas.style.display = '';
                    }
                    if (fallbackStage) {
                        fallbackStage.hidden = true;
                    }
                    heliRenderer = rendererInstance;
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

    // If GLB + WebGL cannot initialize within 3.5s, fall back seamlessly so animation never stalls
    fallbackTimer = window.setTimeout(() => {
        fallbackTimer = null;
        if (!isDisposed && !heliRenderer) {
            activateFallbackRenderer();
        }
    }, 3500);

    // -------------------------------------------------------------------------
    // 5. Continuous Independent 60 FPS Infinite Animation Loop
    // -------------------------------------------------------------------------
    function resizeAllLayers() {
        if (cloudController && cloudController.resize) {
            cloudController.resize();
        }
        if (shadowController && shadowController.resize) {
            shadowController.resize();
        }
        if (rotorWashController && rotorWashController.resize) {
            rotorWashController.resize();
        }
        if (heliRenderer && heliRenderer.resize) {
            heliRenderer.resize();
        }
    }

    if (window.ResizeObserver) {
        const ro = new window.ResizeObserver(() => {
            resizeAllLayers();
        });
        ro.observe(overlay);
    }

    function stopLoop() {
        isLoopRunning = false;
        if (rafId !== null) {
            window.cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    function startLoop() {
        if (isDisposed || isLoopRunning || document.hidden) {
            return;
        }
        isLoopRunning = true;
        lastFrameTime = performance.now();
        rafId = window.requestAnimationFrame(tick);
    }

    function tick(now) {
        if (isDisposed || !isLoopRunning) {
            return;
        }

        const dt = Math.min((now - lastFrameTime) / 1000, 0.05);
        lastFrameTime = now;
        const elapsedMs = Math.max(0, now - startTime);
        const nowSec = now * 0.001;

        const size = getContainerSize();
        const vw = size.width;
        const vh = size.height;
        const flight = sampleFlightState(elapsedMs, nowSec, vw, vh);

        const isSlideVisible = heroSlide.classList.contains('is-active') || heroSlide.classList.contains('is-outgoing');
        if (isSlideVisible) {
            if (cloudController) {
                cloudController.update(dt, nowSec);
            }

            if (shadowController) {
                shadowController.update(flight, vw, nowSec);
            }

            if (rotorWashController) {
                rotorWashController.update(dt, nowSec, flight, vw);
            }

            if (heliRenderer) {
                heliRenderer.update(flight, dt, nowSec);
            }
        }

        rafId = window.requestAnimationFrame(tick);
    }

    const slideObserver = new window.MutationObserver(() => {
        if (heroSlide.classList.contains('is-active') && !document.hidden) {
            resizeAllLayers();
            if (!isLoopRunning) {
                startLoop();
            }
        }
    });

    slideObserver.observe(heroSlide, { attributes: true, attributeFilter: ['class'] });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopLoop();
        } else {
            startLoop();
        }
    });

    window.addEventListener('pagehide', () => {
        isDisposed = true;
        if (fallbackTimer !== null) {
            window.clearTimeout(fallbackTimer);
            fallbackTimer = null;
        }
        stopLoop();
        slideObserver.disconnect();
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
    });

    startLoop();

    // =========================================================================
    // Subtle Drifting Sky Clouds
    // =========================================================================
    function createCloudLayerSystem(canvas, getSize) {
        if (!canvas) {
            return null;
        }
        const ctx = canvas.getContext('2d', { alpha: true });
        if (!ctx) {
            return null;
        }

        let { width, height } = getSize();
        let dpr = Math.min(window.devicePixelRatio || 1, 1.5);

        const resize = () => {
            const s = getSize();
            width = s.width;
            height = s.height;
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
            resize,
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
    // Real-Time Helipad Perimeter Aviation Lights & Contact Shadow
    // =========================================================================
    function createLedGlowSprite(r, g, b) {
        const c = document.createElement('canvas');
        c.width = 32;
        c.height = 32;
        const gCtx = c.getContext('2d');
        const grad = gCtx.createRadialGradient(16, 16, 0.5, 16, 16, 15.5);
        grad.addColorStop(0.0, 'rgba(255, 255, 255, 0.96)');
        grad.addColorStop(0.22, `rgba(${r}, ${g}, ${b}, 0.72)`);
        grad.addColorStop(0.55, `rgba(${r}, ${g}, ${b}, 0.22)`);
        grad.addColorStop(1.0, `rgba(${r}, ${g}, ${b}, 0)`);
        gCtx.fillStyle = grad;
        gCtx.fillRect(0, 0, 32, 32);
        return c;
    }

    function createHelipadShadowRenderer(canvas, getSize) {
        if (!canvas) {
            return null;
        }
        const ctx = canvas.getContext('2d', { alpha: true });
        if (!ctx) {
            return null;
        }

        let { width, height } = getSize();
        let dpr = Math.min(window.devicePixelRatio || 1, 2);

        const resize = () => {
            const s = getSize();
            width = s.width;
            height = s.height;
            dpr = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = Math.round(width * dpr);
            canvas.height = Math.round(height * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        };

        resize();
        window.addEventListener('resize', resize, { passive: true });

        // Pre-cached radial glow textures (zero per-frame allocations)
        const whiteGlow = createLedGlowSprite(220, 240, 255);
        const blueGlow = createLedGlowSprite(115, 210, 255);
        const greenGlow = createLedGlowSprite(72, 238, 160);

        // 12 fixed aviation perimeter fixtures around the circular helipad rim
        // Green approach guidance markers face the left arrival/departure sector & right axis
        const padLights = [];
        const totalLights = 12;
        for (let i = 0; i < totalLights; i++) {
            const angle = (i / totalLights) * Math.PI * 2;
            const isApproachGreen = i === 5 || i === 6 || i === 7 || i === 0;
            const isBlue = !isApproachGreen && i % 2 === 1;
            padLights.push({
                cos: Math.cos(angle),
                sin: Math.sin(angle),
                phaseOffset: i * 0.52,
                kind: isApproachGreen ? 'green' : (isBlue ? 'blue' : 'white'),
                coreColor: isApproachGreen ? '#5ff5b4' : (isBlue ? '#aee4ff' : '#f4f9ff'),
                sprite: isApproachGreen ? greenGlow : (isBlue ? blueGlow : whiteGlow),
            });
        }

        return {
            resize,
            update(flight, vw, nowSec) {
                ctx.clearRect(0, 0, width, height);

                const uiScale = Math.max(0.68, Math.min(1.25, vw / 1280));
                const t = typeof nowSec === 'number' ? nowSec : 0;

                // 1. Fixed Helipad Perimeter & Approach Guidance Lights (independent of helicopter)
                const ellipse = flight && flight.helipadEllipse;
                if (ellipse) {
                    const approachPulse = 0.5 + 0.5 * Math.sin(t * 2.6);
                    for (let i = 0; i < padLights.length; i++) {
                        const lgt = padLights[i];
                        const perspFactor = 1.0 + 0.045 * lgt.sin;
                        const lx = ellipse.cx + lgt.cos * ellipse.rx * perspFactor;
                        const ly = ellipse.cy + lgt.sin * ellipse.ry;
                        if (lx < -24 || lx > width + 24 || ly < -24 || ly > height + 24) {
                            continue;
                        }

                        // Subtle foreshortening: closer bottom fixtures read slightly brighter/larger
                        const depthWeight = 0.80 + 0.26 * ((lgt.sin + 1) * 0.5);
                        const seqPulse = 0.5 + 0.5 * Math.sin(t * 2.9 - lgt.phaseOffset);
                        const pulse = lgt.kind === 'green'
                            ? lerp(0.32, 0.92, Math.pow(approachPulse, 1.35))
                            : lerp(0.24, 0.84, Math.pow(seqPulse, 1.5));

                        // Compact ground LED housing collar
                        ctx.fillStyle = 'rgba(20, 28, 34, 0.65)';
                        ctx.beginPath();
                        ctx.ellipse(lx, ly + 0.6 * uiScale, 3.0 * uiScale * depthWeight, 1.9 * uiScale * depthWeight, 0, 0, Math.PI * 2);
                        ctx.fill();

                        // Soft radial LED halo
                        const glowR = (9.5 + pulse * 4.5) * uiScale * depthWeight;
                        ctx.globalAlpha = (0.16 + pulse * 0.44) * depthWeight;
                        ctx.drawImage(lgt.sprite, lx - glowR, ly - glowR * 0.72, glowR * 2, glowR * 1.44);

                        // Crisp tiny LED core dot
                        ctx.globalAlpha = 0.45 + pulse * 0.55;
                        ctx.fillStyle = lgt.coreColor;
                        ctx.beginPath();
                        ctx.arc(lx, ly, (1.35 + pulse * 0.45) * uiScale * depthWeight, 0, Math.PI * 2);
                        ctx.fill();
                    }
                    ctx.globalAlpha = 1;
                }

                // 2. Real-Time Helicopter Contact Shadow on Ground Pad
                if (!flight || flight.shadowAlpha <= 0.01) {
                    return;
                }

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
    // Realistic Rotor-Wash Ground Dust (Active Near Ground Take-off & Landing)
    // =========================================================================
    function createRotorWashSystem(canvas, getSize) {
        if (!canvas) {
            return null;
        }
        const ctx = canvas.getContext('2d', { alpha: true });
        if (!ctx) {
            return null;
        }

        let { width, height } = getSize();
        let dpr = Math.min(window.devicePixelRatio || 1, 1.5);

        const resize = () => {
            const s = getSize();
            width = s.width;
            height = s.height;
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
            resize,
            reset() {
                dustParticles.length = 0;
                spawnCarry = 0;
                ctx.clearRect(0, 0, width, height);
            },
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

    function createGltfHelicopterRenderer(canvas, glbUrl, getSize) {
        const THREE = window.THREE;
        const renderer = new THREE.WebGLRenderer({
            canvas,
            alpha: true,
            antialias: true,
            powerPreference: 'high-performance',
        });

        let { width, height } = getSize();
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

        // Pre-generate soft red halo sprite texture once for tail anti-collision beacon
        const beaconGlowCanvas = document.createElement('canvas');
        beaconGlowCanvas.width = 64;
        beaconGlowCanvas.height = 64;
        const bgCtx = beaconGlowCanvas.getContext('2d');
        const bgGrad = bgCtx.createRadialGradient(32, 32, 1, 32, 32, 31);
        bgGrad.addColorStop(0.0, 'rgba(255, 235, 225, 1.0)');
        bgGrad.addColorStop(0.22, 'rgba(255, 58, 42, 0.85)');
        bgGrad.addColorStop(0.55, 'rgba(235, 18, 12, 0.28)');
        bgGrad.addColorStop(1.0, 'rgba(220, 0, 0, 0)');
        bgCtx.fillStyle = bgGrad;
        bgCtx.fillRect(0, 0, 64, 64);
        const beaconGlowTex = new THREE.CanvasTexture(beaconGlowCanvas);
        applySRGB(THREE, beaconGlowTex, false);

        return new Promise((resolve, reject) => {
            loader.load(
                glbUrl,
                (gltf) => {
                    const heliRoot = gltf.scene;
                    const mainRotor = heliRoot.getObjectByName('MainRotor');
                    const tailRotor = heliRoot.getObjectByName('TailRotor');
                    const trackedMaterials = [];

                    // Guard against any stray floating or duplicate parts
                    const unwantedNames = new Set([
                        'mirrors_0',
                        'mirrors_1',
                        'mirrors_2',
                        'mirrors_3',
                        'Rear_rotor02.001',
                        'Rear_rotor10.001',
                        'fuselage_13',
                        'TailHub_rotortail1',
                    ]);

                    heliRoot.traverse((node) => {
                        if (node.name && unwantedNames.has(node.name)) {
                            node.visible = false;
                            return;
                        }
                        if (!node.isMesh || !node.material) {
                            return;
                        }
                        const mats = Array.isArray(node.material) ? node.material : [node.material];
                        mats.forEach((mat) => {
                            if (mat.name === 'Glass_0' || mat.transparent) {
                                mat.color.setHex(0x163847);
                                mat.opacity = 0.56;
                                mat.roughness = 0.08;
                                mat.metalness = 0.22;
                                mat.envMapIntensity = 1.85;
                                mat.depthWrite = false;
                            } else {
                                mat.envMapIntensity = 1.18;
                                if (typeof mat.roughness === 'number') {
                                    mat.roughness = Math.max(mat.roughness, 0.24);
                                }
                                if (typeof mat.metalness === 'number') {
                                    mat.metalness = Math.min(mat.metalness, 0.18);
                                }
                            }
                            mat.userData = mat.userData || {};
                            mat.userData.baseOpacity = mat.opacity !== undefined ? mat.opacity : 1;
                            mat.userData.baseTransparent = Boolean(mat.transparent);
                            trackedMaterials.push(mat);
                        });
                    });

                    // Polish MainRotor composite roughness
                    if (mainRotor) {
                        mainRotor.traverse((mNode) => {
                            if (mNode.isMesh && mNode.material) {
                                mNode.material.roughness = 0.42;
                                mNode.material.envMapIntensity = 0.95;
                            }
                        });
                    }

                    // Upgrade TailRotor: graphite blades + yellow safety tips + spinner cap + natural initial tilt
                    const tailBlurMeshes = [];
                    if (tailRotor) {
                        tailRotor.rotation.x = 0.48;

                        const tailBladeMat = new THREE.MeshStandardMaterial({
                            color: 0x323b44,
                            roughness: 0.36,
                            metalness: 0.22,
                            envMapIntensity: 1.05,
                        });
                        tailBladeMat.userData = { baseOpacity: 1, baseTransparent: false };
                        trackedMaterials.push(tailBladeMat);

                        const tailTipMat = new THREE.MeshStandardMaterial({
                            color: 0xf4c430,
                            roughness: 0.32,
                            metalness: 0.10,
                            envMapIntensity: 1.05,
                        });
                        tailTipMat.userData = { baseOpacity: 1, baseTransparent: false };
                        trackedMaterials.push(tailTipMat);

                        ['TailBlade_0', 'TailBlade_1'].forEach((bladeName, idx) => {
                            const bladeNode = tailRotor.getObjectByName(bladeName);
                            if (!bladeNode) {
                                return;
                            }
                            bladeNode.traverse((bChild) => {
                                if (bChild.isMesh) {
                                    bChild.material = tailBladeMat;
                                }
                            });
                            const tipGeo = new THREE.BoxGeometry(0.156, 0.024, 0.16);
                            const tipMesh = new THREE.Mesh(tipGeo, tailTipMat);
                            tipMesh.position.set(0, 0, idx === 0 ? -1.20 : 1.20);
                            bladeNode.add(tipMesh);
                        });

                        // Compact brushed spinner nut on outer TailRotor hub
                        const spinnerMat = new THREE.MeshStandardMaterial({
                            color: 0x454f59,
                            roughness: 0.28,
                            metalness: 0.55,
                            envMapIntensity: 1.35,
                        });
                        spinnerMat.userData = { baseOpacity: 1, baseTransparent: false };
                        trackedMaterials.push(spinnerMat);
                        const spinnerMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.048, 0.072, 0.065, 14), spinnerMat);
                        spinnerMesh.rotation.z = Math.PI / 2;
                        spinnerMesh.position.set(-0.08, 0, 0);
                        tailRotor.add(spinnerMesh);

                        // Subtle 3D motion-blur sectors + swept outer disc in Y-Z rotation plane
                        [0, Math.PI].forEach((az) => {
                            [
                                { arc: 0.22, opacity: 0.24 },
                                { arc: 0.52, opacity: 0.12 },
                            ].forEach((tr) => {
                                const tbMat = new THREE.MeshBasicMaterial({
                                    color: 0x262e36,
                                    transparent: true,
                                    opacity: 0,
                                    side: THREE.DoubleSide,
                                    depthWrite: false,
                                });
                                const tbGeo = new THREE.RingGeometry(0.20, 1.27, 16, 1, az - tr.arc, tr.arc);
                                const tbMesh = new THREE.Mesh(tbGeo, tbMat);
                                tbMesh.rotation.y = Math.PI / 2;
                                tbMesh.position.set(0.01, 0, 0);
                                tbMesh.visible = false;
                                tailRotor.add(tbMesh);
                                tailBlurMeshes.push({ mesh: tbMesh, baseOpacity: tr.opacity });
                            });
                        });

                        const discMat = new THREE.MeshBasicMaterial({
                            color: 0x2a333c,
                            transparent: true,
                            opacity: 0,
                            side: THREE.DoubleSide,
                            depthWrite: false,
                        });
                        const discMesh = new THREE.Mesh(new THREE.RingGeometry(0.22, 1.26, 36), discMat);
                        discMesh.rotation.y = Math.PI / 2;
                        discMesh.position.set(0.005, 0, 0);
                        discMesh.visible = false;
                        tailRotor.add(discMesh);
                        tailBlurMeshes.push({ mesh: discMesh, baseOpacity: 0.10 });

                        const tipBlurMat = new THREE.MeshBasicMaterial({
                            color: 0xf4c430,
                            transparent: true,
                            opacity: 0,
                            side: THREE.DoubleSide,
                            depthWrite: false,
                        });
                        const tipBlurMesh = new THREE.Mesh(new THREE.RingGeometry(1.13, 1.28, 36), tipBlurMat);
                        tipBlurMesh.rotation.y = Math.PI / 2;
                        tipBlurMesh.position.set(0.004, 0, 0);
                        tipBlurMesh.visible = false;
                        tailRotor.add(tipBlurMesh);
                        tailBlurMeshes.push({ mesh: tipBlurMesh, baseOpacity: 0.14 });
                    }

                    // 3D Blinking Red Anti-Collision Beacon on Top of Vertical Tail Fin
                    const tailBeaconGroup = new THREE.Group();
                    tailBeaconGroup.position.set(-0.001, 2.012, -7.255);

                    const beaconCollarMat = new THREE.MeshStandardMaterial({
                        color: 0x242b32,
                        roughness: 0.35,
                        metalness: 0.5,
                    });
                    beaconCollarMat.userData = { baseOpacity: 1, baseTransparent: false };
                    trackedMaterials.push(beaconCollarMat);

                    const collarMesh = new THREE.Mesh(new THREE.CylinderGeometry(0.040, 0.054, 0.036, 14), beaconCollarMat);
                    collarMesh.position.set(0, 0.012, 0);
                    tailBeaconGroup.add(collarMesh);

                    const beaconLensMat = new THREE.MeshStandardMaterial({
                        color: 0xa61210,
                        emissive: 0xff1e16,
                        emissiveIntensity: 0.35,
                        roughness: 0.16,
                        metalness: 0.10,
                        transparent: true,
                        opacity: 0.94,
                    });
                    beaconLensMat.userData = { baseOpacity: 0.94, baseTransparent: true };
                    trackedMaterials.push(beaconLensMat);

                    const lensMesh = new THREE.Mesh(new THREE.SphereGeometry(0.040, 14, 12), beaconLensMat);
                    lensMesh.scale.set(1, 1.15, 1);
                    lensMesh.position.set(0, 0.044, 0);
                    tailBeaconGroup.add(lensMesh);

                    const beaconCoreMat = new THREE.MeshBasicMaterial({
                        color: 0xffdcd0,
                        transparent: true,
                        opacity: 0.3,
                        depthWrite: false,
                    });
                    const coreMesh = new THREE.Mesh(new THREE.SphereGeometry(0.022, 10, 10), beaconCoreMat);
                    coreMesh.position.set(0, 0.046, 0);
                    tailBeaconGroup.add(coreMesh);

                    const beaconGlowMat = new THREE.SpriteMaterial({
                        map: beaconGlowTex,
                        color: 0xff2a1e,
                        transparent: true,
                        opacity: 0.25,
                        blending: THREE.AdditiveBlending,
                        depthWrite: false,
                    });
                    const beaconGlowSprite = new THREE.Sprite(beaconGlowMat);
                    beaconGlowSprite.position.set(0, 0.048, 0);
                    beaconGlowSprite.scale.setScalar(0.55);
                    tailBeaconGroup.add(beaconGlowSprite);

                    heliRoot.add(tailBeaconGroup);

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

                    // Shift model slightly along Z so its fuselage center sits cleanly over the helipad 'H'
                    heliRoot.position.set(0, 0, 1.5);

                    const flightRig = new THREE.Group();
                    const attitudeGroup = new THREE.Group();
                    attitudeGroup.add(heliRoot);
                    flightRig.add(attitudeGroup);
                    scene.add(flightRig);

                    const handleResize = () => {
                        const s = getSize();
                        width = s.width;
                        height = s.height;
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
                        resize: handleResize,
                        update(flight, dt) {
                            if (flight.hasDisappeared || flight.opacity <= 0.01) {
                                flightRig.visible = false;
                                renderer.clear();
                                return;
                            }

                            flightRig.visible = true;

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

                            flightRig.rotation.y = flight.yaw;

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

                            // 2. Independent continuous rotation of tail rotor + motion blur
                            if (tailRotor) {
                                tailRotor.rotation.x += dt * flight.tailRotorSpeed;
                            }

                            for (let t = 0; t < tailBlurMeshes.length; t++) {
                                const tb = tailBlurMeshes[t];
                                tb.mesh.visible = flight.rotorBlurFactor > 0.03;
                                tb.mesh.material.opacity = tb.baseOpacity * flight.rotorBlurFactor * flight.opacity;
                            }

                            // 3. Aviation red anti-collision tail beacon strobe
                            const pulse = flight.beaconPulse || 0;
                            beaconLensMat.emissiveIntensity = 0.30 + pulse * 3.2;
                            beaconCoreMat.opacity = (0.20 + pulse * 0.80) * flight.opacity;
                            beaconGlowMat.opacity = (0.10 + pulse * 0.85) * flight.opacity;
                            beaconGlowSprite.scale.setScalar(0.52 + pulse * 0.85);

                            const isFading = flight.opacity < 0.99;
                            for (let i = 0; i < trackedMaterials.length; i++) {
                                const mat = trackedMaterials[i];
                                mat.transparent = isFading ? true : mat.userData.baseTransparent;
                                mat.opacity = mat.userData.baseOpacity * flight.opacity;
                            }

                            renderer.render(scene, camera);
                        },
                        dispose() {
                            window.removeEventListener('resize', handleResize);
                            envRT.dispose();
                            beaconGlowTex.dispose();
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
    function createFallbackHeliRenderer(el, mainRotorEl, tailRotorEl, tailBeaconGlowEl, tailBeaconCoreEl) {
        let mainDeg = 0;
        let tailDeg = 28;

        return {
            update(flight, dt) {
                if (flight.hasDisappeared || flight.opacity <= 0.01) {
                    el.style.opacity = '0';
                    return;
                }

                mainDeg = (mainDeg + dt * flight.mainRotorSpeed * (180 / Math.PI)) % 360;
                tailDeg = (tailDeg + dt * flight.tailRotorSpeed * (180 / Math.PI)) % 360;

                const tiltDeg = flight.forwardPitch * (180 / Math.PI);
                const scale = flight.depthScale;
                const elH = el.offsetHeight || 105;
                const skidOffsetPx = elH * 0.33 * scale;

                // Smooth horizontal heading projection matching 3D pedal turn (-1 left -> +1 right)
                const dirSign = Math.sin(flight.yaw + 0.34);
                const scaleX = (Math.abs(dirSign) < 0.06 ? (dirSign >= 0 ? 0.06 : -0.06) : dirSign) * scale;

                if (mainRotorEl) {
                    const effectiveMainDeg = dirSign < 0 ? -mainDeg : mainDeg;
                    mainRotorEl.setAttribute(
                        'transform',
                        `translate(183 31) scale(1 0.24) rotate(${effectiveMainDeg.toFixed(1)})`
                    );
                }

                if (tailRotorEl) {
                    tailRotorEl.setAttribute('transform', `translate(31 79) rotate(${tailDeg.toFixed(1)})`);
                }

                const pulse = flight.beaconPulse || 0;
                if (tailBeaconGlowEl) {
                    tailBeaconGlowEl.setAttribute('opacity', (0.14 + pulse * 0.82).toFixed(2));
                    tailBeaconGlowEl.setAttribute('r', (9 + pulse * 6.5).toFixed(1));
                }
                if (tailBeaconCoreEl) {
                    tailBeaconCoreEl.setAttribute('opacity', (0.25 + pulse * 0.75).toFixed(2));
                }

                el.style.opacity = flight.opacity.toFixed(3);
                el.style.transform =
                    `translate3d(calc(-50% + ${flight.screenX.toFixed(1)}px), calc(-50% + ${(flight.screenY - skidOffsetPx).toFixed(1)}px), 0) ` +
                    `scale(${scaleX.toFixed(3)}, ${scale.toFixed(3)}) ` +
                    `rotateZ(${tiltDeg.toFixed(2)}deg)`;
            },
            dispose() { },
        };
    }
})();
