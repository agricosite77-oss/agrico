<x-website-layout>
    <x-slot name="title">Munsell Color Chart</x-slot>

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

        /* Content wrapper */
        .munsell-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 36px 24px 80px;
        }

        /* Filter bar */
        .munsell-filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .munsell-search {
            flex: 1;
            min-width: 280px;
            max-width: 480px;
            position: relative;
        }
        .munsell-search input {
            width: 100%;
            padding: 11px 40px 11px 46px;
            border: 1px solid rgba(142, 182, 155, 0.3);
            border-radius: 16px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            outline: none;
            color: #DAF1DE;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            background: rgba(11, 43, 38, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            box-sizing: border-box;
        }
        .munsell-search input::placeholder {
            color: #8EB69B;
            font-weight: 500;
        }
        .munsell-search input:hover {
            border-color: rgba(142, 182, 155, 0.5);
            background: rgba(11, 43, 38, 0.8);
        }
        .munsell-search input:focus {
            border-color: rgba(142, 182, 155, 0.6);
            background: rgba(11, 43, 38, 0.95);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }
        .munsell-search svg {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            stroke: #8EB69B;
            z-index: 2;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .munsell-search input:focus ~ svg,
        .munsell-search:focus-within svg {
            stroke: #4ADE80 !important;
            transform: translateY(-50%) scale(1.05) !important;
        }

        .munsell-clear-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: transparent;
            border: none;
            color: #8EB69B;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 0;
        }
        .munsell-clear-btn:hover {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            transform: translateY(-50%) scale(1.08);
        }
        html.light-mode .munsell-clear-btn {
            color: #94a3b8 !important;
        }
        html.light-mode .munsell-clear-btn:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* Search Suggestions Dropdown */
        .munsell-suggestions-box {
            position: absolute;
            top: calc(100% + 8px);
            left: 0; right: 0;
            background: rgba(11, 43, 38, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(142, 182, 155, 0.35);
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5), 0 0 20px rgba(35, 83, 71, 0.3);
            z-index: 100;
            overflow: hidden;
            display: none;
            flex-direction: column;
            animation: suggestFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes suggestFadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .munsell-suggestions-header {
            padding: 10px 16px 8px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #8EB69B;
            border-bottom: 1px solid rgba(142, 182, 155, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .munsell-suggestions-list {
            max-height: 240px;
            overflow-y: auto;
            padding: 4px 0;
        }
        .munsell-suggestion-item {
            padding: 9px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .munsell-suggestion-item:hover {
            background: rgba(35, 83, 71, 0.45);
        }
        .munsell-suggest-title {
            font-size: 13.5px;
            font-weight: 700;
            color: #DAF1DE;
        }
        .munsell-suggest-code {
            font-size: 11.5px;
            font-weight: 700;
            color: #4ADE80;
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.25);
            padding: 2px 7px;
            border-radius: 6px;
        }

        /* Hue filter buttons (Matching Header navigation style) */
        .hue-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .hue-pill {
            padding: 8px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
            background: rgba(11, 43, 38, 0.5);
            font-size: 14px;
            font-weight: 600;
            color: #8EB69B;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .hue-pill:hover {
            background: rgba(35, 83, 71, 0.4);
            color: #DAF1DE;
            border-color: rgba(142, 182, 155, 0.3);
        }
        .hue-pill.active {
            background: linear-gradient(135deg, #235347, #163832) !important;
            border-color: #8EB69B !important;
            color: #DAF1DE !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 14px rgba(35, 83, 71, 0.4) !important;
        }

        /* Color stats */
        .munsell-stats {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }
        .munsell-stat-chip {
            background: rgba(11, 43, 38, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(142, 182, 155, 0.25);
            border-radius: 12px;
            padding: 9px 18px;
            font-size: 13px;
            color: #8EB69B;
            font-weight: 600;
        }
        .munsell-stat-chip span {
            color: #4ADE80;
            font-weight: 800;
            margin-left: 4px;
        }

        /* Grid */
        .munsell-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        .munsell-card {
            background: linear-gradient(145deg, #0B2B26 0%, #163832 100%);
            border-radius: 18px;
            border: 1px solid rgba(142, 182, 155, 0.22);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: default;
        }
        .munsell-card:hover {
            border-color: #DAF1DE !important;
            box-shadow: 0 14px 36px rgba(0, 0, 0, 0.45);
            transform: translateY(-2px);
        }
        .munsell-swatch {
            height: 110px;
            width: 100%;
            position: relative;
            box-shadow: none;
        }
        .munsell-swatch-badge {
            position: absolute;
            bottom: 10px; right: 10px;
            background: rgba(5, 31, 32, 0.88);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(142, 182, 155, 0.35);
            border-radius: 8px;
            padding: 3px 10px;
            font-size: 11.5px;
            font-weight: 800;
            color: #DAF1DE;
            letter-spacing: 0.02em;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .munsell-body {
            padding: 16px 18px 18px;
        }
        .munsell-body h4 {
            font-size: 16px;
            font-weight: 800;
            color: #FFFFFF !important;
            margin: 0 0 8px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.01em;
        }
        .munsell-body .code {
            font-size: 12.5px;
            font-weight: 800;
            color: #4ADE80 !important;
            background: rgba(74, 222, 128, 0.2) !important;
            border: 1.5px solid rgba(74, 222, 128, 0.4) !important;
            display: inline-block;
            padding: 4px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            letter-spacing: 0.02em;
        }
        .munsell-meta {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .munsell-meta span {
            font-size: 12px;
            color: #DAF1DE !important;
            background: rgba(5, 31, 32, 0.8) !important;
            border: 1px solid rgba(142, 182, 155, 0.4) !important;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 700;
        }
        .munsell-desc {
            font-size: 12.5px;
            color: #A7F3D0 !important;
            margin-top: 10px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-weight: 600;
        }

        /* Empty state */
        .ws-empty {
            text-align: center;
            padding: 80px 24px;
            color: #8EB69B;
            font-size: 15px;
        }

        /* No-results message */
        #noResults {
            display: none;
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 24px;
            color: #8EB69B;
            font-size: 15px;
        }

        /* Smooth Entrance & Micro-Animations */
        @keyframes munsellFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .munsell-wrapper, .munsell-card, .hue-pill, .munsell-stat-chip {
            animation: munsellFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ── LIGHT MODE OVERRIDES (HIGH CONTRAST & ANIMATIONS) ── */
        html.light-mode .ws-page-hero {
            background: linear-gradient(135deg, #DAF1DE 0%, #EEF7F1 100%) !important;
            border-bottom-color: rgba(142, 182, 155, 0.4) !important;
        }
        html.light-mode .ws-page-title { color: #051F20 !important; }
        html.light-mode .ws-page-sub   { color: #163832 !important; }
        html.light-mode .ws-breadcrumb { color: #235347 !important; }
        html.light-mode .ws-breadcrumb a { color: #051F20 !important; }
        html.light-mode .ws-breadcrumb-current,
        html.light-mode .ws-breadcrumb span { color: #051F20 !important; font-weight: 700 !important; }

        html.light-mode .munsell-stat-chip {
            background: #FFFFFF !important;
            border: 1.5px solid #e2e8f0 !important;
            color: #64748b !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
        }
        html.light-mode .munsell-stat-chip span {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        html.light-mode .munsell-search input {
            background: #FFFFFF !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            color: #1e293b !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        html.light-mode .munsell-search input::placeholder {
            color: #94a3b8 !important;
            font-weight: 500 !important;
        }
        html.light-mode .munsell-search svg {
            stroke: #94a3b8 !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        html.light-mode .munsell-search input:focus,
        html.light-mode .munsell-search:focus-within input {
            border-color: #22c55e !important;
            box-shadow: 0 0 0 3.5px rgba(34, 197, 94, 0.22), 0 4px 12px rgba(34, 197, 94, 0.12) !important;
            background: #FFFFFF !important;
        }
        html.light-mode .munsell-search:focus-within svg {
            stroke: #16a34a !important;
            transform: translateY(-50%) scale(1.1) !important;
        }

        .munsell-clear-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(142, 182, 155, 0.25);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            font-size: 12px;
            color: #DAF1DE;
            font-weight: bold;
            line-height: 1;
            padding: 0;
            z-index: 3;
            transition: all 0.2s ease;
        }
        .munsell-clear-btn:hover {
            background: rgba(142, 182, 155, 0.45);
            color: #ffffff;
        }

        html.light-mode .munsell-clear-btn {
            background: #f1f5f9 !important;
            color: #64748b !important;
        }
        html.light-mode .munsell-clear-btn:hover {
            background: #e2e8f0 !important;
            color: #1e293b !important;
        }

        html.light-mode .munsell-suggestions-box {
            background: #FFFFFF !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12) !important;
            border-radius: 16px !important;
        }
        html.light-mode .munsell-suggestions-header {
            color: #16a34a !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        html.light-mode .munsell-suggestion-item:hover {
            background: #f0fdf4 !important;
        }
        html.light-mode .munsell-suggest-title {
            color: #0f172a !important;
        }

        html.light-mode .hue-pill {
            background: #FFFFFF !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #334155 !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03) !important;
            font-weight: 600 !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        html.light-mode .hue-pill:hover {
            background: #f0fdf4 !important;
            border-color: #86efac !important;
            color: #16a34a !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .hue-pill.active {
            background: #22c55e !important;
            border: none !important;
            color: #FFFFFF !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35) !important;
            transform: translateY(-1px) !important;
        }

        html.light-mode .munsell-card {
            background: #FFFFFF !important;
            border: 1.5px solid #e2e8f0 !important;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05) !important;
            border-radius: 18px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        html.light-mode .munsell-card:hover {
            border-color: #22c55e !important;
            box-shadow: 0 8px 24px rgba(34, 197, 94, 0.15) !important;
        }
        html.light-mode .munsell-swatch-badge {
            background: rgba(15, 23, 42, 0.75) !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }
        html.light-mode .munsell-body h4 {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light-mode .munsell-body .code {
            color: #16a34a !important;
            background: #eefbe7 !important;
            border: 1px solid #86efac !important;
            font-weight: 700 !important;
        }
        html.light-mode .munsell-meta span {
            color: #334155 !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            font-weight: 600 !important;
        }
        html.light-mode .munsell-desc {
            color: #64748b !important;
            font-weight: 600 !important;
        }
        html.light-mode .ws-empty,
        html.light-mode #noResults {
            color: #475569 !important;
        }
    </style>

    {{-- Page Hero --}}
    <div class="ws-page-hero">
        <div class="ws-page-hero-inner ui-reveal">
            <div class="ws-breadcrumb">
                <a href="{{ route('website.home') }}">Home</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                <span class="ws-breadcrumb-current">Munsell Color Chart</span>
            </div>
            <h1 class="ws-page-title">Munsell Soil Color Chart</h1>
            <p class="ws-page-sub">Browse and filter all Munsell color classifications used in soil analysis.</p>
        </div>
    </div>

    <div class="munsell-wrapper">

        {{-- Filter Bar --}}
        <div class="munsell-filter-bar ui-reveal">
            <div class="munsell-search">
                <input type="text" id="munsellSearch" placeholder="Search by color name, Munsell code, or hue group..." autocomplete="off" />
                <svg width="18" height="18" fill="none" stroke="#8EB69B" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <button type="button" id="munsellSearchClearBtn" class="munsell-clear-btn" onclick="clearMunsellSearch()" title="Clear Search" aria-label="Clear Search">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Live Suggestions Dropdown --}}
                <div class="munsell-suggestions-box" id="munsellSuggestionsBox">
                    <div class="munsell-suggestions-header">
                        <span>Quick Suggestions</span>
                        <span style="font-size:10.5px; opacity:0.7;">Click to filter</span>
                    </div>
                    <div class="munsell-suggestions-list" id="munsellSuggestionsList"></div>
                </div>
            </div>
        </div>

        {{-- Hue Pills --}}
        @if($hues->count() > 0)
        <div class="hue-pills ui-reveal" id="huePills">
            <button class="hue-pill all-pill active" data-hue="all" onclick="filterHue('all', this)">All</button>
            @foreach($hues as $hue)
                <button class="hue-pill" data-hue="{{ $hue }}"
                        onclick="filterHue('{{ $hue }}', this)">{{ $hue }}</button>
            @endforeach
        </div>
        @endif

        {{-- Stats --}}
        <div class="munsell-stats ui-reveal">
            <div class="munsell-stat-chip">
                Total Colors: <span>{{ $colors->count() }}</span>
            </div>
            <div class="munsell-stat-chip">
                Hue Groups: <span>{{ $hues->count() }}</span>
            </div>
            <div class="munsell-stat-chip" id="visibleCount">
                Showing: <span>{{ $colors->count() }}</span>
            </div>
        </div>

        {{-- Grid --}}
        @if($colors->count() > 0)
            <div class="munsell-grid" id="munsellGrid">
                @foreach($colors as $color)
                @php
                    $cleanHue = trim(str_replace('/', '', $color->hue ?? '10YR'));
                    $cleanVal = trim($color->value ?? '4');
                    $cleanChroma = trim($color->chroma ?? '3');
                    $displayCode = $cleanHue . ' ' . $cleanVal . '/' . $cleanChroma;

                    $swatchBg = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex(
                        $cleanHue,
                        $cleanVal,
                        $cleanChroma
                    );
                @endphp
                <div class="munsell-card ui-scroll-reveal"
                     data-hue="{{ $cleanHue }}"
                     data-name="{{ $color->color_name ?? ('Munsell ' . $cleanHue) }}"
                     data-soil-name="{{ strtolower($color->color_name ?? ('Munsell ' . $cleanHue)) }}"
                     data-munsell-code="{{ strtolower($displayCode) }}"
                     data-code="{{ $displayCode }}"
                     data-description="{{ strtolower($color->description ?? '') }}"
                     data-search="{{ strtolower(($color->color_name ?? '') . ' ' . $displayCode . ' ' . $cleanHue . ' ' . ($color->description ?? '')) }}">

                    {{-- Color Swatch --}}
                    <div class="munsell-swatch"
                         style="background: {{ $swatchBg }};">
                    </div>

                    <div class="munsell-body">
                        <h4>{{ $color->color_name ?? ('Munsell ' . $cleanHue) }}</h4>
                        <div class="code">{{ $displayCode }}</div>
                        <div class="munsell-meta">
                            <span>H: {{ $cleanHue }}</span>
                            <span>V: {{ $cleanVal }}</span>
                            <span>C: {{ $cleanChroma }}</span>
                        </div>
                        @if($color->description)
                            <p class="munsell-desc">{{ $color->description }}</p>
                        @endif
                    </div>
                </div>
                @endforeach

                <div id="noResults">
                    <svg width="48" height="48" fill="none" stroke="#8EB69B" stroke-width="1.5"
                         viewBox="0 0 24 24" style="margin:0 auto 16px;display:block;">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    No colors match your search.
                </div>
            </div>
        @else
            <div class="ws-empty">
                <p>No Munsell colors have been added yet.</p>
            </div>
        @endif

    </div>

    <script>
        let currentHue = 'all';
        let searchTerm = '';
        let isExactMatch = false;

        function applyFilters() {
            const cards = document.querySelectorAll('#munsellGrid .munsell-card');
            let visible = 0;
            cards.forEach(card => {
                const hue = (card.dataset.hue || '').toLowerCase();
                const soilName = (card.dataset.soilName || card.dataset.name || '').toLowerCase();
                const munsellCode = (card.dataset.munsellCode || card.dataset.code || '').toLowerCase();
                const desc = (card.dataset.description || '').toLowerCase();
                const searchStr = (card.dataset.search || '').toLowerCase();

                const hueMatch  = currentHue === 'all' || hue === currentHue.toLowerCase() || card.dataset.hue === currentHue;

                let textMatch = false;
                if (searchTerm === '') {
                    textMatch = true;
                } else if (isExactMatch) {
                    // Exact match comparison when a suggestion dropdown item is selected
                    textMatch = (soilName === searchTerm) || (munsellCode === searchTerm);
                } else {
                    // Standard substring search when user types manually
                    textMatch = soilName.includes(searchTerm) || 
                        munsellCode.includes(searchTerm) || 
                        hue.includes(searchTerm) || 
                        desc.includes(searchTerm) || 
                        searchStr.includes(searchTerm);
                }

                if (hueMatch && textMatch) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            const statsSpans = document.querySelectorAll('.munsell-stats .munsell-stat-chip span');
            if (statsSpans && statsSpans.length > 0) {
                const showingSpan = statsSpans[statsSpans.length - 1];
                if (showingSpan) showingSpan.textContent = visible;
            }
            const noResults = document.getElementById('noResults');
            if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
        }

        function filterHue(hue, btn) {
            currentHue = hue;
            document.querySelectorAll('.hue-pill').forEach(p => p.classList.remove('active'));
            if (btn) btn.classList.add('active');
            applyFilters();
        }

        const searchInput = document.getElementById('munsellSearch');
        const suggestionsBox = document.getElementById('munsellSuggestionsBox');
        const suggestionsList = document.getElementById('munsellSuggestionsList');

        function showSuggestions() {
            if (!suggestionsBox) return;
            renderMatchingSuggestions();
            suggestionsBox.style.display = 'flex';
        }

        function hideSuggestions() {
            if (suggestionsBox) suggestionsBox.style.display = 'none';
        }

        function renderMatchingSuggestions() {
            if (!suggestionsList) return;
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const cards = Array.from(document.querySelectorAll('#munsellGrid .munsell-card'));
            
            const matches = [];
            const seen = new Set();

            cards.forEach(card => {
                const name = card.dataset.name || '';
                const code = card.dataset.code || '';
                const search = card.dataset.search || '';
                if (!query || search.includes(query)) {
                    if (!seen.has(name) && matches.length < 8) {
                        seen.add(name);
                        matches.push({ name, code });
                    }
                }
            });

            if (matches.length === 0) {
                suggestionsList.innerHTML = '<div style="padding:14px; text-align:center; color:#8EB69B; font-size:12.5px;">No matching color suggestions found</div>';
                return;
            }

            suggestionsList.innerHTML = matches.map(item => `
                <div class="munsell-suggestion-item" onclick="selectSuggestion('${item.name.replace(/'/g, "\\'")}')">
                    <div class="munsell-suggest-title">${item.name}</div>
                </div>
            `).join('');
        }

        function selectSuggestion(val) {
            if (searchInput) {
                searchInput.value = val;
                searchTerm = val.toLowerCase().trim();
                isExactMatch = true; // Set flag for exact equality checks
                const clearBtn = document.getElementById('munsellSearchClearBtn');
                if (clearBtn) clearBtn.style.display = 'flex';
                applyFilters();
            }
            hideSuggestions();
        }

        function clearMunsellSearch() {
            if (searchInput) searchInput.value = '';
            const clearBtn = document.getElementById('munsellSearchClearBtn');
            if (clearBtn) clearBtn.style.display = 'none';
            searchTerm = '';
            isExactMatch = false; // Reset flag for standard substring searches
            applyFilters();
            hideSuggestions();
        }

        const handleSearchUpdate = e => {
            searchTerm = e.target.value.toLowerCase().trim();
            isExactMatch = false; // Reset flag for standard substring searches when typing
            const clearBtn = document.getElementById('munsellSearchClearBtn');
            if (clearBtn) clearBtn.style.display = searchTerm ? 'flex' : 'none';
            applyFilters();
            renderMatchingSuggestions();
            showSuggestions();
        };

        searchInput?.addEventListener('focus', showSuggestions);
        searchInput?.addEventListener('input', handleSearchUpdate);
        searchInput?.addEventListener('change', handleSearchUpdate);

        // Hide suggestions on outside click
        document.addEventListener('click', e => {
            if (!e.target.closest('.munsell-search')) {
                hideSuggestions();
            }
        });

        // Hide on Escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') hideSuggestions();
        });
    </script>

</x-website-layout>
