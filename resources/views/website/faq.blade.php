<x-website-layout>
    <x-slot name="title">FAQ</x-slot>

    <style>
        .ws-page-hero {
            background: linear-gradient(135deg, #051F20 0%, #0B2B26 100%);
            padding: 16px 24px 14px;
            border-bottom: 1px solid rgba(142, 182, 155, 0.2);
            flex-shrink: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        .ws-page-hero-inner { max-width: 1400px; margin: 0 auto; width: 100%; }
        .ws-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: #8EB69B; margin-bottom: 4px;
            font-weight: 500;
        }
        .ws-breadcrumb a { color: #DAF1DE; text-decoration: none; font-weight: 600; transition: color 0.2s ease; }
        .ws-breadcrumb a:hover { text-decoration: underline; color: #8EB69B; }
        .ws-page-title {
            font-size: clamp(1.3rem, 2.5vw, 1.75rem); font-weight: 900;
            color: #DAF1DE; margin: 0 0 2px 0; letter-spacing: -0.5px; line-height: 1.2;
        }
        .ws-page-sub { font-size: 13px; color: #8EB69B; margin: 0; font-weight: 400; }

        .faq-wrapper {
            max-width: 1080px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }

        /* Section header */
        .faq-count-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(11, 43, 38, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 12px;
            padding: 8px 18px;
            font-size: 13.5px;
            color: #8EB69B;
            font-weight: 600;
            margin-bottom: 28px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }
        .faq-count-chip span { color: #4ADE80; font-weight: 800; }

        /* Accordion Cards */
        .faq-item {
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border: 1px solid rgba(142, 182, 155, 0.22);
            border-radius: 18px;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .faq-item:hover {
            border-color: rgba(142, 182, 155, 0.45);
            transform: translateY(-2px);
            box-shadow: 0 14px 36px rgba(0, 0, 0, 0.45);
        }
        .faq-item.open {
            border-color: #DAF1DE !important;
            background: linear-gradient(145deg, #163832 0%, #0B2B26 100%) !important;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5), 0 0 24px rgba(35, 83, 71, 0.3) !important;
        }
        .faq-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 26px;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
            transition: background 0.2s ease;
        }
        .faq-question {
            font-size: 16px;
            font-weight: 800;
            color: #DAF1DE;
            line-height: 1.45;
            flex: 1;
            letter-spacing: -0.01em;
            transition: color 0.25s ease;
        }
        .faq-item.open .faq-question { color: #FFFFFF !important; font-weight: 800; }
        .faq-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .faq-answer {
            padding: 16px 26px 26px;
            font-size: 14.5px;
            color: #DAF1DE;
            line-height: 1.75;
            border-top: 1px solid rgba(142, 182, 155, 0.18);
            font-weight: 400;
            opacity: 0;
            transform: translateY(-8px);
            transition: opacity 0.3s ease 0.05s, transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) 0.05s;
        }
        .faq-item.open .faq-answer {
            opacity: 1;
            transform: translateY(0);
        }
        .faq-icon {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(5, 31, 32, 0.7);
            border: 1.5px solid rgba(142, 182, 155, 0.35);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .faq-icon svg { stroke: #4ADE80 !important; transition: stroke 0.25s ease; }
        .faq-trigger:hover .faq-icon {
            transform: scale(1.1);
            border-color: #DAF1DE;
            box-shadow: 0 0 14px rgba(218, 241, 222, 0.3);
        }
        .faq-item.open .faq-icon {
            background: linear-gradient(135deg, #235347, #163832) !important;
            border-color: #DAF1DE !important;
            transform: rotate(135deg) scale(1.05);
            box-shadow: 0 4px 16px rgba(35, 83, 71, 0.5) !important;
        }
        .faq-item.open .faq-icon svg { stroke: #FFFFFF !important; }

        /* Empty state */
        .ws-empty {
            text-align: center;
            padding: 80px 24px;
            color: #8EB69B;
            font-size: 15px;
        }

        /* Contact CTA */
        .faq-contact-cta {
            margin-top: 56px;
            background: linear-gradient(135deg, #051F20 0%, #163832 100%);
            border: 1px solid rgba(142, 182, 155, 0.3);
            border-radius: 24px;
            padding: 42px 36px;
            text-align: center;
            color: #DAF1DE;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), 0 0 30px rgba(35, 83, 71, 0.25);
        }
        .faq-contact-cta h3 {
            font-size: 22px;
            font-weight: 900;
            color: #DAF1DE;
            margin: 0 0 10px 0;
            letter-spacing: -0.02em;
        }
        .faq-contact-cta p {
            font-size: 14px;
            color: #8EB69B;
            margin: 0 0 26px 0;
            line-height: 1.6;
        }
        .faq-contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: rgba(35, 83, 71, 0.45);
            border: 1.5px solid #8EB69B;
            color: #DAF1DE;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            transition: all 0.25s ease;
        }
        .faq-contact-btn:hover {
            background: #235347;
            border-color: #DAF1DE;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(35, 83, 71, 0.5);
        }

        /* ── LIGHT MODE OVERRIDES ── */
        html.light-mode .ws-page-hero {
            background: linear-gradient(135deg, #DAF1DE 0%, #EEF7F1 100%) !important;
            border-bottom-color: rgba(142, 182, 155, 0.4) !important;
        }
        html.light-mode .ws-page-title { color: #051F20 !important; }
        html.light-mode .ws-page-sub   { color: #163832 !important; }
        html.light-mode .ws-breadcrumb { color: #235347 !important; }
        html.light-mode .ws-breadcrumb a { color: #051F20 !important; }
        html.light-mode .ws-breadcrumb span { color: #051F20 !important; }

        html.light-mode .faq-count-chip {
            background: #FFFFFF;
            border-color: rgba(142, 182, 155, 0.5);
            color: #163832;
            box-shadow: 0 2px 10px rgba(5, 31, 32, 0.08);
        }
        html.light-mode .faq-count-chip span { color: #235347; }

        html.light-mode .faq-item {
            background: #FFFFFF !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
            border-radius: 18px !important;
        }
        html.light-mode .faq-item:hover {
            border-color: #22c55e !important;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .faq-item.open {
            background: #FFFFFF !important;
            border-color: #22c55e !important;
            box-shadow: 0 8px 28px rgba(34, 197, 94, 0.18) !important;
        }
        html.light-mode .faq-question {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode .faq-trigger:hover .faq-question,
        html.light-mode .faq-item.open .faq-question {
            color: #16a34a !important;
            font-weight: 800 !important;
        }

        html.light-mode .faq-icon {
            background: #f0fdf4 !important;
            border: 1.5px solid #86efac !important;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.1) !important;
        }
        html.light-mode .faq-icon svg {
            stroke: #16a34a !important;
        }
        html.light-mode .faq-item.open .faq-icon {
            background: #22c55e !important;
            border-color: #22c55e !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
        }
        html.light-mode .faq-item.open .faq-icon svg {
            stroke: #FFFFFF !important;
        }

        html.light-mode .faq-answer {
            color: #334155 !important;
            border-top-color: #f1f5f9 !important;
            font-weight: 500 !important;
        }

        html.light-mode .faq-contact-cta {
            background: linear-gradient(135deg, #DAF1DE 0%, #EEF7F1 100%);
            border-color: rgba(142, 182, 155, 0.5);
            box-shadow: 0 8px 28px rgba(5, 31, 32, 0.08);
        }
        html.light-mode .faq-contact-cta h3 { color: #051F20; }
        html.light-mode .faq-contact-cta p  { color: #163832; }
        html.light-mode .faq-contact-btn {
            background: #22c55e !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        html.light-mode .faq-contact-btn:hover {
            background: #16a34a !important;
            box-shadow: 0 6px 18px rgba(34, 197, 94, 0.45) !important;
            transform: translateY(-1px) !important;
        }
        .ws-breadcrumb-current { color: #DAF1DE; font-weight: 700; }
        html.light-mode .ws-breadcrumb-current { color: #051F20 !important; font-weight: 700 !important; }

        html.light-mode .ws-empty { color: #235347; }
    </style>

    {{-- Page Hero --}}
    <div class="ws-page-hero">
        <div class="ws-page-hero-inner ui-reveal">
            <div class="ws-breadcrumb">
                <a href="{{ route('website.home') }}">Home</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                <span class="ws-breadcrumb-current">FAQ</span>
            </div>
            <h1 class="ws-page-title">Frequently Asked Questions</h1>
            <p class="ws-page-sub">Everything you need to know about AgriCo and soil scanning.</p>
        </div>
    </div>

    <div class="faq-wrapper">

        @if($faqs->count() > 0)
            <div class="faq-count-chip ui-reveal">
                <span>{{ $faqs->count() }}</span> questions answered
            </div>

            @foreach($faqs as $i => $faq)
            <div class="faq-item ui-reveal" id="faq-{{ $i }}" data-index="{{ $i }}">
                <button class="faq-trigger" onclick="toggleFaq({{ $i }})" type="button"
                        aria-expanded="false" aria-controls="faq-body-{{ $i }}">
                    <span class="faq-question">{{ $faq->question }}</span>
                    <div class="faq-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                </button>
                <div class="faq-body" id="faq-body-{{ $i }}" role="region">
                    <div class="faq-answer">
                        {{ $faq->answer }}
                    </div>
                </div>
            </div>
            @endforeach

        @else
            <div class="ws-empty">
                <svg width="48" height="48" fill="none" stroke="#8EB69B" stroke-width="1.5"
                     viewBox="0 0 24 24" style="margin:0 auto 16px;display:block;">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <p>No FAQs have been added yet. Check back soon!</p>
            </div>
        @endif

        {{-- Contact CTA --}}
        <div class="faq-contact-cta ui-scroll-reveal">
            <h3>Still have questions?</h3>
            <p>Browse the Munsell Color Chart or explore our interactive soil map for more insights about AgriCo.</p>
            <a href="{{ route('website.munsell') }}" class="faq-contact-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                View Color Chart
            </a>
        </div>

    </div>

    <script>
        function toggleFaq(index) {
            const item = document.getElementById('faq-' + index);
            if (!item) return;
            const body = item.querySelector('.faq-body');
            const trigger = item.querySelector('.faq-trigger');
            const isOpen = item.classList.contains('open');

            // Close all items smoothly
            document.querySelectorAll('.faq-item').forEach(el => {
                const elBody = el.querySelector('.faq-body');
                const elTrigger = el.querySelector('.faq-trigger');
                el.classList.remove('open');
                if (elBody) elBody.style.maxHeight = '0px';
                if (elTrigger) elTrigger.setAttribute('aria-expanded', 'false');
            });

            // Open clicked item smoothly
            if (!isOpen && body) {
                item.classList.add('open');
                if (trigger) trigger.setAttribute('aria-expanded', 'true');
                body.style.maxHeight = (body.scrollHeight + 40) + 'px';
            }
        }
    </script>

</x-website-layout>
