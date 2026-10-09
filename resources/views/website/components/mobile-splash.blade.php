@guest
<div id="agricoMobileSplash" style="
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: #ffffff;
    flex-direction: column;
    justify-content: space-between;
    padding: 40px 24px 32px 24px;
    box-sizing: border-box;
    overflow-y: auto;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
" class="animate-in fade-in duration-300">

    {{-- TOP BRAND HEADER --}}
    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <img src="/images/agrico-logo.png" style="width: 34px; height: 34px; object-fit: contain;" alt="AgriCo">
            <span style="font-size: 20px; font-weight: 900; color: #0f172a; letter-spacing: -0.5px;">agrico</span>
        </div>
        <button type="button" onclick="closeMobileSplash()" style="
            background: #f1f5f9;
            border: none;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
        ">
            Skip
        </button>
    </div>

    {{-- CENTER GRAPHIC & HERO TITLE --}}
    <div style="text-align: center; margin: auto 0; padding: 32px 0;">
        {{-- Floating soft square background accents --}}
        <div style="position: relative; width: 220px; height: 180px; margin: 0 auto 24px auto;">
            <div style="
                position: absolute;
                width: 90px; height: 90px;
                background: #f0fdf4;
                border-radius: 20px;
                top: 10px; left: 20px;
                transform: rotate(-12deg);
                border: 1px solid rgba(34,197,94,0.15);
            "></div>
            <div style="
                position: absolute;
                width: 100px; height: 100px;
                background: #f8fafc;
                border-radius: 24px;
                top: 30px; right: 20px;
                transform: rotate(14deg);
                border: 1px solid #e2e8f0;
            "></div>
            <div style="
                position: absolute;
                width: 70px; height: 70px;
                background: #dcfce7;
                border-radius: 18px;
                bottom: 10px; left: 75px;
                transform: rotate(5deg);
                box-shadow: 0 10px 25px rgba(34,197,94,0.15);
            "></div>
            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
                <img src="/images/agrico-logo.png" style="width: 72px; height: 72px; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(34,197,94,0.3));" alt="AgriCo Logo">
            </div>
        </div>

        <h1 style="
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.15;
            letter-spacing: -0.8px;
            margin: 0 0 12px 0;
        ">
            Your Smart<br>Soil Assistant
        </h1>
        <p style="
            font-size: 14.5px;
            color: #64748b;
            font-weight: 500;
            max-width: 280px;
            margin: 0 auto;
            line-height: 1.5;
        ">
            Instant camera soil color determination &amp; Munsell GIS mapping for Lal-lo, Cagayan.
        </p>
    </div>

    {{-- BOTTOM BUTTONS (Matching trim / sleek onboarding layout) --}}
    <div style="display: flex; flex-direction: column; gap: 12px; width: 100%;">
        {{-- Primary Button: Scan Soil Now --}}
        <a href="{{ route('website.scan') }}" onclick="closeMobileSplash()" style="
            width: 100%;
            padding: 16px;
            background: #18181b;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            border-radius: 999px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            box-sizing: border-box;
            transition: all 0.2s ease;
        ">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
            </svg>
            Start Soil Scanning
        </a>

        {{-- Secondary Button: Login --}}
        <a href="{{ route('login') }}" style="
            width: 100%;
            padding: 16px;
            background: #18181b;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            border-radius: 999px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            display: flex; align-items: center; justify-content: center; gap: 10px;
            box-sizing: border-box;
        ">
            Sign In to Account
        </a>

        {{-- Light Button: Explore Map --}}
        <a href="{{ route('website.map') }}" onclick="closeMobileSplash()" style="
            width: 100%;
            padding: 15px;
            background: #f4f4f5;
            color: #18181b;
            font-size: 15px;
            font-weight: 700;
            border-radius: 999px;
            text-align: center;
            text-decoration: none;
            box-sizing: border-box;
        ">
            Explore Soil Map
        </a>

        {{-- Disclaimer Footer --}}
        <p style="
            font-size: 11px;
            color: #a1a1aa;
            text-align: center;
            margin: 12px 0 0 0;
            font-weight: 500;
        ">
            By using AgriCo, you agree to our Terms and Privacy Policy.
        </p>
    </div>

</div>

<script>
    function showMobileSplashIfNeeded() {
        // Show onboarding splash on mobile screens (width <= 600px) on first load per session
        if (window.innerWidth <= 600 && !sessionStorage.getItem('agrico_splash_seen')) {
            const splash = document.getElementById('agricoMobileSplash');
            if (splash) splash.style.display = 'flex';
        }
    }

    function closeMobileSplash() {
        const splash = document.getElementById('agricoMobileSplash');
        if (splash) splash.style.display = 'none';
        sessionStorage.setItem('agrico_splash_seen', 'true');
    }

    document.addEventListener('DOMContentLoaded', showMobileSplashIfNeeded);
</script>
@endguest
