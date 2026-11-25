<div class="om-intro-loader"
     id="omIntroLoader"
     role="status"
     aria-live="polite"
     aria-label="Loading {{ $siteSettings['site_name'] ?? 'OmTravel' }}">
    <div class="om-loader-sky" aria-hidden="true"></div>
    <div class="om-loader-sunlight" aria-hidden="true"></div>
    <div class="om-loader-horizon-haze" aria-hidden="true"></div>

    <canvas class="om-loader-clouds-canvas" id="omLoaderCloudsCanvas" aria-hidden="true"></canvas>
    <canvas class="om-loader-airport-canvas" id="omLoaderAirportCanvas" aria-hidden="true"></canvas>
    <canvas class="om-loader-webgl-canvas" id="omLoaderWebglCanvas" aria-hidden="true"></canvas>

    <div class="om-loader-fallback-stage" id="omLoaderFallbackStage" aria-hidden="true" hidden>
        <div class="om-loader-fallback-plane" id="omLoaderPlaneFallback">
            <svg viewBox="0 0 280 140" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="omSideBodyGrad" x1="20" y1="45" x2="265" y2="95" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#E7F0F7"/>
                        <stop offset="48%" stop-color="#FFFFFF"/>
                        <stop offset="100%" stop-color="#D4E2ED"/>
                    </linearGradient>
                    <linearGradient id="omSideWingGrad" x1="90" y1="60" x2="200" y2="115" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FFFFFF"/>
                        <stop offset="100%" stop-color="#C9DAE8"/>
                    </linearGradient>
                    <linearGradient id="omSideTailGrad" x1="25" y1="18" x2="70" y2="75" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#1CA8CB"/>
                        <stop offset="100%" stop-color="#113D48"/>
                    </linearGradient>
                </defs>

                <!-- Far Wing (swept back) -->
                <path d="M135 66 L102 42 L114 40 L165 64 Z" fill="#DCE9F3" stroke="#BCCFD9" stroke-width="0.8"/>

                <!-- Horizontal Tail Stabilizer -->
                <path d="M44 69 L18 56 L28 54 L64 67 Z" fill="#E3EEF6" stroke="#BCCFD9" stroke-width="0.8"/>

                <!-- Vertical Tail Fin -->
                <path d="M42 68 L18 22 L38 22 L72 66 Z" fill="url(#omSideTailGrad)"/>
                <path d="M36 66 L21 34 L31 34 L52 66 Z" fill="#22BBDD" opacity="0.55"/>

                <!-- Retractable Landing Gear (visible on runway & initial climb) -->
                <g class="om-fallback-gear" id="omFallbackGear">
                    <line x1="138" y1="84" x2="136" y2="99" stroke="#536878" stroke-width="2.2" stroke-linecap="round"/>
                    <circle cx="134" cy="101" r="3.8" fill="#1F303B"/>
                    <circle cx="140" cy="101" r="3.8" fill="#1F303B"/>
                    <line x1="226" y1="81" x2="227" y2="98" stroke="#536878" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="227" cy="100" r="3" fill="#1F303B"/>
                </g>

                <!-- Commercial Fuselage (nose pointing right) -->
                <path d="M22 68 C48 64 95 57 165 57 L224 58 C246 59 266 66 268 72 C266 78 244 83 220 84 L110 84 C68 84 40 77 22 68 Z"
                      fill="url(#omSideBodyGrad)"
                      stroke="#C0D2DF"
                      stroke-width="0.9"/>

                <!-- Subtle Airline Cheatline & Cabin Windows -->
                <path d="M62 72 L236 72" stroke="#1CA8CB" stroke-width="1.8" stroke-linecap="round" opacity="0.8"/>
                <path d="M88 66.5 L218 66.5" stroke="#1A3444" stroke-width="2.4" stroke-dasharray="3.2 3.2" stroke-linecap="round" opacity="0.82"/>

                <!-- Cockpit Windshield -->
                <path d="M234 62 L248 63.5 L254 68 L236 68 Z" fill="#152B38"/>

                <!-- Near Swept Main Wing & Winglet -->
                <path d="M168 74 L92 103 L79 102 L126 75 Z" fill="url(#omSideWingGrad)" stroke="#B7CAD8" stroke-width="0.9"/>
                <path d="M92 103 L84 95 L79 102 Z" fill="#1CA8CB"/>

                <!-- Underwing High-Bypass Turbofan Engine -->
                <rect x="148" y="79" width="24" height="10" rx="4.5" fill="#E6F0F7" stroke="#8CA4B6" stroke-width="0.8"/>
                <ellipse cx="171" cy="84" rx="2.4" ry="4.4" fill="#1B303D"/>
                <circle cx="171.5" cy="84" r="1.2" fill="#B8CCD8"/>
            </svg>
        </div>
    </div>

    <header class="om-loader-brand" id="omLoaderBrand" aria-hidden="true">
        <span class="om-loader-brand-mark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
            </svg>
        </span>
        <span class="om-loader-brand-text">
            <span class="om-loader-brand-name">{{ $siteSettings['site_name'] ?? 'OmTravel' }}</span>
            <span class="om-loader-brand-sub">{{ $siteSettings['site_tagline'] ?? 'Explore World' }}</span>
        </span>
    </header>

    <div class="om-loader-bottom" id="omLoaderBottom">
        <p class="om-loader-tagline">YOUR JOURNEY BEGINS HERE</p>
        <div class="om-loader-progress-track" aria-hidden="true">
            <div class="om-loader-progress-fill" id="omLoaderProgressFill"></div>
        </div>
    </div>
</div>
