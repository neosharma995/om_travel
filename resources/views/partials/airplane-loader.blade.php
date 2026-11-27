<div class="om-intro-loader"
     id="omIntroLoader"
     aria-hidden="true">
    <div class="om-loader-sky" aria-hidden="true"></div>
    <img class="om-loader-scenery-img"
         id="omLoaderSceneryImg"
         src="{{ asset('images/loader/himalayan-helipad-bg.jpg') }}"
         alt=""
         aria-hidden="true"
         decoding="async"
         fetchpriority="high">
    <div class="om-loader-sunlight" aria-hidden="true"></div>
    <div class="om-loader-sunrays" aria-hidden="true"></div>
    <div class="om-loader-horizon-haze" aria-hidden="true"></div>

    <canvas class="om-loader-clouds-canvas" id="omLoaderCloudsCanvas" aria-hidden="true"></canvas>
    <canvas class="om-loader-airport-canvas" id="omLoaderAirportCanvas" aria-hidden="true"></canvas>
    <canvas class="om-loader-rotorwash-canvas" id="omLoaderRotorWashCanvas" aria-hidden="true"></canvas>
    <canvas class="om-loader-webgl-canvas" id="omLoaderWebglCanvas" aria-hidden="true"></canvas>

    <div class="om-loader-fallback-stage" id="omLoaderFallbackStage" aria-hidden="true" hidden>
        <div class="om-loader-fallback-heli" id="omLoaderHeliFallback">
            <svg viewBox="0 0 300 160" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="omAs350RedGrad" x1="120" y1="44" x2="255" y2="92" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#E43826"/>
                        <stop offset="60%" stop-color="#D4291B"/>
                        <stop offset="100%" stop-color="#B01F14"/>
                    </linearGradient>
                    <linearGradient id="omAs350WhiteGrad" x1="120" y1="78" x2="245" y2="115" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FFFFFF"/>
                        <stop offset="100%" stop-color="#DCE6EE"/>
                    </linearGradient>
                    <linearGradient id="omAs350GlassGrad" x1="196" y1="52" x2="256" y2="94" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#2E4C5E"/>
                        <stop offset="55%" stop-color="#132632"/>
                        <stop offset="100%" stop-color="#09151D"/>
                    </linearGradient>
                    <radialGradient id="omTailBeaconGlowGrad" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#FF5A48" stop-opacity="0.95"/>
                        <stop offset="38%" stop-color="#FF2216" stop-opacity="0.48"/>
                        <stop offset="100%" stop-color="#FF1E14" stop-opacity="0"/>
                    </radialGradient>
                </defs>

                <!-- High-Skid Landing Gear (Dark Charcoal with Boarding Step) -->
                <g stroke="#1C2228" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="158" y1="104" x2="152" y2="132"/>
                    <line x1="212" y1="104" x2="220" y2="132"/>
                    <path d="M132 132 L242 132 C252 132 259 125 263 117"/>
                    <line x1="176" y1="117" x2="196" y2="117" stroke-width="2"/>
                </g>

                <!-- Slender Red-and-White Tail Boom & Empennage -->
                <path d="M138 74 L32 77 L30 82 L134 89 Z" fill="#FFFFFF" stroke="#C5D2DC" stroke-width="0.7"/>
                <path d="M138 72 L32 76 L32 80 L138 79 Z" fill="url(#omAs350RedGrad)"/>
                <!-- Horizontal Stabilizer -->
                <path d="M58 80 L38 73 L46 72 L74 79 Z" fill="#F2F6FA" stroke="#B7C6D2" stroke-width="0.7"/>
                <!-- Red Upper Vertical Fin + White Lower Ventral Fin -->
                <path d="M40 77 L25 46 L39 46 L52 77 Z" fill="url(#omAs350RedGrad)"/>
                <path d="M42 82 L31 104 L39 104 L49 82 Z" fill="#F7FAFC" stroke="#C0CED8" stroke-width="0.7"/>

                <!-- Blinking Red Tail Anti-Collision Beacon -->
                <g id="omFallbackTailBeacon" transform="translate(29 45)">
                    <circle id="omFallbackTailBeaconGlow" cx="0" cy="-1" r="12" fill="url(#omTailBeaconGlowGrad)" opacity="0.2"/>
                    <rect x="-2.2" y="-0.2" width="4.4" height="2" rx="0.8" fill="#2A333B"/>
                    <circle cx="0" cy="-1.2" r="2.1" fill="#C81812"/>
                    <circle id="omFallbackTailBeaconCore" cx="0" cy="-1.4" r="1.35" fill="#FFD6CC" opacity="0.3"/>
                </g>

                <!-- 2-Blade Tail Rotor (Animated Independently) -->
                <g id="omFallbackTailRotor" transform="translate(31 79)">
                    <circle cx="0" cy="0" r="3.4" fill="#2A333B" stroke="#687684" stroke-width="0.6"/>
                    <rect x="-1.8" y="-18" width="3.6" height="36" rx="1.2" fill="#2B343E"/>
                    <rect x="-1.8" y="-18" width="3.6" height="4.8" fill="#F2C230"/>
                    <rect x="-1.8" y="13.2" width="3.6" height="4.8" fill="#F2C230"/>
                    <circle cx="0" cy="0" r="1.4" fill="#9AA7B2"/>
                </g>

                <!-- AS350 Aerodynamic Cabin (White Crown, Red Body, Crisp White Cheatline, White Belly) -->
                <path d="M132 72 C138 56 156 46 184 46 L210 47 C236 51 258 67 262 84 C260 99 240 107 206 108 L150 107 C135 104 130 92 132 72 Z"
                      fill="url(#omAs350WhiteGrad)"
                      stroke="#C2CFD9"
                      stroke-width="0.8"/>

                <!-- Red Upper Fuselage & Nose Band -->
                <path d="M133 74 C139 58 156 48 184 48 L210 49 C235 53 256 68 261 83 C246 86 226 86 205 85 C186 84 176 92 162 92 C146 92 135 84 133 74 Z"
                      fill="url(#omAs350RedGrad)"/>

                <!-- Gloss White Upper Roof & Turbine Housing Crown -->
                <path d="M144 54 C156 43 182 40 206 47 L194 52 C176 50 158 53 144 58 Z" fill="#FFFFFF"/>

                <!-- Crisp White Cheatline Separating Red & White -->
                <path d="M134 83 C148 90 162 93 177 86 C192 80 228 83 259 83" stroke="#FFFFFF" stroke-width="2.4" fill="none" stroke-linecap="round"/>

                <!-- Turboshaft Exhaust Pipe -->
                <path d="M142 54 L128 57 L130 62 L144 59 Z" fill="#909CA6" stroke="#5A6670" stroke-width="0.6"/>

                <!-- Cockpit Windshield, Chin Glass & Side Cabin Doors/Windows -->
                <path d="M204 51 C228 54 248 66 254 80 L206 81 Z" fill="url(#omAs350GlassGrad)"/>
                <path d="M242 84 C250 84 256 86 258 91 C250 94 243 93 240 89 Z" fill="url(#omAs350GlassGrad)"/>
                <rect x="176" y="56" width="24" height="22" rx="4" fill="url(#omAs350GlassGrad)"/>
                <rect x="158" y="59" width="14" height="19" rx="3.5" fill="url(#omAs350GlassGrad)"/>

                <!-- Rotor Mast & Exposed 3-Arm Hub -->
                <rect x="180" y="30" width="6" height="15" rx="2" fill="#3B4650"/>
                <circle cx="183" cy="31" r="5" fill="#232B32"/>

                <!-- Exactly THREE (3) Main Rotor Blades Spaced 120 Degrees Apart -->
                <g id="omFallbackMainRotor" transform="translate(183 31)">
                    <g id="omFallbackBlade1">
                        <path d="M0 -1.5 L98 -3 L98 2 L0 1.5 Z" fill="#20272E"/>
                        <rect x="90" y="-2.8" width="8" height="4.8" fill="#F2C230"/>
                    </g>
                    <g id="omFallbackBlade2" transform="rotate(120)">
                        <path d="M0 -1.5 L98 -3 L98 2 L0 1.5 Z" fill="#2B343D"/>
                        <rect x="90" y="-2.8" width="8" height="4.8" fill="#F2C230"/>
                    </g>
                    <g id="omFallbackBlade3" transform="rotate(240)">
                        <path d="M0 -1.5 L98 -3 L98 2 L0 1.5 Z" fill="#181E24"/>
                        <rect x="90" y="-2.8" width="8" height="4.8" fill="#F2C230"/>
                    </g>
                </g>
            </svg>
        </div>
    </div>
</div>
