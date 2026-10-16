<div>
@if($showModal)

<div
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    style="background: rgba(0,0,0,0.20); backdrop-filter: blur(20px); padding: 16px 12px;"
>

    <div class="munsell-form-modal-container" style="background:#fff; border-radius:28px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.3); border:1px solid #f1f5f9; width:100%; max-width:680px; height:auto; max-height:calc(100dvh - 24px); margin:auto; display:flex; flex-direction:column; overflow:hidden;">


        {{-- HEADER --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px 16px 24px; border-bottom:1px solid #f3f4f6; flex-shrink:0;">

            <h2 style="font-size:16px; font-weight:700; color:#111827; margin:0;">
                {{ $colorId ? 'Edit Munsell Profile Description' : 'Add Munsell Color' }}
            </h2>

            <x-close-button wire:click="closeModal" />

        </div>


        {{-- BODY --}}
        <div style="padding:16px 24px; display:flex; flex-direction:column; gap:14px; overflow-y:auto; flex:1;">


            {{-- IMAGE UPLOAD --}}
            <div>
                <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:2px;">Munsell Color Image</div>
                <div style="font-size:12px; color:#9ca3af; margin-bottom:8px;">Upload a photo or take a picture of the Munsell color sample.</div>

                <div style="border:2px dashed #e5e7eb; border-radius:14px; padding:18px 16px; display:flex; flex-direction:column; align-items:center; gap:10px; background:#fafafa;">

                    @if($image)
                        <img src="{{ $image->temporaryUrl() }}" style="width:110px; height:110px; object-fit:cover; border-radius:14px; border:2px solid #22c55e;">
                    @elseif($existingImage)
                        <img src="{{ asset('storage/'.$existingImage) }}" style="width:110px; height:110px; object-fit:cover; border-radius:14px; border:2px solid #22c55e;">
                    @else
                        <div style="width:38px; height:38px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center;">
                            <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <p style="font-size:13px; color:#6b7280; margin:0;">Drag & drop an image here, or use the buttons below</p>

                        <div style="display:flex; gap:8px;">

                            <label style="display:flex; align-items:center; gap:6px; padding:8px 14px; border:1.5px solid #e5e7eb; border-radius:10px; background:#fff; font-size:13px; font-weight:600; color:#374151; cursor:pointer;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload Image
                                <input wire:model="image" type="file" accept="image/*" style="display:none;">
                            </label>

                            <button type="button" style="display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; background:#22c55e; border:none; font-size:13px; font-weight:600; color:#fff; cursor:pointer;">
                                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Take Photo
                            </button>

                        </div>
                    @endif

                </div>
                @error('image')
                    <div style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>


            {{-- SOIL NAME --}}
            <div>
                <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                    Soil Name <span style="color:#ef4444;">*</span>
                </div>
                <input
                    wire:model="color_name"
                    type="text"
                    placeholder="Munchong Clay (Ultisol)"
                    style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; font-family:inherit;"
                    onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                >
                @error('color_name')
                    <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>


            {{-- DESCRIPTION --}}
            <div>
                <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:2px;">
                    Description <span style="color:#ef4444;">*</span>
                </div>
                <div style="font-size:12px; color:#9ca3af; margin-bottom:6px;">Describe the soil type, characteristics, and typical agricultural use.</div>
                <textarea
                    wire:model="description"
                    placeholder="Deep red-yellow clay soil with high iron"
                    style="width:100%; box-sizing:border-box; height:80px; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; resize:none; outline:none; font-family:inherit;"
                    onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                ></textarea>
                @error('description')
                    <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>


            {{-- MUNSELL HUE CODE + FULL MUNSELL CODE --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                {{-- HUE CODE --}}
                <div>
                    <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                        Munsell Hue Code <span style="color:#ef4444;">*</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        @php
                            $swatchBg = '#d97706';
                            if ($hue && $value !== '' && $chroma !== '') {
                                try {
                                    $swatchBg = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($hue, $value, $chroma);
                                } catch (\Exception $e) {}
                            } elseif ($hue) {
                                try {
                                    $swatchBg = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($hue, 5, 6);
                                } catch (\Exception $e) {}
                            }
                        @endphp
                        <div style="width:28px; height:28px; border-radius:8px; background:{{ $swatchBg }}; flex-shrink:0;"></div>
                        <div style="flex:1; position:relative;">
                            <input
                                wire:model.live="hue"
                                type="text"
                                list="munsell-hue-list"
                                placeholder="e.g. 2.5YR"
                                style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 12px; font-size:13.5px; color:#111827; outline:none; background:#fff; font-family:inherit;"
                                onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                            >
                            <datalist id="munsell-hue-list">
                                @foreach($availableHues as $hOpt)
                                    <option value="{{ $hOpt }}">{{ $hOpt }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    @error('hue')
                        <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- FULL CODE --}}
                <div>
                    <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:2px;">
                        Full Munsell Code <span style="color:#ef4444;">*</span>
                    </div>
                    <div style="font-size:11px; color:#9ca3af; margin-bottom:6px;">Format: HUE Value/Chroma</div>
                    <input
                        wire:model="full_code"
                        type="text"
                        placeholder="2.5YR 4/8"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; background:#fff; font-family:inherit;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                    @error('full_code')
                        <div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

            </div>


            {{-- STATUS + ADMIN ID --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                <div>
                    <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Status</div>
                    <select
                        wire:model="status"
                        style="width:100%; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; box-shadow:none; background:#fff; font-family:inherit; accent-color:auto;"
                        onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                        onblur="this.style.outline='none'; this.style.boxShadow='none';"
                    >
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Admin ID</div>
                    <input
                        wire:model="admin_id"
                        type="text"
                        placeholder="e.g. ADM-001"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 14px; font-size:13.5px; color:#111827; outline:none; font-family:inherit;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                </div>

            </div>


        </div>


        {{-- FOOTER --}}
        <div style="padding: 16px 24px 20px; background: #ffffff; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; flex-shrink: 0;">
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
                title="{{ $colorId ? 'Save Changes' : 'Add Munsell Color' }}"
            >
                <span>{{ $colorId ? 'Save Changes' : 'Add Munsell Color' }}</span>
            </button>
        </div>


    </div>

</div>

@endif
</div>
