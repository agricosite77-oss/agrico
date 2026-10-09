<div>
@if($showModal && $color)

@php
    $swatchColor = !empty($color->hex_code)
        ? (str_starts_with($color->hex_code, '#') ? strtolower($color->hex_code) : '#' . strtolower($color->hex_code))
        : \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex(
            $color->hue,
            $color->value,
            $color->chroma,
            $color->hex_code ?? null
        );

    $targetAdminId = $color->admin_id;
    $adminName = 'Agrico Administrator';
    $adminEmail = 'admin@agrico.com';
    $adminId = $targetAdminId ?: 'ADM-001';

    if ($targetAdminId) {
        $cleanId = 'ADM-' . str_pad((int)preg_replace('/\D/', '', $targetAdminId), 3, '0', STR_PAD_LEFT);
        $adminRec = \App\Models\Admin::where('admin_id', $targetAdminId)
            ->orWhere('admin_id', $cleanId)
            ->first();

        if ($adminRec) {
            $adminName = $adminRec->full_name;
            $adminEmail = $adminRec->email;
            $adminId = $adminRec->admin_id;
        } else {
            $userRec = \App\Models\User::where('email', $targetAdminId)->first();
            if ($userRec) {
                $adminName = $userRec->name;
                $adminEmail = $userRec->email;
            }
        }
    }
@endphp

{{-- STYLES FOR MUNSELL RECORD DETAILS MODAL --}}
<style>
    .munsell-detail-backdrop {
        position: fixed !important;
        inset: 0 !important;
        z-index: 9999 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
        padding: 24px 16px !important;
    }
    .munsell-detail-modal-container {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 28px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 680px;
        margin: auto;
        display: flex;
        flex-direction: column;
        height: 75vh;
        max-height: 75vh;
        overflow: hidden;
        position: relative;
    }
    .munsell-detail-modal-header {
        padding: 20px 26px 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        flex-shrink: 0;
    }
    .munsell-detail-modal-header h2 {
        margin: 0;
        font-size: 17.5px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
    }
    .munsell-detail-modal-body {
        padding: 18px 24px 20px;
        overflow-y: auto;
        flex: 1 1 auto;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        gap: 12px;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }
    .munsell-detail-modal-body::-webkit-scrollbar {
        width: 6px;
        display: block !important;
    }
    .munsell-detail-modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 9999px;
        margin: 4px 0;
    }
    .munsell-detail-modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .munsell-detail-modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .munsell-detail-modal-footer {
        padding: 16px 24px 20px;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 540px) {
        .munsell-detail-backdrop {
            padding: 16px 14px !important;
        }
        .munsell-detail-modal-container {
            height: 75vh !important;
            max-height: 75vh !important;
            border-radius: 24px !important;
            margin: auto !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
        }
        .munsell-detail-modal-header {
            padding: 16px 20px 14px !important;
        }
        .munsell-detail-modal-header h2 {
            font-size: 17.5px !important;
            font-weight: 800 !important;
        }
        .munsell-detail-modal-body {
            padding: 14px 18px 16px !important;
            gap: 10px !important;
        }
        .munsell-detail-modal-footer {
            padding: 12px 18px 14px !important;
        }
    }
</style>

{{-- BACKDROP OVERLAY --}}
<div
    x-data
    x-init="document.body.classList.add('overflow-hidden')"
    x-on:unmount="document.body.classList.remove('overflow-hidden')"
    class="munsell-detail-backdrop fixed inset-0 z-[9999] flex items-center justify-center"
    style="background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 24px 16px;"
>

    {{-- MODAL CONTAINER --}}
    <div class="munsell-detail-modal-container">

        {{-- FIXED HEADER --}}
        <div class="munsell-detail-modal-header">
            <h2>
                Munsell Color Details
            </h2>
            <x-modal-close-button click="closeModal" />
        </div>

        {{-- SCROLLABLE BODY --}}
        <div class="munsell-detail-modal-body">

            {{-- 1. COLOR SWATCH HEADER CARD --}}
            <div style="background:#ffffff; border:1.5px solid #e2e8f0; border-radius:20px; padding:18px 22px; display:flex; align-items:center; justify-content:space-between; gap:18px; box-shadow:0 4px 16px rgba(0,0,0,0.02);">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:56px; height:56px; border-radius:16px; background-color:{{ $swatchColor }}; flex-shrink:0; border:1px solid rgba(0,0,0,0.12); box-shadow:0 6px 18px rgba(0,0,0,0.15);"></div>

                    <div>
                        <div style="font-size:22px; font-weight:900; color:#0f172a; line-height:1.1; letter-spacing:-0.02em;">
                            {{ $color->full_code }}
                        </div>
                        <div style="font-size:13px; font-weight:500; color:#94a3b8; margin-top:3px;">
                            Munsell Soil Color Notation
                        </div>
                    </div>
                </div>

                <div style="background:#dcfce7; color:#16a34a; border-radius:9999px; padding:5px 14px; font-size:12.5px; font-weight:700; display:inline-flex; align-items:center; gap:6px; flex-shrink:0;">
                    <span style="width:7px; height:7px; border-radius:50%; background:#22c55e; box-shadow:0 0 6px #22c55e;"></span>
                    <span>Active Standard</span>
                </div>
            </div>

            {{-- 1.5. SOIL ORGANIC INDEX & SOIL TYPE (2 EQUAL COLUMNS) --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                {{-- SOIL ORGANIC INDEX --}}
                <div style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:14px 16px;">
                    <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                        Soil Organic Index (SOC)
                    </div>
                    <div style="font-size:15px; font-weight:800; color:#0f172a; line-height:1.3;">
                        {{ $color->soil_organic_index ?: '—' }}
                    </div>
                </div>

                {{-- SOIL TYPE --}}
                <div style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:14px 16px;">
                    <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                        Soil Type / General Group
                    </div>
                    <div style="font-size:15px; font-weight:800; color:#0f172a; line-height:1.3;">
                        {{ $color->soil_type ?: '—' }}
                    </div>
                </div>
            </div>

            {{-- 2. HUE, VALUE, CHROMA METRICS (3 EQUAL COLUMNS) --}}
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                {{-- HUE --}}
                <div style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:14px 16px;">
                    <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                        Hue
                    </div>
                    <div style="font-size:18px; font-weight:800; color:#0f172a;">
                        {{ $color->hue }}
                    </div>
                </div>

                {{-- VALUE --}}
                <div style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:14px 16px;">
                    <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                        Value
                    </div>
                    <div style="font-size:18px; font-weight:800; color:#0f172a;">
                        {{ $color->value }}
                    </div>
                </div>

                {{-- CHROMA --}}
                <div style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:14px 16px;">
                    <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                        Chroma
                    </div>
                    <div style="font-size:18px; font-weight:800; color:#0f172a;">
                        {{ $color->chroma }}
                    </div>
                </div>
            </div>

            {{-- 3. RESPONSIVE CREATED DATE & ADMIN OWNER ROW --}}
            <style>
                .munsell-detail-row-grid {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    width: 100%;
                }
                .munsell-created-card { order: 2; }
                .munsell-owner-card { order: 1; }

                @media (min-width: 480px) {
                    .munsell-detail-row-grid {
                        display: grid !important;
                        grid-template-columns: 1fr 1.5fr !important;
                        gap: 12px !important;
                    }
                    .munsell-created-card { order: 1 !important; }
                    .munsell-owner-card { order: 2 !important; }
                }
            </style>

            <div class="munsell-detail-row-grid">
                {{-- Created Date --}}
                <div class="munsell-created-card" style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:12px 16px; display:flex; flex-direction:column; justify-content:center;">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:3px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em;">Created</span>
                    </div>
                    <div style="font-size:14px; font-weight:800; color:#0f172a;">
                        {{ $color->created_at?->format('m/d/Y') ?: '08/16/2026' }}
                    </div>
                </div>

                {{-- Admin Record Owner --}}
                <div class="munsell-owner-card" style="border:1.5px solid #e2e8f0; background:#f8fafc; border-radius:16px; padding:10px 14px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0; flex:1;">
                        <div style="width:38px; height:38px; border-radius:50%; background:#22c55e !important; color:#ffffff !important; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:15px; flex-shrink:0; box-shadow:0 3px 8px rgba(34,197,94,0.35);">
                            {{ strtoupper(substr($adminName, 0, 1)) }}
                        </div>
                        <div style="min-width:0; flex:1;">
                            <div style="font-size:13px; font-weight:800; color:#0f172a; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $adminName }}
                            </div>
                            <div style="font-size:11.5px; font-weight:500; color:#64748b; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $adminEmail }}
                            </div>
                        </div>
                    </div>

                    <div style="background:#dcfce7; color:#16a34a; border-radius:9999px; padding:4px 10px; font-size:11px; font-weight:800; font-family:monospace; flex-shrink:0; border:1px solid #bbf7d0;">
                        {{ $adminId }}
                    </div>
                </div>
            </div>

            {{-- 4. COLOR NAME CARD --}}
            <div style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:14px 18px;">
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; text-align:left;">
                    Color Name
                </div>
                <div style="font-size:16px; font-weight:800; color:#0f172a; text-align:left; line-height:1.3;">
                    {{ $color->color_name ?: 'No color name specified.' }}
                </div>
            </div>

            {{-- 5. DESCRIPTION CARD --}}
            <div style="border:1.5px solid #e2e8f0; background:#ffffff; border-radius:16px; padding:14px 18px;">
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px; text-align:left;">
                    Description
                </div>
                <div style="font-size:14px; font-weight:500; color:#475569; line-height:1.55; text-align:left;">
                    {{ $color->description ?: 'No description available for this record.' }}
                </div>
            </div>

            {{-- 6. LAST UPDATED AUDIT CARD --}}
            @php
                $rawAdminId = $color->last_admin_id ?? null;
                $hasAdmin = !empty($rawAdminId) && $rawAdminId !== '—';
                $isModified = $hasAdmin && !empty($color->updated_at);

                $adminIdDisplay = $isModified ? \App\Models\Admin::formatId($rawAdminId) : '—';
                $changedDateDisplay = $isModified ? $color->updated_at->timezone('Asia/Manila')->format('M d, Y h:i A') : '—';
            @endphp
            <div style="border:1.5px solid #f1f5f9; background:#fafafa; border-radius:16px; padding:12px 16px; display:flex; align-items:center; justify-content:space-between; margin-top:2px;">
                <div>
                    <span style="font-size:10.5px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.04em; display:block;">
                        Last updated:
                    </span>
                    <span style="font-size:12.5px; font-weight:700; color:#64748b; margin-top:2px; display:block;">
                        {{ $isModified ? $changedDateDisplay : '—' }}
                    </span>
                </div>
                @if($isModified)
                    <div style="font-size:11.5px; font-weight:700; color:#64748b; font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; background:#ffffff; border:1px solid #e2e8f0; padding:4px 8px; border-radius:6px;">
                        {{ $adminIdDisplay }}
                    </div>
                @endif
            </div>

        </div>

        {{-- FIXED FOOTER --}}
        <div class="munsell-detail-modal-footer">
            <button
                type="button"
                wire:click="closeModal"
                class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                title="Cancel"
            >
                Cancel
            </button>
            <button
                wire:click="edit"
                type="button"
                class="flex-1 py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer flex items-center justify-center gap-2 text-center"
                style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);"
                title="Edit Record"
            >
                <span>Edit Record</span>
            </button>
        </div>

    </div>

</div>

@endif
</div>
