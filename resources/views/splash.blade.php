<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>AgriCo</title>
<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<style>
  /* --k scales the whole sequence: 1 = 11s (storyboard), .5 = 5.5s */
  :root{--k:1;--d:calc(11s * var(--k));--ease:cubic-bezier(.45,0,.2,1);--ease-out:cubic-bezier(.22,1,.36,1)}
  *{margin:0;padding:0;box-sizing:border-box}
  body{min-height:100vh;display:grid;place-items:center;font-family:Inter,system-ui,sans-serif;color:#16a34a;overflow:hidden}

  /* Light background with concentric rings (same as the auth pages) */
  .bg{background:linear-gradient(180deg,#DAF1DE 0%,#f6faf7 45%,#ffffff 100%);position:fixed;inset:0;overflow:hidden}
  .arc,.arc::before,.arc::after{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);border-radius:50%;pointer-events:none}
  .arc{width:640px;height:640px;border:1.5px solid rgba(142, 182, 155, 0.4)}
  .arc::after{content:"";width:880px;height:880px;border:1.5px solid rgba(187,247,208,0.4)}
  .arc::before{content:"";width:1120px;height:1120px;border:1.5px solid rgba(187,247,208,0.2)}

  .splash{position:fixed;inset:0;z-index:50;animation:end var(--d) linear both}
  .splash[hidden]{display:none}
  @keyframes end{0%,95.5%{opacity:1}100%{opacity:0}}

  /* Wave: rises (1.5-4.0s), holds, then leaves upward (8.0-9.5s) */
  .wave{position:absolute;left:0;top:-25vh;width:100%;height:150vh;transform:translateY(130vh);animation:wave var(--d) both}
  .wave svg{display:block;width:100%;height:100%}
  @keyframes wave{
    0%,13.6%{transform:translateY(130vh);animation-timing-function:var(--ease)}
    36.4%,72.7%{transform:translateY(0);animation-timing-function:var(--ease)}
    86.4%,100%{transform:translateY(-130vh)}
  }

  /* Logo + wordmark, always centered */
  .brand{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:14px}
  .logo{width:92px;height:92px;object-fit:contain;animation:logo calc(11s * var(--k)) var(--ease-out) both}
  @keyframes logo{0%{opacity:0;transform:scale(.9);filter:blur(4px)}9%,100%{opacity:1;transform:scale(1);filter:blur(0)}}

  .word{font-size:58px;overflow:hidden;white-space:nowrap;padding:.2em .08em .35em;margin:-.2em 0 -.35em;max-width:0;opacity:0;animation:word var(--d) var(--ease-out) both}
  .word span{display:block;font-size:1em;font-weight:800;letter-spacing:-.02em;line-height:1.1;animation:wordin var(--d) var(--ease-out) both}
  .word b{color:#22c55e;font-weight:800}
  @keyframes word{0%,36.4%{max-width:0;opacity:0}46%,100%{max-width:320px;opacity:1}}
  @keyframes wordin{0%,36.4%{transform:translateY(10px);filter:blur(4px)}46%,100%{transform:translateY(0);filter:blur(0)}}

  @media (prefers-reduced-motion:reduce){.splash{display:none}}
  @media (max-width:520px){.logo{width:64px;height:64px}.word{font-size:38px}@keyframes word{0%,36.4%{max-width:0;opacity:0}46%,100%{max-width:220px;opacity:1}}}
</style>
</head>
<body>
<div class="bg"><div class="arc"></div></div>

<div class="splash" id="splash" role="status" aria-label="Loading AgriCo">
  <div class="bg"><div class="arc"></div></div>
  <div class="wave" aria-hidden="true">
    <svg viewBox="0 0 1440 1300" preserveAspectRatio="none">
      <defs>
        <linearGradient id="g" x1="0" y1="1" x2="1" y2="0">
          <stop offset="0" stop-color="#9be8b3"/><stop offset=".55" stop-color="#c3f4cf"/><stop offset="1" stop-color="#dcfcb0"/>
        </linearGradient>
      </defs>
      <path fill="url(#g)" d="M0 60C300 60 450 210 720 210C990 210 1140 60 1440 60L1440 1100C1140 1100 990 1250 720 1250C450 1250 300 1100 0 1100Z"/>
    </svg>
  </div>
  <div class="brand">
    <img class="logo" id="logo" src="{{ asset('images/agrico-logo.png') }}" alt="AgriCo Logo">
    <div class="word"><span>Agri<b>Co</b></span></div>
  </div>
</div>

<script>
  const splash = document.getElementById('splash'), logo = document.getElementById('logo');

  // Placeholder pin if images/agrico-logo.png isn't found (demo only)
  const FALLBACK = 'data:image/svg+xml,' + encodeURIComponent(
    `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 120"><path d="M50 112C50 112 14 76 14 48a36 36 0 0 1 72 0c0 28-36 64-36 64Z" fill="#fff" stroke="#22c55e" stroke-width="5" stroke-linejoin="round"/><circle cx="50" cy="48" r="25" fill="#86efac"/><path d="M27 58c14-14 34-14 46-4v9C60 55 42 57 29 67Z" fill="#22c55e"/></svg>`);
  const useFallback = () => { logo.onerror = null; logo.src = FALLBACK; };
  logo.onerror = useFallback;
  if (logo.complete && !logo.naturalWidth) useFallback();

  let redirectTimer = null;

  // Remove the splash and smoothly navigate to the next step when the sequence finishes
  splash.addEventListener('animationend', e => { 
    if (e.target === splash) {
      splash.hidden = true;
      redirectTimer = setTimeout(() => {
        window.location.href = "{{ url('/verify?next=login') }}";
      }, 500);
    } 
  });
</script>
</body>
</html>
