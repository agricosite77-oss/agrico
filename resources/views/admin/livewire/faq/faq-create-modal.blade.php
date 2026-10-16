<div>
    @if($showModal)
    {{-- STYLES FOR FAQ CREATE MODAL --}}
    <style>
        .faq-form-modal-container {
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
        .faq-form-modal-header {
            padding: 20px 26px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            flex-shrink: 0;
        }
        .faq-form-modal-header h2 {
            margin: 0;
            font-size: 17.5px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .faq-form-modal-body {
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
        .faq-form-modal-body::-webkit-scrollbar {
            width: 6px;
            display: block !important;
        }
        .faq-form-modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
            margin: 4px 0;
        }
        .faq-form-modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .faq-form-modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .faq-form-modal-footer {
            padding: 14px 24px 16px;
            background: #fafafa;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
        }
        @media (max-width: 540px) {
            .faq-form-backdrop {
                padding: 16px 14px !important;
            }
            .faq-form-modal-container {
                height: auto !important;
                max-height: calc(100dvh - 20px) !important;
                border-radius: 24px !important;
                margin: auto !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
            }
            .faq-form-modal-header {
                padding: 16px 20px 14px !important;
            }
            .faq-form-modal-header h2 {
                font-size: 17.5px !important;
                font-weight: 800 !important;
            }
            .faq-form-modal-body {
                padding: 14px 18px 16px !important;
                gap: 10px !important;
            }
            .faq-form-modal-footer {
                padding: 12px 18px 14px !important;
            }
        }
    </style>

    <div
        x-data
        x-init="document.body.classList.add('overflow-hidden')"
        x-on:unmount="document.body.classList.remove('overflow-hidden')"
        class="faq-form-backdrop fixed inset-0 z-50 flex items-center justify-center"
        style="background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 24px 16px;"
    >
        <div class="faq-form-modal-container">

            {{-- FIXED HEADER --}}
            <div class="faq-form-modal-header">
                <h2>
                    Add Frequently Asked Question
                </h2>

                <x-modal-close-button click="closeModal" />
            </div>

            {{-- SCROLLABLE BODY --}}
            <div class="faq-form-modal-body">

                {{-- QUESTION --}}
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                        Question <span style="color:#ef4444;">*</span>
                    </label>
                    <input
                        wire:model="question"
                        type="text"
                        placeholder="e.g. How do I scan soil samples using the app?"
                        style="width:100%; border:1px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; transition:all 0.2s ease; box-sizing:border-box;"
                        onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"
                    />
                    @error('question') <span style="font-size:12px; color:#ef4444; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>

                {{-- CATEGORY --}}
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                        Category <span style="color:#ef4444;">*</span>
                    </label>
                    <select
                        wire:model="category"
                        style="width:100%; border:1px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; cursor:pointer; transition:all 0.2s ease; box-sizing:border-box; background:#fff;"
                        onfocus="this.style.borderColor='#22c55e';"
                        onblur="this.style.borderColor='#e5e5e7eb';"
                    >
                        @foreach($availableCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category') <span style="font-size:12px; color:#ef4444; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>

                {{-- ANSWER --}}
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                        Answer <span style="color:#ef4444;">*</span>
                    </label>
                    <textarea
                        wire:model="answer"
                        rows="4"
                        placeholder="Provide a detailed and clear explanation..."
                        style="width:100%; border:1px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; transition:all 0.2s ease; box-sizing:border-box; resize:vertical;"
                        onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3px rgba(68,175,53,0.15)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"
                    ></textarea>
                    @error('answer') <span style="font-size:12px; color:#ef4444; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>

                {{-- STATUS AND ADMIN ROW --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    {{-- STATUS --}}
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                            Status
                        </label>
                        <select
                            wire:model="status"
                            style="width:100%; border:1px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; cursor:pointer; box-sizing:border-box; background:#fff;"
                        >
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- ADMIN ID --}}
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                            Admin ID
                        </label>
                        <input
                            type="text"
                            wire:model="admin_id"
                            readonly
                            style="width:100%; border:1px solid #f1f5f9; border-radius:12px; padding:10px 14px; font-size:13.5px; font-family:monospace; color:#64748b; background:#f8fafc; outline:none; box-sizing:border-box;"
                        />
                    </div>
                </div>

            </div>

            {{-- FIXED FOOTER --}}
            <div class="faq-form-modal-footer" style="padding: 16px 24px 20px; background: #ffffff; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; flex-shrink: 0;">
                <button
                    type="button"
                    wire:click="closeModal"
                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                    title="Cancel"
                >
                    Cancel
                </button>
                <button
                    wire:click="save"
                    type="button"
                    class="flex-1 py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer flex items-center justify-center text-center"
                    style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);"
                    title="Save FAQ"
                >
                    <span>Save FAQ</span>
                </button>
            </div>

        </div>
    </div>
    @endif
</div>
