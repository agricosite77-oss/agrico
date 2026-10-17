<div>
@if($showModal)

{{-- STYLES FOR MUNSELL CREATE MODAL --}}
<style>
    .munsell-form-backdrop {
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
    .munsell-form-modal-container {
        background: #ffffff !important;
        border: 1px solid #f1f5f9;
        border-radius: 28px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
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
    .munsell-form-modal-header {
        padding: 20px 26px 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        flex-shrink: 0;
    }
    .munsell-form-modal-header h2 {
        margin: 0;
        font-size: 17.5px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
    }
    .munsell-form-modal-body {
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
    .munsell-form-modal-body::-webkit-scrollbar {
        width: 6px;
        display: block !important;
    }
    .munsell-form-modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 9999px;
        margin: 4px 0;
    }
    .munsell-form-modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .munsell-form-modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .munsell-form-modal-footer {
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
    @media (max-width: 640px) {
        .munsell-form-backdrop {
            padding: 24px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-sizing: border-box !important;
            z-index: 9999 !important;
        }
        .munsell-form-modal-container {
            width: 100% !important;
            max-width: min(calc(100vw - 32px), 420px) !important;
            height: auto !important;
            max-height: min(80vh, 590px) !important;
            border-radius: 24px !important;
            margin: auto !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
        }
        .munsell-form-modal-header {
            padding: 15px 18px 13px !important;
        }
        .munsell-form-modal-header h2 {
            font-size: 16.5px !important;
            font-weight: 800 !important;
        }
        .munsell-form-modal-body {
            padding: 14px 14px 16px !important;
            gap: 10px !important;
        }
        .munsell-form-modal-footer {
            padding: 12px 16px 14px !important;
        }
    }
</style>

{{-- BACKDROP OVERLAY --}}
<div
    x-data="{
        init() {
            document.body.classList.add('overflow-hidden');
            this.$nextTick(() => {
                if (typeof window.initMunsellPicker === 'function') {
                    window.initMunsellPicker();
                }
            });
        }
    }"
    x-init="init()"
    x-on:unmount="document.body.classList.remove('overflow-hidden')"
    class="munsell-form-backdrop fixed inset-0 z-[9999] flex items-center justify-center"
    style="background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 24px 16px;"
>

    <div class="munsell-form-modal-container">

            {{-- FIXED HEADER --}}
            <div class="munsell-form-modal-header">
                <h2>
                    Add Munsell Color
                </h2>
                <x-modal-close-button click="closeModal" />
            </div>

            {{-- SCROLLABLE BODY --}}
            <div class="munsell-form-modal-body">

            {{-- NEW: IMAGE COLOR PICKER --}}
            <div style="border:1.5px solid #e5e7eb; border-radius:14px; padding:12px; background:#f9fafb;">
                <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:8px;">
                    Pick Color From Soil Photo <span style="font-size:11px; color:#9ca3af; font-weight:500;">(optional)</span>
                </div>

                <div id="munsellUploadZone" style="display:block; border:2px dashed #cbd5e1; border-radius:12px; padding:20px; text-align:center; cursor:pointer;" onclick="document.getElementById('munsellImageInput').click()">
                    <div style="font-size:12.5px; font-weight:700; color:#334155;">Click to upload a soil photo</div>
                    <div style="font-size:11px; color:#94a3b8; margin-top:2px;">JPG or PNG</div>
                </div>
                <input type="file" id="munsellImageInput" accept="image/*" style="display:none;">

                <div id="munsellCanvasWrap" style="display:none; position:relative; border-radius:10px; overflow:hidden; border:1.5px solid #e2e8f0; cursor:crosshair; margin-top:8px;">
                    <canvas id="munsellImageCanvas" style="display:block; max-width:100%; height:auto;"></canvas>
                    <div id="munsellMagnifier" style="position:absolute; display:none; width:90px; height:90px; border-radius:50%; border:3px solid #ffffff; box-shadow:0 4px 16px rgba(0,0,0,0.35); pointer-events:none; z-index:20; background-repeat:no-repeat;"></div>
                </div>

                <div id="munsellPickedPreview" style="display:none; align-items:center; gap:10px; margin-top:8px; padding:8px 10px; background:#ffffff; border-radius:10px; border:1px solid #f1f5f9;">
                    <div id="munsellPickedSwatch" style="width:32px; height:32px; border-radius:8px; border:2px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.12); flex-shrink:0;"></div>
                    <div style="font-size:12px; font-weight:600; color:#334155; flex:1;">
                        Picked: <span id="munsellPickedHexText">—</span>
                    </div>
                    <button type="button" id="munsellUseColorBtn" style="background:#22c55e; color:#fff; border:none; border-radius:9999px; padding:6px 14px; font-size:11.5px; font-weight:700; cursor:pointer;">
                        Use this color
                    </button>
                </div>
            </div>

            {{-- 1. HUE, VALUE, CHROMA (3-COLUMN GRID) --}}
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">

                {{-- HUE --}}
                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                        Hue <span style="color:#ef4444;">*</span>
                    </div>
                    <select
                        wire:model.live="hue"
                        style="width:100%; height:38px; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:0 30px 0 12px; font-size:13px; color:#111827; background:#fff url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M19 9l-7 7-7-7\'/></svg>') no-repeat right 12px center; appearance:none; -webkit-appearance:none; font-family:inherit; cursor:pointer; outline:none; transition:all 0.2s ease;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                        <option value="">Select Hue</option>
                        @foreach($availableHues as $hOpt)
                            <option value="{{ $hOpt }}">{{ $hOpt }}</option>
                        @endforeach
                    </select>
                    @error('hue')
                        <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- VALUE --}}
                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                        Value <span style="color:#ef4444;">*</span>
                    </div>
                    <input
                        wire:model.live="value"
                        type="number"
                        min="0"
                        max="10"
                        step="0.5"
                        placeholder="0 - 10"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 10px; font-size:13px; color:#111827; outline:none; background:#fff; font-family:inherit;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                    @error('value')
                        <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- CHROMA --}}
                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                        Chroma <span style="color:#ef4444;">*</span>
                    </div>
                    <input
                        wire:model.live="chroma"
                        type="number"
                        min="0"
                        max="20"
                        step="1"
                        placeholder="0 - 20"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 10px; font-size:13px; color:#111827; outline:none; background:#fff; font-family:inherit;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                    @error('chroma')
                        <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                    @enderror
                </div>

            </div>


            {{-- 2. FULL MUNSELL CODE --}}
            <div>
                <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:2px;">
                    Full Munsell Code <span style="color:#ef4444;">*</span>
                </div>
                <div style="font-size:11px; color:#9ca3af; margin-bottom:4px;">Auto-generated: HUE Value/Chroma</div>
                <input
                    wire:model="full_code"
                    type="text"
                    readonly
                    placeholder="Auto-generated (e.g. 2.5YR 5/6)"
                    style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#4b5563; outline:none; background:#f9fafb; font-family:inherit; cursor:not-allowed;"
                >
                @error('full_code')
                    <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                @enderror
            </div>




            @php
                $previewColor = !empty($hex_code)
                    ? (str_starts_with($hex_code, '#') ? strtolower($hex_code) : '#' . strtolower($hex_code))
                    : null;
                if (empty($previewColor)) {
                    if ($hue && $value !== '' && $chroma !== '') {
                        try {
                            $previewColor = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($hue, $value, $chroma);
                        } catch (\Exception $e) {}
                    } elseif ($hue) {
                        try {
                            $previewColor = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($hue, 5, 6);
                        } catch (\Exception $e) {}
                    }
                }
                $previewColor = $previewColor ?: '#d97706';
            @endphp

            <div style="display:flex; align-items:center; gap:12px; background:#f9fafb; border-radius:14px; padding:10px 14px; border:1px solid #f1f5f9;">
                <div style="position:relative; width:42px; height:42px; border-radius:12px; overflow:hidden; flex-shrink:0; border:1.5px solid rgba(0,0,0,0.08); box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                    <div style="width:100%; height:100%; background:{{ $previewColor }}; transition:background 0.3s ease;"></div>
                </div>

                <div>
                    <div style="font-size:13px; font-weight:600; color:#374151;">Color Preview</div>
                    <div style="font-size:12px; color:#9ca3af; margin-top:1px;">
                        <span>{{ $full_code ?: ($hue ? $hue : 'Fill in Hue, Value, Chroma above') }}</span>
                    </div>
                </div>
            </div>


            {{-- 3. COLOR NAME --}}
            <div>
                <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                    Color Name <span style="color:#ef4444;">*</span>
                </div>
                <input
                    wire:model="color_name"
                    type="text"
                    placeholder="e.g. Light Brownish Gray"
                    style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#111827; outline:none; font-family:inherit;"
                    onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                >
                @error('color_name')
                    <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                @enderror
            </div>


            {{-- 4. DESCRIPTION --}}
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                    <div style="font-size:12.5px; font-weight:600; color:#374151;">
                        Description <span style="color:#ef4444;">*</span>
                    </div>
                    <div style="font-size:11px; color:#9ca3af; font-weight:500;" x-data="{}" x-text="($wire.description && $wire.description.trim().length > 0 ? $wire.description.trim().split(/\s+/).filter(Boolean).length : 0) + ' words'">
                    </div>
                </div>
                <textarea
                    wire:model="description"
                    placeholder="Describe the soil type, profile characteristics, horizon layers, and detailed properties..."
                    style="width:100%; box-sizing:border-box; height:100px; min-height:90px; max-height:280px; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 12px; font-size:13px; color:#111827; resize:vertical; outline:none; font-family:inherit; line-height:1.5; overflow-y:auto;"
                    onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                ></textarea>
                @error('description')
                    <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                @enderror
            </div>




            {{-- 7. STATUS + ADMIN ID --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">

                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">Status</div>
                    <select
                        wire:model="status"
                        style="width:100%; height:38px; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:0 30px 0 12px; font-size:13px; color:#111827; background:#fff url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' fill=\'none\' stroke=\'%236b7280\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M19 9l-7 7-7-7\'/></svg>') no-repeat right 12px center; appearance:none; -webkit-appearance:none; font-family:inherit; cursor:pointer; outline:none; transition:all 0.2s ease;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">Admin ID</div>
                    <input
                        wire:model="admin_id"
                        type="text"
                        readonly
                        placeholder="Auto-assigned"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#6b7280; outline:none; background:#f9fafb; font-family:inherit; cursor:not-allowed;"
                    >
                </div>

            </div>


        </div>


        {{-- FIXED FOOTER --}}
        <div class="munsell-form-modal-footer">
            <button
                type="button"
                wire:click="save"
                style="width:100%; padding:10px 18px; border-radius:9999px; background:linear-gradient(135deg, #22c55e, #16a34a) !important; color:#ffffff !important; border:none; font-size:14px; font-weight:700; cursor:pointer; box-shadow:0 4px 14px rgba(34, 197, 94, 0.35); text-align:center; transition:all 0.2s ease;"
                onmouseover="this.style.background='linear-gradient(135deg, #16a34a, #15803d) !important'; this.style.transform='translateY(-1px)';"
                onmouseout="this.style.background='linear-gradient(135deg, #22c55e, #16a34a) !important'; this.style.transform='translateY(0)';"
            >
                <span>Add Munsell Color</span>
            </button>
        </div>


    </div>




</div>

@endif

<script>
(function () {
    function initMunsellPicker() {
        const input = document.getElementById('munsellImageInput');
        const uploadZone = document.getElementById('munsellUploadZone');
        const canvasWrap = document.getElementById('munsellCanvasWrap');
        const canvas = document.getElementById('munsellImageCanvas');
        if (!input || !canvas) return; // modal not rendered yet

        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        const magnifier = document.getElementById('munsellMagnifier');
        const previewBox = document.getElementById('munsellPickedPreview');
        const swatch = document.getElementById('munsellPickedSwatch');
        const hexText = document.getElementById('munsellPickedHexText');
        const useColorBtn = document.getElementById('munsellUseColorBtn');

        let img = new Image();
        let picked = null; // { hex, r, g, b }

        input.onchange = function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (evt) {
                img.onload = function () {
                    const maxW = 500;
                    const scale = Math.min(1, maxW / img.width);
                    canvas.width = img.width * scale;
                    canvas.height = img.height * scale;
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    if (uploadZone) uploadZone.style.display = 'none';
                    if (canvasWrap) canvasWrap.style.display = 'block';
                };
                img.src = evt.target.result;
            };
            reader.readAsDataURL(file);
        };

        function rgbToHex(r, g, b) {
            return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('').toUpperCase();
        }

        if (canvasWrap) {
            canvasWrap.onmousemove = function (e) {
                const rect = canvas.getBoundingClientRect();
                const x = Math.floor(e.clientX - rect.left);
                const y = Math.floor(e.clientY - rect.top);
                if (x < 0 || y < 0 || x >= canvas.width || y >= canvas.height) {
                    if (magnifier) magnifier.style.display = 'none';
                    return;
                }
                if (magnifier) {
                    magnifier.style.display = 'block';
                    magnifier.style.left = (e.clientX - rect.left - 45) + 'px';
                    magnifier.style.top = (e.clientY - rect.top - 45) + 'px';
                    const zoom = 6;
                    magnifier.style.backgroundImage = `url(${img.src})`;
                    magnifier.style.backgroundSize = (canvas.width * zoom) + 'px ' + (canvas.height * zoom) + 'px';
                    magnifier.style.backgroundPosition = (-(x * zoom - 45)) + 'px ' + (-(y * zoom - 45)) + 'px';
                }
            };

            canvasWrap.onmouseleave = function () {
                if (magnifier) magnifier.style.display = 'none';
            };

            canvasWrap.onclick = function (e) {
                const rect = canvas.getBoundingClientRect();
                const x = Math.floor(e.clientX - rect.left);
                const y = Math.floor(e.clientY - rect.top);
                if (x < 0 || y < 0 || x >= canvas.width || y >= canvas.height) return;

                const pixel = ctx.getImageData(x, y, 1, 1).data;
                const r = pixel[0], g = pixel[1], b = pixel[2];
                const hex = rgbToHex(r, g, b);

                picked = { hex, r, g, b };

                if (swatch) swatch.style.background = hex;
                if (hexText) hexText.textContent = hex;
                if (previewBox) previewBox.style.display = 'flex';
            };
        }

        if (useColorBtn) {
            useColorBtn.onclick = function () {
                if (!picked) return;
                // Calls the existing Livewire method on MunsellCreateModal
                if (window.Livewire) {
                    window.Livewire.dispatch('apply-picked-color', {
                        hex: picked.hex,
                    });
                }
            };
        }
    }

    window.initMunsellPicker = initMunsellPicker;
    document.addEventListener('DOMContentLoaded', initMunsellPicker);
    document.addEventListener('livewire:navigated', initMunsellPicker);
    if (window.Livewire) {
        window.Livewire.hook('morph.updated', () => initMunsellPicker());
    } else {
        document.addEventListener('livewire:init', () => {
            window.Livewire.hook('morph.updated', () => initMunsellPicker());
        });
    }
})();
</script>
</div>
