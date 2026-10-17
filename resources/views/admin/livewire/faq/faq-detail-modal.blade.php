<div>
    @if($showModal && $faq)
    @php
        $adminUser = null;
        $targetAdminId = $faq->admin_id;

        if ($targetAdminId) {
            $adminUser = \App\Models\User::where('last_admin_id', $targetAdminId)->first();
            if (!$adminUser) {
                $adminRec = \App\Models\Admin::where('admin_id', $targetAdminId)->first();
                if ($adminRec?->email) {
                    $adminUser = \App\Models\User::where('email', $adminRec->email)->first();
                }
            }
        }

        if (!$adminUser) {
            $adminUser = auth()->user();
        }

        $adminName  = $adminUser?->name ?? 'Agrico Administrator';
        $adminEmail = $adminUser?->email ?? 'admin@agrico.com';
        $adminId    = $targetAdminId ?: ($adminUser?->Admin_ID ?: 'ADM-001');
    @endphp

    {{-- STYLES FOR FAQ DETAILS MODAL --}}
    <style>
        .faq-detail-modal-container {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 680px;
            margin: auto;
            display: flex;
            flex-direction: column;
            height: auto;
            max-height: min(85vh, calc(100dvh - 24px));
            overflow: hidden;
            position: relative;
        }
        .faq-detail-modal-header {
            padding: 20px 26px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            flex-shrink: 0;
        }
        .faq-detail-modal-header h2 {
            margin: 0;
            font-size: 17.5px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .faq-detail-modal-body {
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
        }
        .faq-detail-modal-body::-webkit-scrollbar {
            width: 6px;
            display: block !important;
        }
        .faq-detail-modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
            margin: 4px 0;
        }
        .faq-detail-modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .faq-detail-modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .faq-detail-modal-footer {
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
            .faq-detail-backdrop {
                padding: 24px 16px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                box-sizing: border-box !important;
            }
            .faq-detail-modal-container {
                width: 100% !important;
                max-width: min(calc(100vw - 32px), 420px) !important;
                height: auto !important;
                max-height: min(72vh, 520px) !important;
                border-radius: 24px !important;
                margin: auto !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
            }
            .faq-detail-modal-header {
                padding: 16px 20px 14px !important;
            }
            .faq-detail-modal-header h2 {
                font-size: 17.5px !important;
                font-weight: 800 !important;
            }
            .faq-detail-modal-body {
                padding: 14px 18px 16px !important;
                gap: 10px !important;
            }
            .faq-detail-modal-footer {
                padding: 12px 18px 14px !important;
            }
        }
    </style>

    <div
        x-data
        x-init="document.body.classList.add('overflow-hidden')"
        x-on:unmount="document.body.classList.remove('overflow-hidden')"
        class="faq-detail-backdrop fixed inset-0 z-50 flex items-center justify-center"
        style="background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 24px 16px;"
    >
        <div class="faq-detail-modal-container">

            {{-- FIXED HEADER --}}
            <div class="faq-detail-modal-header">
                <h2>
                    FAQ Details
                </h2>

                <x-modal-close-button click="closeModal" />
            </div>

            {{-- SCROLLABLE BODY --}}
            <div class="faq-detail-modal-body">

                {{-- QUESTION CARD --}}
                <div style="background:#f0fdf4; border-radius:16px; padding:18px 20px; border:1px solid #dcfce7;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                        <span style="font-size:11px; font-weight:700; text-transform:uppercase; tracking:0.05em; color:#22c55e;">
                            {{ $faq->category ?? 'General' }}
                        </span>
                        <div style="display:inline-flex; align-items:center; gap:5px;">
                            <span style="width:7px; height:7px; border-radius:50%; background:{{ ($faq->status ?? 'Active') === 'Active' ? '#22c55e' : '#94a3b8' }};"></span>
                            <span style="font-size:12px; font-weight:700; color:{{ ($faq->status ?? 'Active') === 'Active' ? '#16a34a' : '#64748b' }};">
                                {{ $faq->status ?? 'Active' }}
                            </span>
                        </div>
                    </div>
                    <div style="font-size:16px; font-weight:800; color:#111827; line-height:1.4;">
                        {{ $faq->question }}
                    </div>
                </div>

                {{-- ANSWER SECTION --}}
                <div>
                    <label style="display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#6b7280; margin-bottom:6px;">
                        Answer
                    </label>
                    <div style="background:#f9fafb; border:1px solid #f3f4f6; border-radius:14px; padding:16px 18px; font-size:13.5px; color:#374151; line-height:1.6; white-space:pre-line; word-break:break-word; overflow-wrap:anywhere;">{{ trim($faq->answer) }}</div>
                </div>

                {{-- LAST UPDATED AUDIT CARD --}}
                @php
                    $rawAdminId = $faq->last_admin_id ?? null;
                    $hasAdmin = !empty($rawAdminId) && $rawAdminId !== '—';
                    $isModified = $hasAdmin && !empty($faq->updated_at);

                    $adminIdDisplay = $isModified ? \App\Models\Admin::formatId($rawAdminId) : '—';
                    $changedDateDisplay = $isModified ? $faq->updated_at->timezone('Asia/Manila')->format('M d, Y h:i A') : '—';
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
            <div class="faq-detail-modal-footer">
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
