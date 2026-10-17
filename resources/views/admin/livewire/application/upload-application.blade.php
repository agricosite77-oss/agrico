<div style="display:flex; flex-direction:column; gap:24px; width:100%;" class="animate-in fade-in duration-500">

    <style>
        .upload-app-grid {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 24px;
            width: 100%;
        }
        .upload-app-card {
            background: #ffffff;
            border-radius: 24px;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            padding: 28px 24px;
            box-sizing: border-box;
            width: 100%;
        }
        .version-item-card {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            padding-bottom: 24px;
            border-bottom: 1px solid #f1f5f9;
            box-sizing: border-box;
        }
        .version-item-card:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }
        .version-header-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            flex-wrap: wrap;
        }
        .version-header-left {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            flex: 1 1 200px;
            min-width: 0;
        }
        .version-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }
        .app-maintenance-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid;
            white-space: nowrap;
        }
        .version-release-notes {
            width: 100%;
            font-size: 13px;
            color: #475569;
            font-weight: 500;
            line-height: 1.55;
            word-break: break-word;
            overflow-wrap: anywhere;
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid #f1f5f9;
            box-sizing: border-box;
        }
        @media (max-width: 640px) {
            .upload-app-grid {
                gap: 16px !important;
            }
            .upload-app-card {
                padding: 20px 16px !important;
                border-radius: 20px !important;
            }
            .upload-app-header-controls {
                width: 100% !important;
                justify-content: flex-start !important;
            }
            .version-header-actions {
                width: 100% !important;
                justify-content: space-between !important;
                margin-top: 4px !important;
            }
            .app-maintenance-btn {
                flex: 1 1 auto !important;
                justify-content: center !important;
            }
        }
    </style>

    {{-- 1. TOP HEADER BAR --}}
    <div class="ui-reveal upload-app-header" style="--i:0; display:flex; align-items:center; justify-content:space-between; width:100%; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="ui-reveal-text" style="--i:0; font-size:24px; font-weight:900; color:#0f172a; margin:0; line-height:1; letter-spacing:-0.5px;">
                Upload Application
            </h1>
            <p class="ui-reveal-text" style="--i:0; font-size:13px; color:#64748b; font-weight:500; margin-top:6px; margin-bottom:0;">
                Manage and deploy AgriCo mobile application versions
            </p>
        </div>

        <div class="upload-app-header-controls" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold text-gray-700 bg-white border border-gray-200 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition"
                title="Back to Dashboard"
            >
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Dashboard
            </a>

            {{-- TOP RIGHT BADGE --}}
            <div class="ui-reveal-text" style="--i:0; background:transparent; color:#22c55e; font-weight:700; border-radius:9999px; padding:6px 18px; font-size:13px; border:1px solid #bbf7d0; display:inline-flex; align-items:center; gap:6px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>Current: {{ $currentActive ? $currentActive->version : 'v2.4.0' }}</span>
            </div>
        </div>
    </div>

    {{-- FLASH NOTIFICATION --}}
    @if (session()->has('message'))
        <div style="background:#ffffff; border:1px solid #bbf7d0; color:#166534; padding:12px 18px; border-radius:16px; font-size:14px; font-weight:600; display:flex; align-items:center; gap:8px; box-shadow:0 1px 3px rgba(0,0,0,0.03); width:100%; box-sizing:border-box;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- MAINTENANCE MODE ACTIVE ALERT BANNER --}}
    @if(\App\Models\Application::isMaintenanceMode())
        <div style="background:#fff7ed; border:1px solid #fed7aa; color:#c2410c; padding:14px 18px; border-radius:16px; font-size:13.5px; font-weight:600; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; width:100%; box-sizing:border-box;">
            <div style="display:flex; align-items:center; gap:10px;">
                <svg width="20" height="20" fill="none" stroke="#ea580c" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span><strong>System Maintenance / Updating Mode is Active:</strong> Website application downloads are paused and users will receive a maintenance notice.</span>
            </div>
            <button
                type="button"
                wire:click="toggleMaintenance"
                style="padding:6px 14px; background:#ea580c; color:#ffffff; border:none; border-radius:9999px; font-size:12px; font-weight:700; cursor:pointer; flex-shrink:0; transition:opacity 0.2s ease;"
                onmouseover="this.style.opacity='0.9';"
                onmouseout="this.style.opacity='1';"
            >
                Disable Maintenance Mode
            </button>
        </div>
    @endif

    {{-- 2. MAIN FULL-WIDTH RESPONSIVE FLEX LAYOUT --}}
    <div class="upload-app-grid" style="display:flex; flex-wrap:wrap; align-items:flex-start; gap:24px; width:100%;">

        {{-- LEFT COLUMN: UPLOAD NEW VERSION FORM CARD --}}
        <div class="ui-reveal upload-app-card" style="--i:1; flex:1 1 340px; width:100%; background:#ffffff; border-radius:24px; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.02); padding:28px 24px; box-sizing:border-box;">
            <h2 class="ui-reveal-text" style="--i:1; font-size:16px; font-weight:800; color:#0f172a; margin:0 0 20px 0;">
                Upload New Version
            </h2>

            <form wire:submit.prevent="uploadVersion" style="display:flex; flex-direction:column; gap:16px; width:100%;">
                {{-- DRAG & DROP FILE DROPZONE WITH ANIMATION --}}
                <style>
                    .agrico-dropzone {
                        display: block;
                        border: 2px dashed {{ $appFile ? '#22c55e' : '#cbd5e1' }};
                        border-radius: 20px;
                        padding: 32px 20px;
                        text-align: center;
                        background: {{ $appFile ? '#F1FBF1' : '#ffffff' }};
                        cursor: pointer;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        width: 100%;
                        box-sizing: border-box;
                    }
                    .agrico-dropzone:hover {
                        border-color: #22c55e !important;
                        background: #F1FBF1 !important;
                        box-shadow: 0 8px 24px rgba(68, 175, 53, 0.15) !important;
                        transform: translateY(-3px) !important;
                    }
                    .agrico-dropzone:hover .drop-icon-circle {
                        background: #dcfce7 !important;
                        color: #22c55e !important;
                        transform: scale(1.15) translateY(-3px) !important;
                        box-shadow: 0 4px 14px rgba(34, 197, 94, 0.25) !important;
                    }
                    .agrico-dropzone:hover .drop-title-text {
                        color: #22c55e !important;
                    }
                    .drop-icon-circle {
                        width: 52px;
                        height: 52px;
                        border-radius: 50%;
                        background: {{ $appFile ? '#dcfce7' : '#f8fafc' }};
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 12px auto;
                        color: {{ $appFile ? '#22c55e' : '#94a3b8' }};
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        box-shadow: {{ $appFile ? '0 4px 12px rgba(34,197,94,0.2)' : 'none' }};
                    }
                    @keyframes dropCheckPulse {
                        0% { transform: scale(0); opacity: 0; }
                        70% { transform: scale(1.25); }
                        100% { transform: scale(1); opacity: 1; }
                    }
                    @keyframes dropBadgePop {
                        0% { transform: scale(0.9) translateY(4px); opacity: 0; }
                        100% { transform: scale(1) translateY(0); opacity: 1; }
                    }
                </style>
                <div style="position:relative; width:100%;">
                    <label for="appFileInput" class="agrico-dropzone">
                        <div class="drop-icon-circle">
                            @if($appFile)
                                <svg width="26" height="26" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="animation: dropCheckPulse 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            @else
                                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 9l5-5 5 5M12 4v12"/>
                                </svg>
                            @endif
                        </div>

                        <span class="drop-title-text" style="font-size:15px; font-weight:800; color:{{ $appFile ? '#22c55e' : '#1e293b' }}; display:block; transition:color 0.2s ease;">
                            {{ $appFile ? 'APK/IPA File Selected!' : 'Drop APK/IPA here' }}
                        </span>
                        <span style="font-size:12px; color:#64748b; font-weight:500; display:block; margin-top:4px;">
                            {{ $appFile ? 'Click to change file' : 'or click to browse from device' }}
                        </span>

                        @if ($appFile)
                            <div style="margin-top:14px; background:#22c55e; color:#ffffff; font-size:12.5px; font-weight:700; border-radius:9999px; padding:6px 16px; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(22,163,74,0.3); animation: dropBadgePop 0.3s ease-out;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                                </svg>
                                {{ $appFile->getClientOriginalName() }}
                            </div>
                        @endif
                    </label>

                    <input
                        id="appFileInput"
                        type="file"
                        wire:model="appFile"
                        accept=".apk,.ipa"
                        class="hidden"
                        style="display:none;"
                    />
                </div>

                {{-- VERSION NUMBER --}}
                <div style="width:100%;">
                    <label style="font-size:12px; font-weight:800; color:#475569; display:block; margin-bottom:6px;">
                        Version Number
                    </label>
                    <input
                        type="text"
                        wire:model="versionNumber"
                        placeholder="e.g. v1.0.4"
                        style="width:100%; border:1px solid #e2e8f0; border-radius:12px; background:#ffffff; padding:11px 16px; font-size:14px; font-weight:500; color:#1e293b; outline:none; transition:all 0.2s ease; box-sizing:border-box;"
                        onfocus="this.style.borderColor='#93e098'; this.style.boxShadow='0 0 0 3px rgba(147,224,152,0.2)';"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                        required
                    />
                    @error('versionNumber')
                        <span style="font-size:12px; color:#ef4444; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- GITHUB RELEASE ASSET URL (OPTIONAL) --}}
                <div style="width:100%;">
                    <label style="font-size:12px; font-weight:800; color:#475569; display:block; margin-bottom:6px;">
                        GitHub Release APK URL <span style="font-size:11px; color:#94a3b8; font-weight:500;">(Optional if uploading file above)</span>
                    </label>
                    <input
                        type="url"
                        wire:model="githubUrl"
                        placeholder="https://github.com/agricosite77-oss/agrico-releases/releases/download/v1.0.4/agrico-app-v1.0.4.apk"
                        style="width:100%; border:1px solid #e2e8f0; border-radius:12px; background:#ffffff; padding:11px 16px; font-size:13px; font-weight:500; color:#1e293b; outline:none; transition:all 0.2s ease; box-sizing:border-box;"
                        onfocus="this.style.borderColor='#93e098'; this.style.boxShadow='0 0 0 3px rgba(147,224,152,0.2)';"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                    />
                    @error('githubUrl')
                        <span style="font-size:12px; color:#ef4444; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- RELEASE NOTES --}}
                <div style="width:100%;">
                    <label style="font-size:12px; font-weight:800; color:#475569; display:block; margin-bottom:6px;">
                        Release Notes
                    </label>
                    <textarea
                        wire:model="releaseNotes"
                        rows="4"
                        placeholder="Describe what's new in this version..."
                        style="width:100%; border:1px solid #e2e8f0; border-radius:12px; background:#ffffff; padding:11px 16px; font-size:14px; font-weight:500; color:#1e293b; outline:none; transition:all 0.2s ease; box-sizing:border-box; resize:vertical;"
                        onfocus="this.style.borderColor='#93e098'; this.style.boxShadow='0 0 0 3px rgba(147,224,152,0.2)';"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                        required
                    ></textarea>
                    @error('releaseNotes')
                        <span style="font-size:12px; color:#ef4444; font-weight:600; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- SUBMIT BUTTON --}}
                <button
                    type="submit"
                    style="width:100%; background:#93e098; color:#ffffff; font-size:15px; font-weight:800; border-radius:12px; padding:12px; border:none; cursor:pointer; transition:all 0.2s ease; text-align:center; margin-top:6px;"
                    onmouseover="this.style.background='#7ec983';"
                    onmouseout="this.style.background='#93e098';"
                >
                    Deploy New Version
                </button>
            </form>
        </div>

        {{-- RIGHT COLUMN: VERSION HISTORY CARD --}}
        <div class="ui-reveal upload-app-card" style="--i:2; flex:1.4 1 360px; width:100%; background:#ffffff; border-radius:24px; border:none; box-shadow:0 1px 3px rgba(0,0,0,0.02); padding:28px 24px; box-sizing:border-box;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
                <h2 class="ui-reveal-text" style="--i:2; font-size:16px; font-weight:800; color:#0f172a; margin:0;">
                    Version History
                </h2>
                <span class="ui-reveal-num" style="--i:2; font-size:12px; font-weight:500; color:#94a3b8;" x-data x-init="animateCount($el, {{ count($applications) }}, { suffix: ' versions', delay: 800 })">
                    {{ count($applications) }} versions
                </span>
            </div>

            {{-- VERSION LIST ITEMS --}}
            <div style="display:flex; flex-direction:column; gap:20px; width:100%;">
                @forelse ($applications as $app)
                    <div class="version-item-card">

                        {{-- TOP ROW: ICON + TITLE/BADGES + ACTION BUTTONS --}}
                        <div class="version-header-row">

                            {{-- LEFT: ICON + VERSION INFO --}}
                            <div class="version-header-left">
                                {{-- FILE ICON --}}
                                @if($app->status === 'ACTIVE')
                                    <div style="width:40px; height:40px; border-radius:12px; background:#dcfce7; color:#22c55e; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                @else
                                    <div style="width:40px; height:40px; border-radius:12px; background:#f1f5f9; color:#94a3b8; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- DETAILS --}}
                                <div style="flex:1; min-width:0;">
                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                        <span style="font-size:16px; font-weight:900; color:#0f172a; line-height:1.2;">
                                            {{ $app->version }}
                                        </span>

                                        @if($app->status === 'ACTIVE')
                                            <span style="background:#dcfce7; color:#22c55e; font-size:10px; font-weight:800; border-radius:9999px; padding:2px 8px; border:1px solid #bbf7d0; text-transform:uppercase;">
                                                ACTIVE
                                            </span>
                                        @else
                                            <span style="background:#f1f5f9; color:#64748b; font-size:10px; font-weight:800; border-radius:9999px; padding:2px 8px; border:1px solid #e2e8f0; text-transform:uppercase;">
                                                ARCHIVED
                                            </span>
                                        @endif

                                        @if(str_contains($app->file_path ?? '', 'github.com'))
                                            <a href="{{ $app->file_path }}" target="_blank" rel="noopener noreferrer" style="background:#f8fafc; color:#0f172a; font-size:10px; font-weight:700; border-radius:9999px; padding:2px 8px; border:1px solid #e2e8f0; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:border-color 0.2s ease;" onmouseover="this.style.borderColor='#94a3b8';" onmouseout="this.style.borderColor='#e2e8f0';" title="View GitHub Release Asset">
                                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                                                GitHub Release
                                            </a>
                                        @endif
                                    </div>

                                    {{-- FILENAME & SIZE --}}
                                    <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0; word-break:break-all;">
                                        {{ $app->filename ?: ('agrico-app-' . $app->version . '.apk') }} · {{ $app->file_size ?: '48.0 MB' }}
                                    </p>
                                </div>
                            </div>

                            {{-- RIGHT ACTIONS CLUSTER --}}
                            <div class="version-header-actions">
                                {{-- MAINTENANCE TOGGLE BUTTON (ACTIVE ONLY) --}}
                                @if($app->status === 'ACTIVE')
                                    @php
                                        $isMaintenanceActive = \App\Models\Application::isMaintenanceMode();
                                    @endphp
                                    <button
                                        type="button"
                                        wire:click="toggleMaintenance"
                                        class="app-maintenance-btn"
                                        style="{{ $isMaintenanceActive ? 'background:#fff7ed; color:#ea580c; border-color:#fed7aa; box-shadow:0 1px 3px rgba(234,88,12,0.15);' : 'background:#f0fdf4; color:#16a34a; border-color:#bbf7d0;' }}"
                                        title="{{ $isMaintenanceActive ? 'System currently in Maintenance / Updating mode. Click to Disable.' : 'System Active. Click to Enable Maintenance / Updating mode.' }}"
                                    >
                                        <span style="width:7px; height:7px; border-radius:50%; background:{{ $isMaintenanceActive ? '#ea580c' : '#22c55e' }}; display:inline-block; flex-shrink:0; {{ $isMaintenanceActive ? 'box-shadow:0 0 0 3px rgba(234,88,12,0.25);' : '' }}"></span>
                                        <span>{{ $isMaintenanceActive ? 'Maintenance: Enabled' : 'Maintenance: Disabled' }}</span>
                                    </button>
                                @endif

                                {{-- DOWNLOAD BUTTON --}}
                                <button
                                    type="button"
                                    wire:click="downloadFile({{ $app->application_id }})"
                                    style="width:34px; height:34px; border-radius:10px; background:#f8fafc; border:1px solid #e2e8f0; color:#475569; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s ease; flex-shrink:0;"
                                    onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'; this.style.borderColor='#cbd5e1';"
                                    onmouseout="this.style.background='#f8fafc'; this.style.color='#475569'; this.style.borderColor='#e2e8f0';"
                                    title="Download File"
                                >
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                                    </svg>
                                </button>

                                {{-- DELETE BUTTON --}}
                                @if($app->status !== 'ACTIVE')
                                    <button
                                        type="button"
                                        wire:click="deleteVersion({{ $app->application_id }})"
                                        style="width:34px; height:34px; border-radius:10px; background:#fef2f2; border:1px solid #fee2e2; color:#ef4444; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s ease; flex-shrink:0;"
                                        onmouseover="this.style.background='#fee2e2'; this.style.color='#dc2626';"
                                        onmouseout="this.style.background='#fef2f2'; this.style.color='#ef4444';"
                                        title="Delete Version"
                                    >
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- RELEASE NOTES: FULL 100% WIDTH --}}
                        <div class="version-release-notes">
                            {{ $app->release_notes }}
                        </div>

                        {{-- METADATA: DATE & UPLOADER --}}
                        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:6px; font-size:11px; color:#94a3b8; font-weight:500;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            <span>{{ $app->created_at ? $app->created_at->format('Y-m-d h:i A') : '2026-04-01 09:00 AM' }}</span>
                            <span>by</span>
                            <span style="font-weight:600; color:#64748b;">{{ $app->uploaded_by ?: 'Administrator' }}</span>
                        </div>

                    </div>
                @empty
                    <div style="padding:48px; text-align:center; color:#94a3b8; font-size:14px; font-weight:500;">
                        No application versions found.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
