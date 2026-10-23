<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifying — AgriCo</title>
<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
<link rel="icon" href="{{ asset('images/agrico-logo.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700;800;900&display=swap">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body, html { height: 100%; }
  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    max-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #DAF1DE 0%, #f6faf7 45%, #ffffff 100%) !important;
    position: relative;
    overflow: hidden;
    margin: 0;
    padding: 12px 16px;
    box-sizing: border-box;
  }

  /* Concentric Geometric Rings (Exact same as Login page) */
  .auth-arc {
    position: absolute;
    width: 640px;
    height: 640px;
    border: 1.5px solid rgba(142, 182, 155, 0.4);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 0;
    pointer-events: none;
  }
  .auth-arc::after {
    content: '';
    position: absolute;
    width: 880px;
    height: 880px;
    border: 1.5px solid rgba(187, 247, 208, 0.4);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  .auth-arc::before {
    content: '';
    position: absolute;
    width: 1120px;
    height: 1120px;
    border: 1.5px solid rgba(187, 247, 208, 0.2);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  /* ==========================================================
     RESPONSIVE BRAND HEADER (Identical to Login page, no link)
     ========================================================== */
  .auth-brand {
    position: fixed !important;
    top: 24px !important;
    left: 32px !important;
    z-index: 20 !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    text-decoration: none !important;
    max-width: 100% !important;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    cursor: default !important;
    user-select: none !important;
    -webkit-user-select: none !important;
  }
  .auth-brand img {
    width: 38px !important;
    height: 38px !important;
    object-fit: contain !important;
  }
  .auth-brand .auth-brand-text {
    font-size: 22px !important;
    font-weight: 900 !important;
    color: #22c55e !important;
    letter-spacing: -0.02em !important;
    white-space: nowrap !important;
  }
  @media (min-width: 1024px) {
    .auth-brand {
      top: 32px !important;
      left: 44px !important;
    }
    .auth-brand img {
      width: 42px !important;
      height: 42px !important;
    }
    .auth-brand .auth-brand-text {
      font-size: 24px !important;
    }
  }
  @media (max-width: 768px) {
    html, body {
      overflow-x: hidden !important;
      max-width: 100vw !important;
    }
    .auth-arc {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }
    .auth-brand {
      position: fixed !important;
      top: max(16px, env(safe-area-inset-top)) !important;
      left: 20px !important;
      margin: 0 !important;
      gap: 8px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: flex-start !important;
      z-index: 30 !important;
    }
    .auth-brand img {
      width: 34px !important;
      height: 34px !important;
    }
    .auth-brand .auth-brand-text {
      font-size: 20px !important;
    }
  }

  /* ==========================================================
     AUTH CARD (Centered with cardEntry animation matching all auth cards)
     ========================================================== */
  .auth-card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 380px;
    background: #ffffff;
    border: 1px solid rgba(218, 241, 222, 0.9);
    border-top: 4px solid #22c55e;
    border-radius: 22px;
    padding: 36px 32px;
    box-shadow: 0 20px 50px rgba(21, 128, 61, 0.08), 0 2px 10px rgba(0, 0, 0, 0.02);
    box-sizing: border-box;
    text-align: center;
    animation: cardEntry 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
  }

  @keyframes cardEntry {
    from {
      opacity: 0;
      transform: translateY(26px) scale(0.97);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  .auth-icon-badge {
    width: 44px;
    height: 44px;
    background: #f0fdf4;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px auto;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.12);
    border: 1px solid #bbf7d0;
  }
  .auth-icon-badge svg {
    width: 22px;
    height: 22px;
  }

  .auth-title {
    text-align: center;
    font-size: 1.18rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
  }

  .auth-subtitle {
    text-align: center;
    font-size: 0.8rem;
    color: #64748b;
    margin: 0 0 16px 0;
    line-height: 1.4;
    font-weight: 400;
  }

  #turnstile-widget {
    margin: 10px auto 6px auto;
    min-height: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .verify-status {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
  }
  .verify-shimmer-bar {
    position: relative;
    overflow: hidden;
    width: 60px;
    height: 6px;
    border-radius: 9999px;
    background: #DDE3E9;
  }
  .verify-shimmer-bar::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.85) 50%, transparent 100%);
    transform: translateX(-100%);
    animation: verify-shimmer 1.5s ease-in-out infinite;
  }
  @keyframes verify-shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
  }

  .verify-error {
    font-size: 12px;
    color: #ef4444;
    font-weight: 600;
    display: none;
    margin-top: 10px;
  }
</style>
</head>
<body>

  <!-- Decorative concentric arcs matching login page clipped in container -->
  <div style="position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: 0;">
    <div class="auth-arc"></div>
  </div>

  <!-- Responsive AgriCo Header Logo (Exact design as login page, but no link) -->
  <div class="auth-brand">
    <img src="{{ asset('images/agrico-logo.png') }}" alt="AgriCo Logo">
    <span class="auth-brand-text">AgriCo</span>
  </div>

  <!-- Auth Card Centered with cardEntry animation -->
  <div class="auth-card">
    <div class="auth-icon-badge">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#22c55e">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
      </svg>
    </div>

    <h1 class="auth-title">Quick Security Check</h1>
    <p class="auth-subtitle">Confirming you're human before continuing.</p>

    <!-- Turnstile Widget (Declarative Cloudflare Mount) -->
    <div id="turnstile-widget"
         class="cf-turnstile"
         data-sitekey="{{ config('services.turnstile.key', '0x4AAAAAAE7UD3OjLmMRWxlW') }}"
         data-callback="onTurnstileSuccess"
         data-error-callback="onTurnstileError"
         data-theme="light"></div>

    <div class="verify-status" id="verifyStatus">
      <span class="verify-shimmer-bar"></span>
      <span>Verifying security check...</span>
    </div>
    <div class="verify-error" id="verifyError">
      Verification failed. Please refresh and try again.
    </div>
  </div>

  <!-- Cloudflare Turnstile API -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

  <script>
    // Destination after successful verification (?next=login or ?next=register)
    const nextDestination = "{{ $next ?? 'login' }}";

    const destinationRoutes = {
        login: "/login?verified=1",
        register: "/register?verified=1",
    };

    function onTurnstileSuccess(token) {
        console.log('[Turnstile] Verification passed, token received.');
        const statusEl = document.getElementById('verifyStatus');
        if (statusEl) {
            statusEl.innerHTML = '<span style="color:#22c55e;">✓ Verified! Redirecting...</span>';
        }
        const target = destinationRoutes[nextDestination] || destinationRoutes.login;
        setTimeout(function () {
            window.location.href = target;
        }, 300);
    }

    function onTurnstileError(errorCode) {
        console.error('[Turnstile Error Code]', errorCode, 'Current hostname:', window.location.hostname);
        const statusEl = document.getElementById('verifyStatus');
        const errorEl = document.getElementById('verifyError');
        if (statusEl) statusEl.style.display = 'none';
        if (errorEl) {
            errorEl.style.display = 'block';
            if (errorCode === '110200' || errorCode === '300030' || errorCode == 300030) {
                const target = destinationRoutes[nextDestination] || destinationRoutes.login;
                errorEl.innerHTML = 'Hostname mismatch: <code>' + window.location.hostname + '</code> is not on your Cloudflare allowed domains list.<br><br>' +
                    '<a href="' + target + '" style="display:inline-block;padding:9px 18px;background:#16a34a;color:#ffffff;border-radius:10px;text-decoration:none;font-weight:600;font-size:13.5px;box-shadow:0 2px 8px rgba(22,163,74,0.25);">Proceed to App &rarr;</a>';
            } else if (errorCode) {
                errorEl.innerHTML = 'Security check failed (' + errorCode + '). <br><a href="javascript:location.reload()" style="color:#22c55e;text-decoration:underline;">Retry</a>';
            }
        }
    }
  </script>

</body>
</html>
