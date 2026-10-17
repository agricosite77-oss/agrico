@php
    $wsAuthUser = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
    if (!$wsAuthUser && session()->has('user_id')) {
        $wsAuthUser = \App\Models\User::where('user_id', session('user_id'))->first();
    }
    if (!$wsAuthUser && session()->has('admin_id')) {
        $wsAuthUser = \App\Models\User::where('user_id', session('admin_id'))->first();
    }
    $isWsAuth = (bool) $wsAuthUser;
    $isWsAdmin = $isWsAuth && (in_array(strtolower($wsAuthUser->role ?? ''), ['admin', 'superadmin', 'super admin']) || Auth::guard('admin')->check());
    $isApproved = $isWsAuth && ($isWsAdmin || in_array(strtolower(trim($wsAuthUser->status ?? '')), ['active', 'approved']));
    $isPending  = $isWsAuth && !$isApproved;
    // Default theme is light mode per user preference (or user saved database theme when logged in)
    $currentTheme = $wsAuthUser ? ($wsAuthUser->theme ?? 'light') : 'light';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $currentTheme === 'light' ? 'light-mode' : '' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="description" content="AgriCo — Smart Soil Scanning & Munsell Color Classification for smarter farming decisions." />
    <meta name="color-scheme" content="light dark" />
    <title>{{ isset($title) ? $title . ' — AgriCo' : 'AgriCo — Smart Soil Management' }}</title>
<script>document.documentElement.classList.add("ui-motion","ui-booting");setTimeout(function(){document.documentElement.classList.remove("ui-booting")},1500);</script>
<link rel="stylesheet" href="{{ asset('css/animations.css') }}">
<script src="{{ asset('js/animations.js') }}" defer></script>
    <script>
        // Apply saved theme IMMEDIATELY before paint to eliminate any flash
        (function() {
            try {
                var stored = localStorage.getItem('agrico-theme');
                var theme = stored;
                @if($wsAuthUser)
                    var userTheme = @json($currentTheme);
                    if (!theme) {
                        theme = userTheme || 'light';
                        localStorage.setItem('agrico-theme', theme);
                    }
                @else
                    if (!theme) {
                        theme = 'light';
                        localStorage.setItem('agrico-theme', 'light');
                    }
                @endif

                if (theme === 'light') {
                    document.documentElement.classList.add('light-mode');
                    document.documentElement.style.colorScheme = 'light';
                } else {
                    document.documentElement.classList.remove('light-mode');
                    document.documentElement.style.colorScheme = 'dark';
                }
            } catch (e) {
                // In case localStorage is blocked in private browsing
                document.documentElement.classList.add('light-mode');
                document.documentElement.style.colorScheme = 'light';
            }
        })();
    </script>
    <script>
        (function() {
            // Unregister any active Service Workers to eliminate browser offline HTML caching
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for (let registration of registrations) {
                        registration.unregister();
                    }
                });
            }

            // Automatically force full reload when restored from browser Back/Forward Cache (BFCache)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || (window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType("navigation")[0] && window.performance.getEntriesByType("navigation")[0].type === "back_forward")) {
                    window.location.reload();
                }
            });

            // Silently suppress Livewire's "This page has expired" confirm popup and reload automatically
            const _confirm = window.confirm;
            window.confirm = function(msg) {
                if (msg && typeof msg === 'string' && msg.toLowerCase().includes('page has expired')) {
                    window.location.reload();
                    return true;
                }
                return _confirm.apply(this, arguments);
            };

            const attachLivewireHook = () => {
                if (window.Livewire && typeof window.Livewire.hook === 'function') {
                    window.Livewire.hook('request', ({ fail }) => {
                        fail(({ status, preventDefault }) => {
                            if (status === 419) {
                                preventDefault();
                                window.location.reload();
                            }
                        });
                    });
                }
            };

            if (window.Livewire) {
                attachLivewireHook();
            } else {
                document.addEventListener('livewire:init', attachLivewireHook);
            }
        })();
    </script>

    <link rel="icon" href="/images/agrico-logo.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet" />

    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#22c55e" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="AgriCo" />
    <link rel="apple-touch-icon" href="/images/agrico-logo.png" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            color-scheme: dark;
            --agrico-dark-bg:       #051F20;
            --agrico-dark-card:     #0B2B26;
            --agrico-forest:        #163832;
            --agrico-green:         #235347;
            --agrico-green-hover:   #163832;
            --agrico-green-deep:    #0B2B26;
            --agrico-mint-sage:     #8EB69B;
            --agrico-mint-light:    #DAF1DE;
            --agrico-bg:            #051F20;
            --agrico-nav-h:         68px;
        }

        /* â”€â”€ Universal Modal Close / Exit Circle Button â”€â”€ */
        .modal-close-circle-btn,
        .agrico-close-btn {
            width: var(--close-btn-size, 40px) !important;
            height: var(--close-btn-size, 40px) !important;
            min-width: 40px !important;
            min-height: 40px !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            border: 1.5px solid rgba(142, 182, 155, 0.35) !important;
            background: rgba(5, 31, 32, 0.70) !important;
            color: #8EB69B !important;
            outline: none !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            flex-shrink: 0 !important;
        }
        .modal-close-circle-btn:hover {
            background: rgba(35, 83, 71, 0.85) !important;
            border-color: #DAF1DE !important;
            color: #DAF1DE !important;
            transform: scale(1.06) !important;
        }
        .modal-close-circle-btn:active {
            transform: scale(0.95) !important;
        }

        /* Light Mode Overrides */
        html.light-mode .modal-close-circle-btn,
        .light-mode .modal-close-circle-btn,
        body.light-mode .modal-close-circle-btn {
            color: #94a3b8 !important;
            background: transparent !important;
            border-color: transparent !important;
        }
        html.light-mode .modal-close-circle-btn:hover,
        .light-mode .modal-close-circle-btn:hover,
        body.light-mode .modal-close-circle-btn:hover {
            background: #f1f5f9 !important;
            border-color: #f1f5f9 !important;
            color: #0f172a !important;
            transform: scale(1.06) !important;
        }

        html {
            color-scheme: dark;
            scrollbar-gutter: stable;
            overflow-y: scroll;
            scrollbar-width: thin !important;
            scrollbar-color: #235347 #051F20 !important;
        }
        body {
            scrollbar-width: thin !important;
            scrollbar-color: #235347 #051F20 !important;
        }

        /* â”€â”€ Modern Standard & WebKit Dark-Themed Scrollbar for Chromium & Safari â”€â”€ */
        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        *::-webkit-scrollbar {
            width: 10px !important;
            height: 10px !important;
        }
        html::-webkit-scrollbar-track,
        body::-webkit-scrollbar-track,
        *::-webkit-scrollbar-track {
            background: #051F20 !important;
        }
        html::-webkit-scrollbar-thumb,
        body::-webkit-scrollbar-thumb,
        *::-webkit-scrollbar-thumb {
            background: #163832 !important;
            border-radius: 9999px !important;
            border: 2px solid #051F20 !important;
            transition: background-color 0.2s ease;
        }
        html::-webkit-scrollbar-thumb:hover,
        body::-webkit-scrollbar-thumb:hover,
        *::-webkit-scrollbar-thumb:hover {
            background: #235347 !important;
        }
        html::-webkit-scrollbar-thumb:active,
        body::-webkit-scrollbar-thumb:active,
        *::-webkit-scrollbar-thumb:active {
            background: #22c55e !important;
        }
        html::-webkit-scrollbar-corner,
        body::-webkit-scrollbar-corner,
        *::-webkit-scrollbar-corner {
            background: #051F20 !important;
        }
        html::-webkit-scrollbar-button,
        body::-webkit-scrollbar-button,
        *::-webkit-scrollbar-button {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* â”€â”€ LIGHT MODE SCROLLBAR (Full Standard & WebKit for all pages including scan) â”€â”€ */
        html.light-mode,
        body.light-mode,
        html.light-mode body {
            color-scheme: light !important;
            scrollbar-color: #94a3b8 #F6FAF7 !important;
            scrollbar-width: thin !important;
        }
        html.light-mode::-webkit-scrollbar-track,
        html.light-mode body::-webkit-scrollbar-track,
        html.light-mode *::-webkit-scrollbar-track {
            background: #F6FAF7 !important;
        }
        html.light-mode::-webkit-scrollbar-thumb,
        html.light-mode body::-webkit-scrollbar-thumb,
        html.light-mode *::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border: 2px solid #F6FAF7 !important;
            border-radius: 9999px !important;
        }
        html.light-mode::-webkit-scrollbar-thumb:hover,
        html.light-mode body::-webkit-scrollbar-thumb:hover,
        html.light-mode *::-webkit-scrollbar-thumb:hover {
            background: #22c55e !important;
        }
        html.light-mode::-webkit-scrollbar-corner,
        html.light-mode body::-webkit-scrollbar-corner,
        html.light-mode *::-webkit-scrollbar-corner {
            background: #F6FAF7 !important;
        }
        html.light-mode::-webkit-scrollbar-button,
        html.light-mode body::-webkit-scrollbar-button,
        html.light-mode *::-webkit-scrollbar-button {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Dark / Light Mode Logo Switcher */
        html:not(.light-mode) .logo-dark { display: inline-block !important; }
        html:not(.light-mode) .logo-light { display: none !important; }
        html.light-mode .logo-dark { display: none !important; }
        html.light-mode .logo-light { display: inline-block !important; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-main, #051F20);
            color: var(--text-primary, #DAF1DE);
            margin: 0;
            padding-top: var(--agrico-nav-h, 68px) !important;
            line-height: 1.6;
            transition: background 0.3s ease, color 0.3s ease;
        }
        body.has-pending-banner {
            padding-top: calc(var(--agrico-nav-h, 68px) + 54px) !important;
        }

        /* â”€â”€ UNIVERSAL TEXT-OVERFLOW & CONTAINER CONTAINMENT â”€â”€ */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* Enforce strict, resilient text-wrapping everywhere */
        p, span, a, h1, h2, h3, h4, h5, h6, td, th, label, div, blockquote, dt, dd {
            overflow-wrap: break-word;
            word-break: normal;
            hyphens: none;
        }

        /* Global Container Clipping on Cards, Widgets & Modals */
        .ws-card, .scanner-card, .result-card, .soil-modal-card, .ws-stat-card,
        .ws-table-container, .ws-info-card, .result-box, .soil-modal-grid-card,
        .soil-summary-box, .agronomic-card-box {
            overflow: hidden;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Multi-Line Text Clamping Utilities */
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .line-clamp-4 {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .line-clamp-6 {
            display: -webkit-box;
            -webkit-line-clamp: 6;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* â”€â”€ TOP NAV (LUXURY DARK FOREST GREEN PALETTE) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .ws-nav {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            z-index: 9999 !important;
            height: var(--agrico-nav-h);
            background: rgba(5, 31, 32, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .ws-nav-inner {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Logo */
        .ws-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .ws-logo-text h1 {
            font-size: 22px; font-weight: 900;
            color: #DAF1DE !important; margin: 0; line-height: 1;
            letter-spacing: -0.02em;
            transition: color 0.3s ease;
        }
        .ws-logo-text p {
            font-size: 10.5px; color: #8EB69B !important;
            font-weight: 600; margin: 2px 0 0 0;
            transition: color 0.3s ease;
        }

        /* Desktop nav links */
        .ws-nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .ws-nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            color: #DAF1DE;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            border: 1.5px solid transparent;
        }
        .ws-nav-link:hover {
            background: rgba(35, 83, 71, 0.45);
            color: #ffffff;
            border-color: rgba(142, 182, 155, 0.4);
        }
        .ws-nav-link.active {
            background: linear-gradient(135deg, #235347, #163832) !important;
            color: #DAF1DE !important;
            font-weight: 700 !important;
            border: 1.5px solid #8EB69B !important;
            box-shadow: 0 4px 14px rgba(35, 83, 71, 0.5) !important;
        }
        .ws-nav-link svg { opacity: 0.85; flex-shrink: 0; transition: stroke 0.3s ease; }
        .ws-nav-link.active svg { opacity: 1; }

        /* Auth buttons */
        .ws-nav-auth {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .ws-btn-login {
            padding: 8px 20px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 700;
            color: #DAF1DE;
            background: rgba(35, 83, 71, 0.45);
            border: 1.5px solid #8EB69B;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .ws-btn-login:hover {
            background: #235347;
            border-color: #DAF1DE;
            box-shadow: 0 4px 16px rgba(35, 83, 71, 0.5);
            transform: translateY(-1px);
        }

        /* ── CIRCULAR REVEAL THEME TRANSITION TOKENS ── */
        :root {
            --theme-reveal-duration: 300ms;
            --theme-reveal-ease: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* View Transitions API Root Clipping Configuration */
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none !important;
            mix-blend-mode: normal !important;
        }

        ::view-transition-old(root) {
            z-index: 1 !important;
        }

        ::view-transition-new(root) {
            z-index: 9999 !important;
        }

        /* Fallback Circular Overlay for browsers lacking View Transitions */
        .theme-reveal-fallback-overlay {
            position: fixed;
            top: 0;
            right: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            transform: translate(50%, -50%) scale(0);
            transform-origin: center center;
            pointer-events: none;
            z-index: 999999;
            transition: transform var(--theme-reveal-duration, 700ms) var(--theme-reveal-ease, cubic-bezier(0.4, 0, 0.2, 1));
            will-change: transform;
        }



        /* ── LIGHT MODE HEADER OVERRIDES (MATCHING IMAGE 17) ── */
        html.light-mode .ws-nav {
            background: rgba(255, 255, 255, 0.96) !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06) !important;
        }
        html.light-mode .ws-logo-text h1 {
            color: #22c55e !important;
        }
        html.light-mode .ws-logo-text p {
            color: #64748b !important;
        }
        html.light-mode .ws-nav-link {
            color: #475569;
        }
        html.light-mode .ws-nav-link:hover {
            background: #f0fdf4;
            color: #16a34a;
            border-color: rgba(34, 197, 94, 0.3);
        }
        html.light-mode .ws-nav-link.active {
            background: #eefbe7 !important;
            color: #16a34a !important;
            border: 1.5px solid #86efac !important;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .ws-nav-link.active svg {
            stroke: #16a34a !important;
        }
        html.light-mode .ws-btn-login {
            background: #22c55e !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
        }
        html.light-mode .ws-btn-login:hover {
            background: #16a34a !important;
            border: none !important;
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.45) !important;
            transform: translateY(-1px) !important;
        }
        .ws-btn-admin {
            border: 1.5px solid #8EB69B;
            color: #DAF1DE;
            background: rgba(35, 83, 71, 0.45);
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.25s ease;
        }
        .ws-btn-admin:hover {
            background: #235347;
            border-color: #DAF1DE;
        }
        html.light-mode .ws-btn-admin {
            background: #22c55e !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
        }
        html.light-mode .ws-btn-admin:hover {
            background: #16a34a !important;
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.45) !important;
            transform: translateY(-1px) !important;
        }

        html.light-mode .ws-mobile-menu {
            background: rgba(255, 255, 255, 0.98);
            border-bottom: 1px solid #f1f5f9;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        /* User Avatar Circle */
        .ws-user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0B2B26, #235347);
            color: #fff;
            font-weight: 800;
            font-size: 13.5px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(35, 83, 71, 0.3);
            border: 1px solid #8EB69B;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }
        html.light-mode .ws-user-avatar {
            background: #22c55e !important;
            border: 1.5px solid #22c55e !important;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.25) !important;
            color: #ffffff !important;
        }

        /* Logout Button */
        .ws-btn-logout {
            padding: 6px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #ef4444 !important;
            background: transparent;
            border: 1.5px solid #ef4444;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-block;
        }
        .ws-btn-logout:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
        }
        html.light-mode .ws-btn-logout {
            background: #fef2f2;
            color: #ef4444 !important;
            border-color: rgba(239, 68, 68, 0.4);
        }
        html.light-mode .ws-btn-logout:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444;
        }

        /* Hamburger span in Light Mode */
        html.light-mode .ws-hamburger span {
            background: #0f172a;
        }

        /* Smooth Page Transition Animation */
        @keyframes wsPageFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        main, .ws-page-hero, .scanner-section, .ws-map-card, .munsell-wrapper, .faq-wrapper, .profile-wrapper {
            animation: wsPageFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        /* Hamburger (mobile) */
        .ws-hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
        }
        .ws-hamburger span {
            display: block;
            width: 22px; height: 2px;
            background: #DAF1DE;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Mobile menu (Transparent Glassmorphism) */
        .ws-mobile-menu {
            display: none;
            position: fixed;
            top: var(--agrico-nav-h);
            left: 0; right: 0;
            max-height: calc(100dvh - var(--agrico-nav-h));
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            background: var(--nav-bg, rgba(5, 31, 32, 0.95));
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--nav-border, rgba(142, 182, 155, 0.2));
            box-shadow: var(--shadow-nav, 0 16px 40px rgba(0, 0, 0, 0.4));
            z-index: 99;
            padding: 16px;
            flex-direction: column;
            gap: 4px;
            transition: background 0.3s ease;
        }
        .ws-mobile-menu.open { display: flex; }
        .ws-mobile-menu .ws-nav-link {
            padding: 12px 16px;
            font-size: 15px;
            border-radius: 12px;
        }
        .ws-mobile-menu .ws-nav-auth {
            margin-top: 8px;
            flex-direction: column;
        }
        .ws-mobile-menu .ws-btn-login {
            width: 100%;
            text-align: center;
            padding: 12px;
        }

        /* â”€â”€ PAGE CONTENT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .ws-main { min-height: calc(100vh - var(--agrico-nav-h)); }

        /* â”€â”€ PAGE HERO (COMPACT GREEN HERO BANNER) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .ws-page-hero {
            background: linear-gradient(135deg, var(--hero-bg-start, #051F20) 0%, var(--hero-bg-end, #0B2B26) 100%);
            padding: 16px 24px 14px;
            border-bottom: 1px solid var(--border-color, rgba(142, 182, 155, 0.2));
            flex-shrink: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            transition: background 0.3s ease;
        }
        .ws-page-hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        .ws-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-secondary, #8EB69B);
            margin-bottom: 4px;
            font-weight: 500;
        }
        .ws-breadcrumb a {
            color: var(--text-primary, #DAF1DE);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        .ws-breadcrumb a:hover {
            text-decoration: underline;
            color: var(--text-secondary, #8EB69B);
        }
        .ws-breadcrumb span {
            color: var(--text-primary, #DAF1DE);
            font-weight: 700;
        }
        .ws-page-title {
            font-size: clamp(1.3rem, 2.5vw, 1.75rem);
            font-weight: 900;
            color: var(--text-primary, #DAF1DE);
            margin: 0 0 2px 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .ws-page-sub {
            font-size: 13px;
            color: var(--text-secondary, #8EB69B);
            margin: 0;
            font-weight: 400;
        }

        /* â”€â”€ LIGHT MODE: Page Hero â”€â”€â”€â”€ */
        html.light-mode .ws-page-hero {
            background: linear-gradient(135deg, #DAF1DE 0%, #EEF7F1 100%) !important;
            border-bottom: 1px solid rgba(142, 182, 155, 0.4) !important;
            box-shadow: 0 2px 12px rgba(5, 31, 32, 0.05) !important;
        }
        html.light-mode .ws-page-title { color: #051F20 !important; }
        html.light-mode .ws-page-sub   { color: #163832 !important; font-weight: 500 !important; }
        html.light-mode .ws-breadcrumb { color: #235347 !important; font-weight: 600 !important; }
        html.light-mode .ws-breadcrumb a { color: #051F20 !important; }
        html.light-mode .ws-breadcrumb span { color: #051F20 !important; }

        /* â”€â”€ FOOTER (DARK MODE DEFAULT) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        .ws-footer {
            background: linear-gradient(180deg, #0B2B26 0%, #051F20 100%);
            color: #DAF1DE;
            border-top-left-radius: 40px;
            border-top-right-radius: 40px;
            padding: 56px 40px 0 40px;
            margin-top: auto;
            position: relative;
            overflow: hidden;
            box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.5);
            border-top: 1px solid rgba(142, 182, 155, 0.22);
            transition: all 0.3s ease;
        }
        .ws-footer-watermark {
            position: absolute;
            bottom: -20px;
            left: -10px;
            font-size: 150px;
            font-weight: 900;
            color: rgba(142, 182, 155, 0.035);
            letter-spacing: -0.04em;
            pointer-events: none; user-select: none; z-index: 1; font-family: inherit;
            transition: color 0.3s ease;
        }
        .ws-footer-inner {
            max-width: 1280px; margin: 0 auto;
            display: grid; grid-template-columns: 2fr 1fr 1fr 1.2fr; gap: 40px;
            position: relative; z-index: 2; padding-bottom: 48px;
        }
        .ws-footer-brand-title {
            font-size: 22px; font-weight: 900; color: #DAF1DE; letter-spacing: -0.02em;
            transition: color 0.3s ease;
        }
        .ws-footer-brand-desc {
            font-size: 14px; color: #8EB69B; font-weight: 500; line-height: 1.65; margin: 0; max-width: 280px;
            transition: color 0.3s ease;
        }
        .ws-footer h3 {
            font-size: 15px; font-weight: 800; color: #DAF1DE; margin: 0 0 18px 0; letter-spacing: -0.01em;
            transition: color 0.3s ease;
        }
        .ws-footer a {
            display: block;
            width: fit-content;
            color: #8EB69B;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .ws-footer a:hover {
            color: #FFFFFF !important;
            font-weight: 700 !important;
            transform: translateX(6px) !important;
            text-shadow: 0 2px 10px rgba(218, 241, 222, 0.3) !important;
        }
        .ws-footer-bottom {
            width: calc(100% + 80px); margin-left: -40px; margin-right: -40px; padding: 22px 40px;
            background: #051F20; border-top: 1px solid rgba(142, 182, 155, 0.15);
            position: relative; z-index: 2; box-sizing: border-box;
            transition: all 0.3s ease;
        }
        .ws-footer-bottom-inner {
            max-width: 1280px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; font-size: 13.5px; color: #8EB69B; font-weight: 600;
        }
        .ws-footer-bottom-links a {
            display: inline-block; margin-left: 24px; margin-bottom: 0; color: #DAF1DE; font-weight: 700; text-decoration: none; transition: color 0.2s ease;
        }
        .ws-footer-bottom-links a:hover {
            text-decoration: underline; color: #4ADE80;
        }

        /* â”€â”€ FOOTER (LIGHT MODE OVERRIDES - ADMIN MATCHED) â”€â”€ */
        html.light-mode .ws-footer {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            color: #0f172a;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
        }
        html.light-mode .ws-footer-watermark {
            color: rgba(34, 197, 94, 0.035);
        }
        html.light-mode .ws-footer-brand-title {
            color: #22c55e !important;
        }
        html.light-mode .ws-footer-brand-desc {
            color: #64748b;
            font-weight: 500;
        }
        html.light-mode .ws-footer h3 {
            color: #0f172a;
            font-weight: 800;
        }
        html.light-mode .ws-footer a {
            color: #475569;
            font-weight: 600;
        }
        html.light-mode .ws-footer a:hover {
            color: #16a34a !important;
            font-weight: 800 !important;
            transform: translateX(6px) !important;
            text-shadow: 0 2px 8px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .ws-footer-bottom {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }
        html.light-mode .ws-footer-bottom-inner {
            color: #64748b;
        }
        html.light-mode .ws-footer-bottom-links a {
            color: #334155;
            font-weight: 700;
        }
        html.light-mode .ws-footer-bottom-links a:hover {
            color: #22c55e;
        }

        @media (max-width: 900px) {
            .ws-nav-links { display: none; }
            .ws-hamburger { display: flex; }
            .ws-footer-inner { grid-template-columns: 1fr 1fr; gap: 32px; }
        }
        @media (max-width: 768px) {
            html {
                scrollbar-gutter: auto !important;
            }
            html, body {
                overflow-x: hidden !important;
                max-width: 100vw !important;
                width: 100% !important;
            }
            .ws-main {
                overflow-x: hidden !important;
                max-width: 100vw !important;
                width: 100% !important;
            }
        }
        @media (max-width: 600px) {
            .ws-footer { padding: 40px 24px 0 24px; border-top-left-radius: 28px; border-top-right-radius: 28px; }
            .ws-footer-inner { grid-template-columns: 1fr; gap: 28px; }
            .ws-footer-bottom { width: calc(100% + 48px); margin-left: -24px; margin-right: -24px; padding: 20px 24px; }
            .ws-footer-bottom-inner { flex-direction: column; align-items: flex-start; gap: 10px; }
            .ws-footer-bottom-links a { margin-left: 0; margin-right: 20px; }
            .ws-nav-auth { display: none; }
        }

        /* ── ACCOUNT APPROVAL PENDING NOTIFICATION BANNER ── */
        .ws-pending-notification {
            width: 100%;
            background: linear-gradient(90deg, rgba(16, 42, 37, 0.98) 0%, rgba(22, 56, 50, 0.95) 50%, rgba(16, 42, 37, 0.98) 100%);
            border-bottom: 1px solid rgba(245, 158, 11, 0.35);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.28), inset 0 1px 0 rgba(245, 158, 11, 0.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            position: fixed !important;
            top: var(--agrico-nav-h, 68px) !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 9998 !important;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .ws-pending-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-sizing: border-box;
        }
        .ws-pending-main {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 0;
        }
        .ws-pending-icon-chip {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(245, 158, 11, 0.14);
            border: 1.5px solid rgba(245, 158, 11, 0.45);
            color: #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0 !important;
            box-shadow: 0 0 14px rgba(245, 158, 11, 0.2);
            box-sizing: border-box;
        }
        .ws-pending-icon-chip svg {
            width: 18px;
            height: 18px;
            stroke: #fbbf24;
            flex-shrink: 0 !important;
            display: block;
        }
        .ws-pending-text {
            font-size: 13.5px;
            line-height: 1.5;
            color: #DAF1DE;
            margin: 0;
            word-break: normal;
            overflow-wrap: break-word;
        }
        .ws-pending-title {
            color: #fbbf24;
            font-weight: 800;
            letter-spacing: -0.01em;
            margin-right: 4px;
        }
        .ws-pending-desc {
            font-weight: 500;
            color: #d1fae5;
        }
        .ws-pending-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .ws-pending-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            color: #fbbf24;
            background: rgba(245, 158, 11, 0.14);
            border: 1.2px solid rgba(245, 158, 11, 0.4);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            user-select: none;
        }
        .ws-pending-action-btn:hover {
            background: #f59e0b;
            color: #051F20;
            border-color: #f59e0b;
            box-shadow: 0 0 16px rgba(245, 158, 11, 0.45);
            transform: translateY(-1px);
        }
        .ws-pending-action-btn:active {
            transform: translateY(0);
        }

        /* Light Mode Styling */
        html.light-mode .ws-pending-notification {
            background: linear-gradient(90deg, #fffbeb 0%, #fef3c7 50%, #fffbeb 100%);
            border-bottom: 1px solid #fde68a;
            box-shadow: 0 2px 10px rgba(217, 119, 6, 0.1);
        }
        html.light-mode .ws-pending-icon-chip {
            background: #fde68a;
            border-color: #f59e0b;
            color: #b45309;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.15);
        }
        html.light-mode .ws-pending-icon-chip svg {
            stroke: #b45309;
        }
        html.light-mode .ws-pending-text {
            color: #78350f;
        }
        html.light-mode .ws-pending-title {
            color: #92400e;
        }
        html.light-mode .ws-pending-desc {
            color: #854d0e;
        }
        html.light-mode .ws-pending-action-btn {
            background: #ffffff;
            border-color: #f59e0b;
            color: #92400e;
            box-shadow: 0 2px 6px rgba(217, 119, 6, 0.12);
        }
        html.light-mode .ws-pending-action-btn:hover {
            background: #f59e0b;
            color: #ffffff;
            border-color: #f59e0b;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        }

        /* Responsive Breakpoints (Mobile & Tablet) */
        @media (max-width: 820px) {
            .ws-pending-inner {
                padding: 10px 16px;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            .ws-pending-main {
                align-items: flex-start;
                gap: 12px;
            }
            .ws-pending-icon-chip {
                width: 30px;
                height: 30px;
                border-radius: 8px;
                margin-top: 2px;
                flex-shrink: 0 !important;
            }
            .ws-pending-icon-chip svg {
                width: 16px;
                height: 16px;
                flex-shrink: 0 !important;
            }
            .ws-pending-text {
                font-size: 12.5px;
                line-height: 1.48;
                text-align: left;
            }
            .ws-pending-title {
                font-size: 13px;
                display: inline;
            }
            .ws-pending-actions {
                width: 100%;
            }
            .ws-pending-action-btn {
                width: 100%;
                justify-content: center;
                padding: 8px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body class="{{ $isPending ? 'has-pending-banner' : '' }}">

{{-- â”€â”€ TOP NAVIGATION â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<nav class="ws-nav" id="wsNav">
    <div class="ws-nav-inner">
        @php
            $wsUser = Auth::guard('web')->user() ?? Auth::user() ?? Auth::guard('admin')->user();
            if (!$wsUser && session()->has('user_id')) {
                $wsUser = \App\Models\User::where('user_id', session('user_id'))->first();
            }
            if (!$wsUser && session()->has('admin_id')) {
                $wsUser = \App\Models\User::where('user_id', session('admin_id'))->first();
            }
            $isWsAuth = (bool) $wsUser;
            $isWsAdmin = $isWsAuth && (in_array(strtolower($wsUser->role ?? ''), ['admin', 'superadmin', 'super admin']) || Auth::guard('admin')->check());
            $isApproved = $isWsAuth && ($isWsAdmin || in_array(strtolower(trim($wsUser->status ?? '')), ['active', 'approved']));
            $isPending  = $isWsAuth && !$isApproved;
        @endphp

        {{-- Logo --}}
        <a href="{{ route('website.home') }}" class="ws-logo">
            <img src="{{ asset('images/agrico-logo-dark.png') }}" class="logo-dark" style="width:48px; height:48px; object-fit:contain; flex-shrink:0;" alt="AgriCo">
            <img src="{{ asset('images/agrico-logo.png') }}" class="logo-light" style="width:48px; height:48px; object-fit:contain; flex-shrink:0;" alt="AgriCo">
            <div class="ws-logo-text">
                <h1>AgriCo</h1>
                <p>Soil Intelligence</p>
            </div>
        </a>

        {{-- Desktop Nav Links --}}
        <div class="ws-nav-links">
            @php
                $wsRoute = request()->routeIs('website.*') ? request()->route()->getName() : '';
            @endphp

            <a href="{{ route('website.home') }}"
               class="ws-nav-link {{ $wsRoute === 'website.home' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Home
            </a>

            <a href="{{ route('website.scan') }}"
               @if($isPending) onclick="event.preventDefault(); showAccountPendingModal('Account Pending Approval', 'Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.');"
               @elseif(!$isWsAuth) onclick="event.preventDefault(); showGlobalLoginModal('scan');" @endif
               class="ws-nav-link {{ $wsRoute === 'website.scan' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                Scan
            </a>

            <a href="{{ route('website.map') }}"
               class="ws-nav-link {{ $wsRoute === 'website.map' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                    <line x1="8" y1="2" x2="8" y2="18"/>
                    <line x1="16" y1="6" x2="16" y2="22"/>
                </svg>
                Map
            </a>

            <a href="{{ route('website.munsell') }}"
               class="ws-nav-link {{ $wsRoute === 'website.munsell' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                Munsell Color Chart
            </a>

            <a href="{{ route('website.faq') }}"
               class="ws-nav-link {{ $wsRoute === 'website.faq' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                FAQ
            </a>

            <a href="{{ route('website.profile') }}"
               @if(!$isWsAuth) onclick="event.preventDefault(); showGlobalLoginModal('profile');" @endif
               class="ws-nav-link {{ $wsRoute === 'website.profile' ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Profile
            </a>
        </div>

        {{-- Auth Buttons, Theme Toggle & Hamburger Grouped Tightly --}}
        <div class="ws-nav-right" style="display:flex;align-items:center;gap:8px;">
            <div class="ws-nav-auth">
                @if($isWsAuth)
                    <div style="display:flex;align-items:center;gap:8px;">
                        <a href="{{ route('website.profile') }}" style="display:flex;align-items:center;gap:6px;text-decoration:none;" title="{{ $wsUser->name ?? $wsUser->full_name ?? 'User Profile' }}">
                            <div class="ws-user-avatar">
                                {{ strtoupper(substr($wsUser->name ?? $wsUser->full_name ?? 'U', 0, 1)) }}
                            </div>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;display:inline;">
                            @csrf
                            <button type="submit" class="ws-btn-logout">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="ws-btn-login">Login</a>
                @endif
            </div>


            {{-- Hamburger --}}
            <button class="ws-hamburger" id="wsHamburger" aria-label="Toggle menu" type="button">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

{{-- Mobile Menu --}}
<div class="ws-mobile-menu" id="wsMobileMenu">
    @php $wsRoute = request()->routeIs('website.*') ? request()->route()->getName() : ''; @endphp

    <a href="{{ route('website.home') }}"    class="ws-nav-link {{ $wsRoute === 'website.home' ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Home
    </a>
    <a href="{{ route('website.scan') }}"
       @if($isPending) onclick="event.preventDefault(); showAccountPendingModal('Account Pending Approval', 'Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.');"
       @elseif(!$isWsAuth) onclick="event.preventDefault(); showGlobalLoginModal('scan');" @endif
       class="ws-nav-link {{ $wsRoute === 'website.scan' ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        Scan
    </a>
    <a href="{{ route('website.map') }}"
       class="ws-nav-link {{ $wsRoute === 'website.map' ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        Map
    </a>
    <a href="{{ route('website.munsell') }}" class="ws-nav-link {{ $wsRoute === 'website.munsell' ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
        Munsell Color Chart
    </a>
    <a href="{{ route('website.faq') }}"     class="ws-nav-link {{ $wsRoute === 'website.faq' ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        FAQ
    </a>
    <a href="{{ route('website.profile') }}"
       @if(!$isWsAuth) onclick="event.preventDefault(); showGlobalLoginModal('profile');" @endif
       class="ws-nav-link {{ $wsRoute === 'website.profile' ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profile
    </a>
    <div class="ws-nav-auth" style="display:flex;flex-direction:column;gap:8px;">
        @if($isWsAuth)
            @if($isWsAdmin)
                <a href="{{ route('dashboard') }}" target="_blank" rel="noopener noreferrer" class="ws-nav-link" style="color:#22c55e;font-weight:700;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Admin Panel
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="width:100%;margin:0;">
                @csrf
                <button type="submit" class="ws-btn-login"
                        style="width:100%;text-align:center;border-color:#fca5a5;color:#ef4444;">
                    Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="ws-btn-login" style="width:100%;text-align:center;">Login</a>
        @endif
    </div>
</div>

@if($isWsAuth && (strtolower($wsUser->status ?? '') === 'pending' || $isPending))
    <div class="ws-pending-notification" role="alert" aria-live="polite">
        <div class="ws-pending-inner">
            <div class="ws-pending-main">
                <div class="ws-pending-icon-chip" aria-hidden="true">
                    <svg fill="none" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <p class="ws-pending-text">
                    <strong class="ws-pending-title">Account Approval Pending:</strong><span class="ws-pending-desc">Your registration has been submitted and is pending Admin approval. Full website functionality will be unlocked once an administrator approves your request.</span>
                </p>
            </div>
            <div class="ws-pending-actions">
                <button type="button" class="ws-pending-action-btn" onclick="showAccountPendingModal('Account Pending Approval', 'Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.')">
                    <span>View Details</span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif

{{-- â”€â”€ MAIN CONTENT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<main class="ws-main">
    {{ $slot }}
</main>

{{-- â”€â”€ FOOTER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
@if(!isset($hideFooter) || !$hideFooter)
<footer class="ws-footer">
    {{-- Large Faint Watermark Text --}}
    <div class="ws-footer-watermark">AGRICO</div>

    <div class="ws-footer-inner">
        {{-- Brand Column --}}
        <div>
            <div class="ws-footer-brand" style="margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <img src="{{ asset('images/agrico-logo-dark.png') }}" class="logo-dark" style="width: 36px; height: 36px; object-fit: contain; flex-shrink: 0;" alt="AgriCo">
                <img src="{{ asset('images/agrico-logo.png') }}" class="logo-light" style="width: 36px; height: 36px; object-fit: contain; flex-shrink: 0;" alt="AgriCo">
                <span class="ws-footer-brand-title">AgriCo</span>
            </div>
            <p class="ws-footer-brand-desc">
                Building systems that work for precision soil analysis and smarter agricultural decisions.
            </p>
        </div>

        {{-- Company --}}
        <div>
            <h3>Company</h3>
            <a href="{{ route('website.home') }}">About</a>
            <a href="{{ route('website.home') }}#features">Our Approach</a>
            <a href="{{ route('website.scan') }}">Our Solutions</a>
            <a href="{{ route('website.faq') }}">Careers & Support</a>
        </div>

        {{-- Services --}}
        <div>
            <h3>Services</h3>
            <a href="{{ route('website.scan') }}">Automate Scanning</a>
            <a href="{{ route('website.munsell') }}">Munsell Color Database</a>
            <a href="{{ route('website.map') }}">Automate Mapping</a>
            <a href="{{ route('website.scan') }}">Agronomic Analysis</a>
            <a href="{{ route('website.munsell') }}">Spectral Systems</a>
        </div>

        {{-- Projects --}}
        <div>
            <h3>Projects</h3>
            <a href="{{ route('website.scan') }}">Soil Scan History</a>
            <a href="{{ route('website.map') }}">GIS Spatial Mapping</a>
            <a href="{{ route('website.munsell') }}">10R & 2.5YR Charts</a>
            <a href="{{ route('website.munsell') }}">5YR & 7.5YR Charts</a>
            <a href="{{ route('website.munsell') }}">10YR & 2.5Y Charts</a>
        </div>
    </div>

    {{-- Footer Bottom Sub-Bar --}}
    <div class="ws-footer-bottom">
        <div class="ws-footer-bottom-inner">
            <div>Â© {{ date('Y') }} AGRICO. All rights reserved.</div>
            <div class="ws-footer-bottom-links">
                <a href="{{ route('website.privacy') }}">Privacy Policy</a>
                <a href="{{ route('website.terms') }}">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
@endif


@livewireScripts
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419) {
                    preventDefault();
                    window.location.reload();
                }
            });
        });
    });
</script>


{{-- Global Login Required Modal for Guest Interceptions --}}
@guest
<div id="globalLoginRequiredModal" class="global-login-modal-overlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(5,31,32,0.82); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); align-items:center; justify-content:center; padding:16px;">
    <div class="global-login-modal-card">
        {{-- Close "X" Exit Button --}}
        <x-close-button onclick="closeGlobalLoginModal()" style="position: absolute; top: 16px; right: 16px; z-index: 10;" title="Close modal" ariaLabel="Close modal" />

        {{-- Shield Icon --}}
        <div class="global-login-icon-box">
            <svg width="34" height="34" fill="none" stroke="#22c55e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <h3 class="global-login-title">Login Required</h3>
        <p id="globalLoginModalNotice" class="global-login-notice">You need to be logged in to access the Interactive Soil Map features. Would you like to log in to your account now?</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button type="button" class="global-login-btn-secondary" onclick="closeGlobalLoginModal()">No, Thanks</button>
            <a href="{{ route('login') }}" class="global-login-btn-primary">Yes, Log In</a>
        </div>
    </div>
</div>
<style>
    /* Dark Mode (Default) */
    .global-login-modal-card {
        background: linear-gradient(145deg, #0B2B26 0%, #051F20 100%);
        border-radius: 28px;
        padding: 38px 32px 30px;
        max-width: 440px;
        width: 90%;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.65), 0 0 40px rgba(142, 182, 155, 0.08);
        border: 1px solid rgba(142, 182, 155, 0.3);
        text-align: center;
        animation: globalLoginModalIn 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        opacity: 1;
        position: relative;
        z-index: 100000;
    }
    .global-login-icon-box {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.18), rgba(22, 56, 50, 0.85));
        border: 1.5px solid rgba(142, 182, 155, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }
    .global-login-title {
        font-size: 21px;
        font-weight: 800;
        color: #DAF1DE;
        margin: 0 0 8px;
        letter-spacing: -0.01em;
    }
    .global-login-notice {
        font-size: 14px;
        color: #8EB69B;
        margin: 0 0 28px;
        line-height: 1.6;
        font-weight: 500;
    }
    .global-login-btn-secondary {
        flex: 1;
        padding: 12px 24px;
        border-radius: 14px;
        border: 1.5px solid rgba(142, 182, 155, 0.3);
        background: #051F20;
        color: #8EB69B;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .global-login-btn-secondary:hover {
        background: #163832;
        color: #DAF1DE;
        border-color: #8EB69B;
    }
    .global-login-btn-primary {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 14px;
        border: 1px solid #8EB69B;
        background: linear-gradient(135deg, #235347, #163832);
        color: #DAF1DE;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(35, 83, 71, 0.45);
        transition: all 0.25s ease;
    }
    .global-login-btn-primary:hover {
        background: linear-gradient(135deg, #163832, #0B2B26);
        border-color: #DAF1DE;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 22px rgba(35, 83, 71, 0.65);
    }

    /* Light Mode Overrides */
    html.light-mode #globalLoginRequiredModal {
        background: rgba(15, 23, 42, 0.45) !important;
    }
    html.light-mode .global-login-modal-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.18) !important;
    }
    html.light-mode .global-login-icon-box {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7) !important;
        border: 1px solid #bbf7d0 !important;
        box-shadow: 0 4px 14px rgba(34, 197, 94, 0.12) !important;
    }
    html.light-mode .global-login-title {
        color: #0f172a !important;
    }
    html.light-mode .global-login-notice {
        color: #64748b !important;
    }
    html.light-mode .global-login-btn-secondary {
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        color: #334155 !important;
    }
    html.light-mode .global-login-btn-secondary:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    html.light-mode .global-login-btn-primary {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        border: 1px solid #16a34a !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
    }
    html.light-mode .global-login-btn-primary:hover {
        background: linear-gradient(135deg, #16a34a, #15803d) !important;
        box-shadow: 0 6px 20px rgba(34, 197, 94, 0.45) !important;
        color: #ffffff !important;
    }

    @keyframes globalLoginModalIn {
        from { transform: scale(0.94) translateY(10px); opacity: 0; }
        to   { transform: scale(1) translateY(0); opacity: 1; }
    }

    /* â”€â”€ Universal Modal Close / Exit Circle Button â”€â”€ */

</style>
@endguest

{{-- Global Account Pending Approval Modal --}}
<div id="globalAccountPendingModal" class="global-pending-modal-overlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(5,31,32,0.85); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); align-items:center; justify-content:center; padding:16px;">
    <div class="global-pending-modal-card">
        {{-- Close "X" Exit Button --}}
        <x-close-button onclick="closeAccountPendingModal()" style="position: absolute; top: 16px; right: 16px; z-index: 10;" title="Close modal" ariaLabel="Close modal" />

        {{-- Glowing Amber / Clock Shield Icon --}}
        <div class="global-pending-icon-box">
            <svg width="34" height="34" fill="none" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <h3 id="globalPendingModalTitle" class="global-pending-title">Account Pending Approval</h3>
        <p id="globalPendingModalNotice" class="global-pending-notice">Your account is awaiting admin approval. Scanning and Map access will be enabled once approved.</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button type="button" class="global-pending-btn-secondary" onclick="closeAccountPendingModal()">Understood</button>
            <a href="{{ route('website.profile') }}" class="global-pending-btn-primary">View Profile</a>
        </div>
    </div>
</div>

{{-- Global System Maintenance & Updating Modal --}}
<div id="globalSystemMaintenanceModal" class="global-pending-modal-overlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(5,31,32,0.85); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); align-items:center; justify-content:center; padding:16px;">
    <div class="global-pending-modal-card" style="border-color:rgba(234, 88, 12, 0.45); box-shadow:0 30px 80px rgba(0,0,0,0.65), 0 0 40px rgba(234, 88, 12, 0.15);">
        {{-- Close "X" Exit Button --}}
        <x-close-button onclick="closeSystemMaintenanceModal()" style="position: absolute; top: 16px; right: 16px; z-index: 10;" title="Close modal" ariaLabel="Close modal" />

        {{-- Glowing Orange / Gear Wrench Icon --}}
        <div class="global-pending-icon-box" style="background:linear-gradient(135deg, rgba(234, 88, 12, 0.2), rgba(22, 56, 50, 0.85)); border-color:rgba(234, 88, 12, 0.5);">
            <svg width="34" height="34" fill="none" stroke="#ea580c" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
        </div>
        <h3 id="globalMaintenanceModalTitle" class="global-pending-title">System Updating & Maintenance</h3>
        <p id="globalMaintenanceModalNotice" class="global-pending-notice">AgriCo is currently updating and performing system maintenance. Application downloads are temporarily paused to ensure you receive the most stable version. Please check back shortly!</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button type="button" class="global-pending-btn-secondary" style="flex:initial; min-width:140px; padding:12px 28px;" onclick="closeSystemMaintenanceModal()">Understood</button>
        </div>
    </div>
</div>
<style>
    /* Account Pending Approval Modal (Amber & Dark Forest) */
    .global-pending-modal-card {
        background: linear-gradient(145deg, #0B2B26 0%, #051F20 100%);
        border-radius: 28px;
        padding: 38px 32px 30px;
        max-width: 440px;
        width: 90%;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.65), 0 0 40px rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.35);
        text-align: center;
        animation: globalLoginModalIn 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        opacity: 1;
        position: relative;
        z-index: 100000;
    }
    .global-pending-icon-box {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.18), rgba(22, 56, 50, 0.85));
        border: 1.5px solid rgba(245, 158, 11, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }
    .global-pending-title {
        font-size: 21px;
        font-weight: 800;
        color: #DAF1DE;
        margin: 0 0 8px;
        letter-spacing: -0.01em;
    }
    .global-pending-notice {
        font-size: 14px;
        color: #8EB69B;
        margin: 0 0 28px;
        line-height: 1.6;
        font-weight: 500;
    }
    .global-pending-btn-secondary {
        flex: 1;
        padding: 12px 24px;
        border-radius: 14px;
        border: 1.5px solid rgba(142, 182, 155, 0.3);
        background: #051F20;
        color: #8EB69B;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .global-pending-btn-secondary:hover {
        background: #163832;
        color: #DAF1DE;
        border-color: #8EB69B;
    }
    .global-pending-btn-primary {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 14px;
        border: 1px solid rgba(245, 158, 11, 0.5);
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #ffffff;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(245, 158, 11, 0.35);
        transition: all 0.25s ease;
    }
    .global-pending-btn-primary:hover {
        background: linear-gradient(135deg, #b45309, #92400e);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 22px rgba(245, 158, 11, 0.5);
    }

    /* Light Mode Overrides */
    html.light-mode #globalAccountPendingModal,
    html.light-mode #globalSystemMaintenanceModal {
        background: rgba(15, 23, 42, 0.45) !important;
    }
    html.light-mode .global-pending-modal-card {
        background: #ffffff !important;
        border: 1px solid #fed7aa !important;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.18) !important;
    }
    html.light-mode .global-pending-icon-box {
        background: linear-gradient(135deg, #fffbeb, #fef3c7) !important;
        border: 1px solid #fde68a !important;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.15) !important;
    }
    html.light-mode .global-pending-title {
        color: #0f172a !important;
    }
    html.light-mode .global-pending-notice {
        color: #64748b !important;
    }
    html.light-mode .global-pending-btn-secondary {
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        color: #334155 !important;
    }
    html.light-mode .global-pending-btn-secondary:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    html.light-mode .global-pending-btn-primary {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        border: 1px solid #d97706 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35) !important;
    }
    html.light-mode .global-pending-btn-primary:hover {
        background: linear-gradient(135deg, #d97706, #b45309) !important;
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.45) !important;
        color: #ffffff !important;
    }

    @media (max-width: 640px) {
        .global-login-modal-card,
        .global-pending-modal-card {
            width: 100% !important;
            max-width: min(calc(100vw - 32px), 420px) !important;
            height: auto !important;
            max-height: min(80vh, 590px) !important;
            border-radius: 24px !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            margin: auto !important;
            padding: 24px 20px 20px !important;
        }
    }
</style>

<script>
    function showGlobalLoginModal(feature = 'map') {
        const modal = document.getElementById('globalLoginRequiredModal');
        const notice = document.getElementById('globalLoginModalNotice');
        if (feature === 'map') {
            if (notice) notice.innerText = 'You need to be logged in to access the Interactive Soil Map features. Would you like to log in to your account now?';
        } else if (feature === 'scan' || feature === 'history') {
            if (notice) notice.innerText = 'Please log in to perform a scan and view scan history. Would you like to log in to your account now?';
        } else if (feature === 'profile') {
            if (notice) notice.innerText = 'Please log in to view and manage your AgriCo account profile. Would you like to log in to your account now?';
        } else if (feature === 'download') {
            if (notice) notice.innerText = 'Please log in to download the AgriCo Mobile Application. Would you like to log in to your account now?';
        }
        if (modal) {
            modal.style.display = 'flex';
        } else {
            window.location.href = "{{ route('login') }}";
        }
    }
    function closeGlobalLoginModal() {
        const modal = document.getElementById('globalLoginRequiredModal');
        if (modal) modal.style.display = 'none';
    }

    function showAccountPendingModal(title = null, msg = null) {
        const modal = document.getElementById('globalAccountPendingModal');
        const titleEl = document.getElementById('globalPendingModalTitle');
        const noticeEl = document.getElementById('globalPendingModalNotice');
        if (titleEl && title) titleEl.innerText = title;
        if (noticeEl && msg) noticeEl.innerText = msg;
        if (modal) {
            modal.style.display = 'flex';
        } else if (typeof showScanNotice === 'function') {
            showScanNotice(msg || 'Your account is awaiting admin approval.');
        } else {
            alert(msg || 'Your account is awaiting admin approval.');
        }
    }
    function closeAccountPendingModal() {
        const modal = document.getElementById('globalAccountPendingModal');
        if (modal) modal.style.display = 'none';
    }

    function showSystemMaintenanceModal(title = null, msg = null) {
        const modal = document.getElementById('globalSystemMaintenanceModal');
        const titleEl = document.getElementById('globalMaintenanceModalTitle');
        const noticeEl = document.getElementById('globalMaintenanceModalNotice');
        if (titleEl && title) titleEl.innerText = title;
        if (noticeEl && msg) noticeEl.innerText = msg;
        if (modal) {
            modal.style.display = 'flex';
        } else {
            alert(msg || 'The system is currently undergoing maintenance and updates. Application downloads are temporarily paused.');
        }
    }
    function closeSystemMaintenanceModal() {
        const modal = document.getElementById('globalSystemMaintenanceModal');
        if (modal) modal.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('globalLoginRequiredModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeGlobalLoginModal();
            });
        }
        const pendingModal = document.getElementById('globalAccountPendingModal');
        if (pendingModal) {
            pendingModal.addEventListener('click', function(e) {
                if (e.target === this) closeAccountPendingModal();
            });
        }
        const maintenanceModal = document.getElementById('globalSystemMaintenanceModal');
        if (maintenanceModal) {
            maintenanceModal.addEventListener('click', function(e) {
                if (e.target === this) closeSystemMaintenanceModal();
            });
        }
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeGlobalLoginModal();
                closeAccountPendingModal();
                closeSystemMaintenanceModal();
            }
        });
    });

    // ── CIRCULAR REVEAL THEME TOGGLE (VIEW TRANSITIONS API + FALLBACK) ──
    (function() {
        const html = document.documentElement;
        let isThemeTransitioning = false;

        // Configurable duration (ms) and easing function
        const THEME_DURATION = 300;
        const THEME_EASING   = 'cubic-bezier(0.4, 0, 0.2, 1)';

        function applyThemeState(isLight) {
            html.classList.toggle('light-mode', isLight);
            html.style.colorScheme = isLight ? 'light' : 'dark';
            const themeStr = isLight ? 'light' : 'dark';
            localStorage.setItem('agrico-theme', themeStr);

            // Dispatch global event for in-page profile switches or widgets
            window.dispatchEvent(new CustomEvent('agrico:theme-changed', {
                detail: { theme: themeStr, isLight: isLight }
            }));

            @if($wsAuthUser)
                // Persist theme preference for authenticated user in database
                fetch('{{ route("user.update-theme") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ theme: themeStr })
                }).catch(function(err) {
                    console.warn('Failed to save user theme preference:', err);
                });
            @endif
        }

        window.agricoToggleTheme = function() {
            // Prevent multiple rapid clicks while animation is running
            if (isThemeTransitioning) return;

            const isCurrentlyLight = html.classList.contains('light-mode');
            const targetIsLight = !isCurrentlyLight;
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // Calculate radius from top-right corner (100% 0) to farthest corner (bottom-left 0 100%)
            const maxRadius = Math.hypot(window.innerWidth, window.innerHeight);

            // 1. Accessibility: Instant toggle if reduced motion is requested
            if (prefersReducedMotion) {
                applyThemeState(targetIsLight);
                return;
            }

            // 2. Modern View Transitions API (Chrome 111+, Edge 111+, Safari 18+)
            if (typeof document.startViewTransition === 'function') {
                isThemeTransitioning = true;
                try {
                    const transition = document.startViewTransition(() => {
                        applyThemeState(targetIsLight);
                    });

                    transition.ready.then(() => {
                        const anim = document.documentElement.animate(
                            {
                                clipPath: [
                                    'circle(0px at 100% 0px)',
                                    `circle(${maxRadius}px at 100% 0px)`
                                ]
                            },
                            {
                                duration: THEME_DURATION,
                                easing: THEME_EASING,
                                pseudoElement: '::view-transition-new(root)'
                            }
                        );

                        anim.finished.finally(() => {
                            isThemeTransitioning = false;
                        });
                    }).catch(() => {
                        isThemeTransitioning = false;
                    });
                } catch (e) {
                    applyThemeState(targetIsLight);
                    isThemeTransitioning = false;
                }
                return;
            }

            // 3. Fallback for browsers lacking View Transitions API (Firefox, Safari <18)
            isThemeTransitioning = true;
            try {
                const overlay = document.createElement('div');
                overlay.className = 'theme-reveal-fallback-overlay';
                overlay.style.backgroundColor = targetIsLight ? '#F7FBF8' : '#051F20';
                document.body.appendChild(overlay);

                // Force layout reflow before triggering scale transform
                void overlay.offsetWidth;
                overlay.style.transform = `translate(50%, -50%) scale(${(maxRadius * 2) / 20})`;

                setTimeout(() => {
                    applyThemeState(targetIsLight);
                    setTimeout(() => {
                        overlay.remove();
                        isThemeTransitioning = false;
                    }, 60);
                }, THEME_DURATION);
            } catch (err) {
                applyThemeState(targetIsLight);
                isThemeTransitioning = false;
            }
        };


    })();

    // Mobile hamburger toggle
    document.addEventListener('DOMContentLoaded', () => {
        const hamburger = document.getElementById('wsHamburger');
        const mobileMenu = document.getElementById('wsMobileMenu');

        hamburger?.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenu?.classList.toggle('open');
        });

        // Close mobile menu on outside click
        document.addEventListener('click', (e) => {
            if (mobileMenu?.classList.contains('open') && !hamburger?.contains(e.target) && !mobileMenu?.contains(e.target)) {
                mobileMenu?.classList.remove('open');
            }
        });
    });

    // Ensure no Service Worker caches HTML pages on Desktop Website
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            for (let registration of registrations) {
                registration.unregister();
            }
        });
        if ('caches' in window) {
            caches.keys().then(function(names) {
                for (let name of names) caches.delete(name);
            });
        }
    }
</script>

</body>
</html>
