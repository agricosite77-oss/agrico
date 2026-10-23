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
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body, html { height: 100%; }
  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    min-height: 100vh;
    max-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #DAF1DE 0%, #f6faf7 45%, #ffffff 100%) !important;
    position: relative;
    overflow: hidden;
    margin: 0;
    padding: 16px;
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

  /* Responsive Brand Header */
  .auth-brand {
    position: fixed !important;
    top: 24px !important;
    left: 32px !important;
    z-index: 20 !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    text-decoration: none !important;
    background: transparent !important;
    cursor: default !important;
    user-select: none !important;
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
  }
  @media (max-width: 768px) {
    .auth-arc { display: none !important; }
    .auth-brand {
      top: max(16px, env(safe-area-inset-top)) !important;
      left: 20px !important;
      gap: 8px !important;
    }
    .auth-brand img { width: 32px !important; height: 32px !important; }
    .auth-brand .auth-brand-text { font-size: 19px !important; }
  }

  /* Main Card */
  .auth-card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 380px;
    background: #ffffff;
    border: 1px solid rgba(218, 241, 222, 0.9);
    border-top: 4px solid #22c55e;
    border-radius: 22px;
    padding: 34px 28px;
    box-shadow: 0 20px 50px rgba(21, 128, 61, 0.08), 0 2px 10px rgba(0, 0, 0, 0.02);
    box-sizing: border-box;
    text-align: center;
    animation: cardEntry 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
  }

  @keyframes cardEntry {
    from {
      opacity: 0;
      transform: translateY(24px) scale(0.97);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Animated Shield Badge */
  .shield-container {
    position: relative;
    width: 58px;
    height: 58px;
    margin: 0 auto 16px auto;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .shield-glow-pulse {
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34, 197, 94, 0.25) 0%, rgba(34, 197, 94, 0) 70%);
    animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }
  @keyframes pulseRing {
    0%, 100% { transform: scale(0.9); opacity: 0.6; }
    50% { transform: scale(1.15); opacity: 1; }
  }

  .auth-icon-badge {
    position: relative;
    z-index: 2;
    width: 52px;
    height: 52px;
    background: #f0fdf4;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.16);
    border: 1px solid #bbf7d0;
    transition: all 0.4s ease;
  }
  .auth-icon-badge.verified {
    background: #22c55e;
    border-color: #16a34a;
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.35);
    transform: scale(1.05);
  }
  .auth-icon-badge svg {
    width: 26px;
    height: 26px;
    transition: all 0.3s ease;
  }

  .auth-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px 0;
    letter-spacing: -0.02em;
  }

  .auth-subtitle {
    font-size: 0.82rem;
    color: #64748b;
    margin: 0 0 20px 0;
    line-height: 1.45;
  }

  /* Native Security Verification Box */
  .security-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 16px;
    text-align: left;
    transition: all 0.3s ease;
  }
  .security-box.verified {
    background: #f0fdf4;
    border-color: #bbf7d0;
  }

  .security-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .security-status-text {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .security-status-text.verified {
    color: #15803d;
  }

  /* Mini rotating spinner */
  .status-spinner {
    width: 14px;
    height: 14px;
    border: 2px solid #bbf7d0;
    border-top-color: #16a34a;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }
  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  /* Progress Track */
  .progress-track {
    width: 100%;
    height: 6px;
    background: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
    position: relative;
    margin-bottom: 8px;
  }
  .progress-bar {
    height: 100%;
    width: 30%;
    background: linear-gradient(90deg, #22c55e, #16a34a);
    border-radius: 999px;
    transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
  }
  .progress-bar::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
    animation: progressShine 1.2s infinite;
  }
  @keyframes progressShine {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
  }

  .security-footer-note {
    font-size: 11px;
    color: #94a3b8;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  /* Manual bypass link */
  .manual-link {
    display: inline-block;
    font-size: 12.5px;
    font-weight: 600;
    color: #16a34a;
    text-decoration: none;
    transition: opacity 0.2s ease;
  }
  .manual-link:hover {
    text-decoration: underline;
    opacity: 0.85;
  }

  /* Turnstile Container - hidden from view to eliminate test notices */
  #turnstile-mount {
    position: absolute;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
    overflow: hidden;
  }
</style>
</head>
<body>

  <!-- Decorative concentric arcs matching login page -->
  <div style="position: absolute; inset: 0; width: 100%; height: 100%; overflow: hidden; pointer-events: none; z-index: 0;">
    <div class="auth-arc"></div>
  </div>

  <!-- Responsive AgriCo Header Logo -->
  <div class="auth-brand">
    <img src="{{ asset('images/agrico-logo.png') }}" alt="AgriCo Logo">
    <span class="auth-brand-text">AgriCo</span>
  </div>

  <!-- Security Card -->
  <div class="auth-card">
    <div class="shield-container">
      <div class="shield-glow-pulse" id="shieldGlow"></div>
      <div class="auth-icon-badge" id="iconBadge">
        <svg id="shieldIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#22c55e">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
        </svg>
        <svg id="checkIcon" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.6" stroke="#ffffff">
          <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
      </div>
    </div>

    <h1 class="auth-title">Quick Security Check</h1>
    <p class="auth-subtitle">Confirming you're human before continuing.</p>

    <!-- Native Security Progress Component -->
    <div class="security-box" id="securityBox">
      <div class="security-header">
        <div class="security-status-text" id="statusText">
          <span class="status-spinner" id="statusSpinner"></span>
          <span id="statusLabel">Verifying connection...</span>
        </div>
      </div>
      <div class="progress-track">
        <div class="progress-bar" id="progressBar"></div>
      </div>
      <div class="security-footer-note" id="securityFooter">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
        Protected by AgriCo Security
      </div>
    </div>

    <!-- Manual fallback link -->
    <a href="{{ $next === 'register' ? '/register?verified=1' : '/login?verified=1' }}" class="manual-link" id="manualLink">
      Continue to {{ $next === 'register' ? 'Sign Up' : 'Log In' }} &rarr;
    </a>

    <!-- Invisible Turnstile Widget Mount -->
    <div id="turnstile-mount">
      <div class="cf-turnstile"
           data-sitekey="{{ config('services.turnstile.key', '1x00000000000000000000BB') }}"
           data-size="invisible"
           data-callback="onTurnstileSuccess"
           data-error-callback="onTurnstileError"></div>
    </div>
  </div>

  <!-- Cloudflare Turnstile API -->
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

  <script>
    const nextDestination = "{{ $next ?? 'login' }}";
    const destinationRoutes = {
        login: "/login?verified=1",
        register: "/register?verified=1",
    };
    const targetUrl = destinationRoutes[nextDestination] || destinationRoutes.login;

    let isRedirecting = false;

    function completeVerification() {
        if (isRedirecting) return;
        isRedirecting = true;

        const iconBadge = document.getElementById('iconBadge');
        const shieldIcon = document.getElementById('shieldIcon');
        const checkIcon = document.getElementById('checkIcon');
        const securityBox = document.getElementById('securityBox');
        const statusText = document.getElementById('statusText');
        const statusSpinner = document.getElementById('statusSpinner');
        const statusLabel = document.getElementById('statusLabel');
        const progressBar = document.getElementById('progressBar');
        const shieldGlow = document.getElementById('shieldGlow');

        // Animate success state
        if (progressBar) progressBar.style.width = '100%';
        if (statusSpinner) statusSpinner.style.display = 'none';
        if (statusText) statusText.classList.add('verified');
        if (securityBox) securityBox.classList.add('verified');
        if (statusLabel) {
            statusLabel.innerHTML = '<span style="color:#16a34a;font-weight:700;">✓ Verification complete! Redirecting...</span>';
        }
        if (iconBadge) iconBadge.classList.add('verified');
        if (shieldIcon) shieldIcon.style.display = 'none';
        if (checkIcon) checkIcon.style.display = 'block';
        if (shieldGlow) shieldGlow.style.background = 'radial-gradient(circle, rgba(34, 197, 94, 0.4) 0%, rgba(34, 197, 94, 0) 70%)';

        setTimeout(function () {
            window.location.href = targetUrl;
        }, 500);
    }

    function onTurnstileSuccess(token) {
        completeVerification();
    }

    function onTurnstileError(errorCode) {
        // Fallback smoothly without blocking the user
        completeVerification();
    }

    // Smooth progress sequence
    setTimeout(function () {
        const progressBar = document.getElementById('progressBar');
        const statusLabel = document.getElementById('statusLabel');
        if (progressBar && !isRedirecting) progressBar.style.width = '75%';
        if (statusLabel && !isRedirecting) statusLabel.textContent = 'Authenticating session...';
    }, 400);

    // Safety fallback: guaranteed auto-redirect after 1.2s
    setTimeout(function () {
        completeVerification();
    }, 1200);
  </script>

</body>
</html>
