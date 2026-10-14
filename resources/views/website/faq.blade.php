<x-website-layout>
    <x-slot name="title">Frequently Asked Questions — AgriCo</x-slot>

    {{-- SEO Meta Description --}}
    @push('head')
        <meta name="description" content="Find quick answers about AgriCo soil scanning, Munsell color codes, account registration, administrator approval, interactive map pins, and the mobile application." />
    @endpush

    <style>
        /* ── PAGE HERO (LIGHT GREEN AGRI-GRADIENT) ── */
        .faq-hero {
            background: linear-gradient(135deg, #f0fdf4 0%, #e2f7e8 50%, #daf1de 100%);
            border-bottom: 1px solid #c7ecd1;
            padding: 32px 24px 28px;
            box-shadow: 0 4px 18px rgba(34, 197, 94, 0.06);
        }
        .faq-hero-inner {
            max-width: 1040px;
            margin: 0 auto;
            width: 100%;
        }
        .faq-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #15803d;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .faq-breadcrumb a {
            color: #166534;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .faq-breadcrumb a:hover {
            color: #22c55e;
            text-decoration: underline;
        }
        .faq-title {
            font-size: clamp(1.6rem, 3.2vw, 2.25rem);
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.025em;
            line-height: 1.2;
            margin: 0 0 6px 0;
        }
        .faq-subtitle {
            font-size: 14.5px;
            color: #475569;
            margin: 0;
            line-height: 1.5;
            max-width: 680px;
        }

        /* ── MAIN CONTENT WRAPPER ── */
        .faq-container {
            max-width: 1040px;
            margin: 0 auto;
            padding: 28px 20px 80px;
            box-sizing: border-box;
        }

        /* ── SEARCH & FILTER CONTROLS ── */
        .faq-controls {
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .faq-search-box {
            position: relative;
            width: 100%;
        }
        .faq-search-input {
            width: 100%;
            height: 50px;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 10px 48px 10px 48px;
            font-size: 15px;
            color: #0f172a;
            box-sizing: border-box;
            outline: none;
            font-family: inherit;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .faq-search-input:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        }
        .faq-search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            stroke: #94a3b8;
            pointer-events: none;
        }
        .faq-search-clear {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: #f1f5f9;
            border: none;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: none;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
            transition: all 0.15s ease;
        }
        .faq-search-clear:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* ── CATEGORY PILLS & NAVIGATION ARROWS ── */
        .faq-categories-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }
        .faq-cat-arrow {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            color: #166534;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
            padding: 0;
        }
        .faq-cat-arrow:hover:not(:disabled) {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #15803d;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
            transform: translateY(-1px);
        }
        .faq-cat-arrow:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            border-color: #e2e8f0;
            color: #94a3b8;
            box-shadow: none;
            transform: none;
        }
        .faq-categories {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 4px 2px 6px;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
            flex: 1;
        }
        .faq-categories::-webkit-scrollbar {
            display: none;
        }
        .faq-cat-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 9999px;
            padding: 8px 16px;
            font-size: 13.5px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }
        .faq-cat-btn:hover {
            border-color: #86efac;
            color: #166534;
            background: #f0fdf4;
        }
        .faq-cat-btn.active {
            background: #22c55e;
            border-color: #22c55e;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
        .faq-cat-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.06);
            border-radius: 9999px;
            padding: 2px 7px;
            font-size: 11.5px;
            font-weight: 700;
        }
        .faq-cat-btn.active .faq-cat-count {
            background: rgba(255, 255, 255, 0.28);
            color: #ffffff;
        }

        /* ── ACCORDION TOOLBAR (COUNT & EXPAND ALL) ── */
        .faq-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 0 4px;
        }
        .faq-status-text {
            font-size: 13.5px;
            font-weight: 700;
            color: #166534;
        }
        .faq-expand-toggle {
            background: none;
            border: none;
            color: #16a34a;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 8px;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .faq-expand-toggle:hover {
            background: #f0fdf4;
            color: #15803d;
        }

        /* ── ACCORDION ITEMS ── */
        .faq-item {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            margin-bottom: 14px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.2s ease;
        }
        .faq-item:hover {
            border-color: #86efac;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.08);
            transform: translateY(-1px);
        }
        .faq-item.is-open {
            border-color: #22c55e;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.12);
        }
        .faq-item.is-hidden {
            display: none !important;
        }
        .faq-item.highlight-target {
            animation: highlightPulse 2s ease-out;
        }
        @keyframes highlightPulse {
            0% { box-shadow: 0 0 0 4px #86efac; border-color: #22c55e; }
            50% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0.25); border-color: #16a34a; }
            100% { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); border-color: #22c55e; }
        }

        /* ── TRIGGER BUTTON ── */
        .faq-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 24px;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            font-family: inherit;
            color: inherit;
            outline: none;
        }
        .faq-trigger:focus-visible {
            outline: 2px solid #22c55e;
            outline-offset: -2px;
            border-radius: 18px;
        }
        .faq-header-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .faq-tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #16a34a;
        }
        .faq-question {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.45;
            transition: color 0.2s ease;
        }
        .faq-item.is-open .faq-question {
            color: #15803d;
        }
        .faq-icon-bubble {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), background 0.2s ease, border-color 0.2s ease;
        }
        .faq-icon-bubble svg {
            stroke: #16a34a;
            transition: stroke 0.2s ease, transform 0.3s ease;
        }
        .faq-item.is-open .faq-icon-bubble {
            background: #22c55e;
            border-color: #22c55e;
            transform: rotate(180deg);
        }
        .faq-item.is-open .faq-icon-bubble svg {
            stroke: #ffffff;
        }

        /* ── BODY / ANSWER ── */
        .faq-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .faq-answer-inner {
            padding: 4px 24px 24px;
            font-size: 14.5px;
            line-height: 1.7;
            color: #334155;
            border-top: 1px solid #f1f5f9;
        }
        .faq-answer-inner a {
            color: #15803d;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 2px;
            transition: color 0.15s ease;
        }
        .faq-answer-inner a:hover {
            color: #22c55e;
        }

        /* ── ACTION FOOTER IN ANSWER (COPY LINK) ── */
        .faq-answer-footer {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px dashed #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: #94a3b8;
        }
        .faq-copy-anchor-btn {
            background: none;
            border: none;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .faq-copy-anchor-btn:hover {
            background: #f1f5f9;
            color: #16a34a;
        }

        /* ── EMPTY / NO RESULTS STATE ── */
        .faq-no-results {
            display: none;
            text-align: center;
            padding: 56px 20px;
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            margin: 20px 0;
        }
        .faq-no-results-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 12px;
            color: #94a3b8;
        }
        .faq-no-results h3 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px 0;
        }
        .faq-no-results p {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 16px 0;
        }
        .faq-clear-search-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            color: #15803d;
            font-size: 13.5px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .faq-clear-search-btn:hover {
            background: #22c55e;
            border-color: #22c55e;
            color: #ffffff;
        }

        /* ── STILL NEED HELP (CONTACT CTA) ── */
        .faq-contact-card {
            margin-top: 48px;
            background: linear-gradient(135deg, #f0fdf4 0%, #e2f7e8 100%);
            border: 2px solid #bbf7d0;
            border-radius: 24px;
            padding: 36px 32px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(34, 197, 94, 0.08);
        }
        .faq-contact-card h2 {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            margin: 0 0 8px 0;
            letter-spacing: -0.02em;
        }
        .faq-contact-card p {
            font-size: 14.5px;
            color: #475569;
            margin: 0 0 24px 0;
            line-height: 1.6;
            max-width: 580px;
            margin-left: auto;
            margin-right: auto;
        }
        .faq-contact-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .faq-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #22c55e;
            border: none;
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 700;
            border-radius: 14px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .faq-btn-primary:hover {
            background: #16a34a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.45);
        }
        .faq-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #334155 !important;
            font-size: 14px;
            font-weight: 700;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .faq-btn-secondary:hover {
            border-color: #22c55e;
            color: #16a34a !important;
            background: #f8fafc;
            transform: translateY(-1px);
        }

        /* ── RESPONSIVE DESIGN ── */
        @media (max-width: 640px) {
            .faq-hero {
                padding: 24px 16px 20px;
            }
            .faq-container {
                padding: 20px 14px 60px;
            }
            .faq-trigger {
                padding: 16px 18px;
            }
            .faq-question {
                font-size: 15px;
            }
            .faq-answer-inner {
                padding: 4px 18px 20px;
                font-size: 14px;
            }
            .faq-contact-card {
                padding: 28px 18px;
            }
            .faq-contact-actions {
                flex-direction: column;
                width: 100%;
            }
            .faq-cat-arrow {
                width: 32px;
                height: 32px;
            }
            .faq-btn-primary, .faq-btn-secondary {
                width: 100%;
                justify-content: center;
                box-sizing: border-box;
            }
        }
    </style>

    {{-- Page Hero Banner --}}
    <section class="faq-hero">
        <div class="faq-hero-inner">
            <nav class="faq-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('website.home') }}">Home</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
                <span>Help & FAQ</span>
            </nav>
            <h1 class="faq-title">Frequently Asked Questions</h1>
            <p class="faq-subtitle">
                Everything you need to know about AgriCo soil scanning, Munsell color classification, accounts, and maps.
            </p>
        </div>
    </section>

    {{-- Main FAQ Content --}}
    <main class="faq-container">

        {{-- Search and Category Filters --}}
        <div class="faq-controls">
            {{-- Search Bar --}}
            <div class="faq-search-box">
                <svg class="faq-search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    type="text"
                    id="faqSearchInput"
                    class="faq-search-input"
                    placeholder="Search questions by topic, keyword, or feature (e.g. forgot password, lighting, pins)..."
                    aria-label="Search frequently asked questions"
                    autocomplete="off"
                />
                <button
                    type="button"
                    id="faqSearchClear"
                    class="faq-search-clear"
                    aria-label="Clear search"
                    title="Clear search"
                >&times;</button>
            </div>

            {{-- Category Filter Pills with Navigation Arrows --}}
            <div class="faq-categories-container">
                <button
                    type="button"
                    class="faq-cat-arrow"
                    id="faqCatPrev"
                    aria-label="Scroll categories left"
                    title="Scroll left"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <div class="faq-categories" id="faqCategoriesTrack" role="tablist" aria-label="FAQ Categories">
                    <button
                        type="button"
                        class="faq-cat-btn active"
                        data-category="all"
                        role="tab"
                        aria-selected="true"
                    >
                        <span>All Questions</span>
                        <span class="faq-cat-count" id="count-all">{{ count($faqs) }}</span>
                    </button>

                    @foreach($categories as $catKey => $catTitle)
                        @if($catKey !== 'all')
                            @php
                                $catItemsCount = collect($faqs)->where('category', $catKey)->count();
                            @endphp
                            @if($catItemsCount > 0)
                                <button
                                    type="button"
                                    class="faq-cat-btn"
                                    data-category="{{ $catKey }}"
                                    role="tab"
                                    aria-selected="false"
                                >
                                    <span>{{ $catTitle }}</span>
                                    <span class="faq-cat-count">{{ $catItemsCount }}</span>
                                </button>
                            @endif
                        @endif
                    @endforeach
                </div>

                <button
                    type="button"
                    class="faq-cat-arrow"
                    id="faqCatNext"
                    aria-label="Scroll categories right"
                    title="Scroll right"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Toolbar: Results count & Expand/Collapse All toggle --}}
        <div class="faq-toolbar">
            <div class="faq-status-text" id="faqStatusText">
                Showing all {{ count($faqs) }} questions
            </div>
            <button type="button" class="faq-expand-toggle" id="faqExpandAllToggle">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M7 11l5 5 5-5M7 7l5 5 5-5"/>
                </svg>
                <span id="faqExpandAllLabel">Expand All</span>
            </button>
        </div>

        {{-- Accordion Question Cards --}}
        <div class="faq-list" id="faqList" role="region" aria-label="FAQ Questions">
            @foreach($faqs as $index => $item)
                <article
                    class="faq-item"
                    id="{{ $item['id'] }}"
                    data-index="{{ $index }}"
                    data-category="{{ $item['category'] }}"
                    data-keywords="{{ $item['keywords'] }}"
                >
                    <button
                        type="button"
                        class="faq-trigger"
                        onclick="toggleAccordion('{{ $item['id'] }}')"
                        aria-expanded="false"
                        aria-controls="faq-body-{{ $item['id'] }}"
                    >
                        <div class="faq-header-content">
                            <span class="faq-tag">{{ $item['category_name'] }}</span>
                            <span class="faq-question">{{ $item['question'] }}</span>
                        </div>
                        <div class="faq-icon-bubble" aria-hidden="true">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </div>
                    </button>
                    <div
                        class="faq-body"
                        id="faq-body-{{ $item['id'] }}"
                        role="region"
                        aria-labelledby="{{ $item['id'] }}"
                    >
                        <div class="faq-answer-inner">
                            {!! $item['answer'] !!}

                            <div class="faq-answer-footer">
                                <span>AgriCo Knowledge Base</span>
                                <button
                                    type="button"
                                    class="faq-copy-anchor-btn"
                                    onclick="copyFaqAnchor('{{ $item['id'] }}', this)"
                                    title="Copy direct link to this answer"
                                >
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                                        <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                                    </svg>
                                    <span>Copy Link</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- No Results Match State --}}
        <div class="faq-no-results" id="faqNoResults">
            <svg class="faq-no-results-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                <line x1="8" y1="11" x2="14" y2="11"/>
            </svg>
            <h3>No matching questions found</h3>
            <p id="faqNoResultsSub">We couldn't find any questions matching your query. Try searching with different terms or reset your filters.</p>
            <button type="button" class="faq-clear-search-btn" onclick="resetFaqFilters()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                <span>Reset All Filters</span>
            </button>
        </div>

        {{-- Still Need Help Card --}}
        <section class="faq-contact-card" aria-labelledby="faqContactTitle">
            <h2 id="faqContactTitle">Still have questions?</h2>
            <p>
                Can't find the answer you're looking for? Our administrative team is here to help with account verification, scan inquiries, or soil classification questions.
            </p>
            <div class="faq-contact-actions">
                <a href="mailto:agricosite77@gmail.com" class="faq-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Contact Support (agricosite77@gmail.com)</span>
                </a>
                <a href="{{ route('website.map') }}" class="faq-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                        <line x1="8" y1="2" x2="8" y2="18"/>
                        <line x1="16" y1="6" x2="16" y2="22"/>
                    </svg>
                    <span>View Soil Map</span>
                </a>
                <a href="{{ route('website.munsell') }}" class="faq-btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    <span>Munsell Color Chart</span>
                </a>
            </div>
        </section>

    </main>

    {{-- Interactive Logic: Search, Category Filters, Single-Open Accordion, Deep Linking --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('faqSearchInput');
            const searchClear = document.getElementById('faqSearchClear');
            const categoryButtons = document.querySelectorAll('.faq-cat-btn');
            const faqItems = document.querySelectorAll('.faq-item');
            const statusText = document.getElementById('faqStatusText');
            const noResults = document.getElementById('faqNoResults');
            const expandAllToggle = document.getElementById('faqExpandAllToggle');
            const expandAllLabel = document.getElementById('faqExpandAllLabel');

            let currentCategory = 'all';
            let searchQuery = '';
            let isAllExpanded = false;

            // ── ACCORDION TOGGLE (SINGLE-OPEN BY DEFAULT) ──
            window.toggleAccordion = function (id, forceState = null) {
                const targetItem = document.getElementById(id);
                if (!targetItem) return;

                const body = targetItem.querySelector('.faq-body');
                const trigger = targetItem.querySelector('.faq-trigger');
                const isOpen = targetItem.classList.contains('is-open');
                const shouldOpen = forceState !== null ? forceState : !isOpen;

                // If opening, close other items unless we're in "expand all" mode
                if (shouldOpen && !isAllExpanded) {
                    faqItems.forEach(item => {
                        if (item.id !== id && item.classList.contains('is-open')) {
                            item.classList.remove('is-open');
                            const itemBody = item.querySelector('.faq-body');
                            const itemTrigger = item.querySelector('.faq-trigger');
                            if (itemBody) itemBody.style.maxHeight = '0px';
                            if (itemTrigger) itemTrigger.setAttribute('aria-expanded', 'false');
                        }
                    });
                }

                if (shouldOpen) {
                    targetItem.classList.add('is-open');
                    if (trigger) trigger.setAttribute('aria-expanded', 'true');
                    if (body) body.style.maxHeight = (body.scrollHeight + 32) + 'px';
                } else {
                    targetItem.classList.remove('is-open');
                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                    if (body) body.style.maxHeight = '0px';
                }
            };

            // ── EXPAND / COLLAPSE ALL TOGGLE ──
            if (expandAllToggle) {
                expandAllToggle.addEventListener('click', function () {
                    isAllExpanded = !isAllExpanded;
                    expandAllLabel.textContent = isAllExpanded ? 'Collapse All' : 'Expand All';

                    faqItems.forEach(item => {
                        if (!item.classList.contains('is-hidden')) {
                            window.toggleAccordion(item.id, isAllExpanded);
                        }
                    });
                });
            }

            // ── FILTERING (CATEGORY + SEARCH) ──
            function applyFilters() {
                const query = searchQuery.trim().toLowerCase();
                let visibleCount = 0;

                faqItems.forEach(item => {
                    const itemCat = item.getAttribute('data-category');
                    const keywords = (item.getAttribute('data-keywords') || '').toLowerCase();
                    const questionText = (item.querySelector('.faq-question')?.textContent || '').toLowerCase();
                    const answerText = (item.querySelector('.faq-answer-inner')?.textContent || '').toLowerCase();

                    const matchesCategory = (currentCategory === 'all' || itemCat === currentCategory);
                    const matchesSearch = !query || questionText.includes(query) || answerText.includes(query) || keywords.includes(query);

                    if (matchesCategory && matchesSearch) {
                        item.classList.remove('is-hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('is-hidden');
                        // Close hidden item so it doesn't take up space
                        item.classList.remove('is-open');
                        const body = item.querySelector('.faq-body');
                        const trigger = item.querySelector('.faq-trigger');
                        if (body) body.style.maxHeight = '0px';
                        if (trigger) trigger.setAttribute('aria-expanded', 'false');
                    }
                });

                // Update status text
                if (statusText) {
                    if (query) {
                        statusText.textContent = `Found ${visibleCount} ${visibleCount === 1 ? 'question' : 'questions'} matching "${query}"`;
                    } else if (currentCategory !== 'all') {
                        const activeBtn = document.querySelector(`.faq-cat-btn[data-category="${currentCategory}"] span`);
                        const catName = activeBtn ? activeBtn.textContent : currentCategory;
                        statusText.textContent = `Showing ${visibleCount} in ${catName}`;
                    } else {
                        statusText.textContent = `Showing all ${visibleCount} questions`;
                    }
                }

                // Show/hide empty state
                if (noResults) {
                    if (visibleCount === 0) {
                        noResults.style.display = 'block';
                    } else {
                        noResults.style.display = 'none';
                    }
                }
            }

            // ── CATEGORY BAR ARROWS & SCROLL NAVIGATION ──
            const categoriesTrack = document.getElementById('faqCategoriesTrack');
            const catPrevBtn = document.getElementById('faqCatPrev');
            const catNextBtn = document.getElementById('faqCatNext');

            function updateCatArrowStates() {
                if (!categoriesTrack || !catPrevBtn || !catNextBtn) return;
                const scrollLeft = categoriesTrack.scrollLeft;
                const maxScroll = categoriesTrack.scrollWidth - categoriesTrack.clientWidth;

                // Disable when at edge
                catPrevBtn.disabled = scrollLeft <= 4;
                catNextBtn.disabled = scrollLeft >= maxScroll - 4;

                // If all categories fit without scroll, hide arrows
                if (maxScroll <= 4) {
                    catPrevBtn.style.display = 'none';
                    catNextBtn.style.display = 'none';
                } else {
                    catPrevBtn.style.display = 'flex';
                    catNextBtn.style.display = 'flex';
                }
            }

            if (catPrevBtn && categoriesTrack) {
                catPrevBtn.addEventListener('click', function () {
                    categoriesTrack.scrollBy({ left: -220, behavior: 'smooth' });
                    setTimeout(updateCatArrowStates, 320);
                });
            }

            if (catNextBtn && categoriesTrack) {
                catNextBtn.addEventListener('click', function () {
                    categoriesTrack.scrollBy({ left: 220, behavior: 'smooth' });
                    setTimeout(updateCatArrowStates, 320);
                });
            }

            if (categoriesTrack) {
                categoriesTrack.addEventListener('scroll', updateCatArrowStates, { passive: true });
                window.addEventListener('resize', updateCatArrowStates);
                setTimeout(updateCatArrowStates, 80);
            }

            function scrollActiveCategoryIntoView(btn) {
                if (!btn || !categoriesTrack) return;
                btn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                setTimeout(updateCatArrowStates, 320);
            }

            // Category Tab Clicks
            categoryButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    categoryButtons.forEach(b => {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });
                    this.classList.add('active');
                    this.setAttribute('aria-selected', 'true');

                    currentCategory = this.getAttribute('data-category') || 'all';
                    applyFilters();
                    scrollActiveCategoryIntoView(this);
                });
            });

            // Search Input Event
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    searchQuery = this.value;
                    if (searchClear) {
                        searchClear.style.display = searchQuery ? 'flex' : 'none';
                    }
                    applyFilters();
                });
            }

            // Search Clear Button
            if (searchClear) {
                searchClear.addEventListener('click', function () {
                    if (searchInput) {
                        searchInput.value = '';
                        searchQuery = '';
                        searchInput.focus();
                    }
                    searchClear.style.display = 'none';
                    applyFilters();
                });
            }

            // Global Reset Filters
            window.resetFaqFilters = function () {
                if (searchInput) {
                    searchInput.value = '';
                    searchQuery = '';
                }
                if (searchClear) searchClear.style.display = 'none';

                categoryButtons.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                const allBtn = document.querySelector('.faq-cat-btn[data-category="all"]');
                if (allBtn) {
                    allBtn.classList.add('active');
                    allBtn.setAttribute('aria-selected', 'true');
                    scrollActiveCategoryIntoView(allBtn);
                }
                currentCategory = 'all';
                applyFilters();
            };

            // ── COPY ANCHOR LINK ──
            window.copyFaqAnchor = function (id, btn) {
                const url = window.location.origin + window.location.pathname + '#' + id;
                navigator.clipboard.writeText(url).then(() => {
                    const originalText = btn.innerHTML;
                    btn.innerHTML = `
                        <svg width="13" height="13" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        <span style="color:#16a34a; font-weight:700;">Copied!</span>
                    `;
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                    }, 2200);
                }).catch(() => {
                    // Fallback
                    prompt('Copy this FAQ link:', url);
                });
            };

            // ── DEEP LINK / ANCHOR HASH HANDLING ON PAGE LOAD ──
            function handleUrlHash() {
                const hash = window.location.hash ? window.location.hash.substring(1) : '';
                if (!hash) return;

                const targetItem = document.getElementById(hash);
                if (targetItem) {
                    // Switch category to target item's category or 'all' if hidden
                    const itemCat = targetItem.getAttribute('data-category');
                    if (itemCat && currentCategory !== 'all' && currentCategory !== itemCat) {
                        let matchedBtn = null;
                        categoryButtons.forEach(b => {
                            const isMatch = b.getAttribute('data-category') === itemCat;
                            b.classList.toggle('active', isMatch);
                            b.setAttribute('aria-selected', isMatch ? 'true' : 'false');
                            if (isMatch) matchedBtn = b;
                        });
                        if (matchedBtn) scrollActiveCategoryIntoView(matchedBtn);
                        currentCategory = itemCat;
                        applyFilters();
                    }

                    // Open accordion
                    window.toggleAccordion(hash, true);

                    // Add highlight pulse
                    targetItem.classList.add('highlight-target');
                    setTimeout(() => targetItem.classList.remove('highlight-target'), 2500);

                    // Smooth scroll with offset for sticky navbar
                    setTimeout(() => {
                        const yOffset = -90;
                        const y = targetItem.getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({ top: y, behavior: 'smooth' });
                    }, 150);
                }
            }

            // Run hash handler on load
            handleUrlHash();
            window.addEventListener('hashchange', handleUrlHash);
        });
    </script>
</x-website-layout>
