<div>
@if($showModal)

{{-- STYLES FOR EDIT SOIL RECORD MODAL (MATCHING STUDENT RECORDS POPUP SIZE & MOBILE RESPONSIVE) --}}
<style>
    .soil-form-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 100002;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        box-sizing: border-box;
    }
    .soil-form-modal-container {
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
    .soil-form-modal-header {
        padding: 20px 26px 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        flex-shrink: 0;
    }
    .soil-form-modal-header h2 {
        margin: 0;
        font-size: 17.5px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
    }
    .soil-form-modal-body {
        padding: 18px 24px 20px;
        overflow-y: auto;
        flex: 1 1 auto;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        gap: 14px;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
    }
    .soil-form-modal-body::-webkit-scrollbar {
        width: 6px;
        display: block !important;
    }
    .soil-form-modal-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 9999px;
        margin: 4px 0;
    }
    .soil-form-modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .soil-form-modal-body::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .soil-form-modal-footer {
        padding: 14px 24px 16px;
        background: #fafafa;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        width: 100%;
        box-sizing: border-box;
    }
    @media (max-width: 540px) {
        .soil-form-backdrop {
            padding: 16px 14px !important;
        }
        .soil-form-modal-container {
            height: 75vh !important;
            max-height: 75vh !important;
            border-radius: 24px !important;
            margin: auto !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
        }
        .soil-form-modal-header {
            padding: 14px 18px 12px !important;
        }
        .soil-form-modal-header h2 {
            font-size: 16px !important;
        }
        .soil-form-modal-body {
            padding: 12px 14px 14px !important;
            gap: 10px !important;
            flex: 1 1 auto !important;
            max-height: none !important;
        }
        .soil-form-modal-footer {
            padding: 10px 14px 12px !important;
        }
        .soil-form-grid-2 {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
        }
        .soil-form-grid-3 {
            grid-template-columns: 1fr 1fr 1fr !important;
            gap: 6px !important;
        }
    }
</style>

<div
    x-data="{
        showCameraModal: false,
        mediaStream: null,
        async startWebcam() {
            this.showCameraModal = true;
            try {
                this.mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                this.$refs.webcamVideo.srcObject = this.mediaStream;
            } catch (err) {
                console.error(err);
                this.$refs.cameraFileInput.click();
                this.showCameraModal = false;
            }
        },
        stopWebcam() {
            if (this.mediaStream) {
                this.mediaStream.getTracks().forEach(track => track.stop());
                this.mediaStream = null;
            }
            this.showCameraModal = false;
        },
        snapPhoto() {
            const video = this.$refs.webcamVideo;
            const canvas = document.createElement('canvas');
            const maxW = 1280;
            let w = video.videoWidth || 640;
            let h = video.videoHeight || 480;
            if (w > maxW) {
                h = Math.round((h * maxW) / w);
                w = maxW;
            }
            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, w, h);
            canvas.toBlob((blob) => {
                if (!blob) return;
                const file = new File([blob], 'camera-photo.jpg', { type: 'image/jpeg' });
                $wire.upload('image', file,
                    (success) => {
                        this.stopWebcam();
                    },
                    (error) => {
                        console.error('Photo upload error:', error);
                    }
                );
            }, 'image/jpeg', 0.88);
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.type && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const maxDim = 1200;
                        let w = img.width;
                        let h = img.height;
                        if (w > maxDim || h > maxDim) {
                            if (w > h) {
                                h = Math.round((h * maxDim) / w);
                                w = maxDim;
                            } else {
                                w = Math.round((w * maxDim) / h);
                                h = maxDim;
                            }
                        }
                        canvas.width = w;
                        canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, w, h);
                        canvas.toBlob((blob) => {
                            if (!blob) return;
                            const compressedFile = new File([blob], file.name || 'soil-image.jpg', { type: 'image/jpeg' });
                            $wire.upload('image', compressedFile,
                                (success) => {},
                                (error) => { console.error('Upload error:', error); }
                            );
                        }, 'image/jpeg', 0.85);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                $wire.upload('image', file);
            }
        }
    }"
    x-init="document.body.classList.add('overflow-hidden')"
    x-on:unmount="document.body.classList.remove('overflow-hidden')"
    class="soil-form-backdrop fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 100002; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; padding: 16px; box-sizing: border-box;"
>

    <div class="soil-form-modal-container">


        {{-- HEADER --}}
        <div class="soil-form-modal-header">

            <h2>
                Edit Soil Record
            </h2>

            <x-close-button wire:click="closeModal" />

        </div>


        {{-- BODY --}}
        <div class="soil-form-modal-body">


            {{-- 1. MUNSELL COLOR IMAGE UPLOAD BOX --}}
            <div>
                <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:2px;">
                    Munsell Color Image
                </div>
                <div style="font-size:12px; color:#9ca3af; margin-bottom:8px;">Upload a photo or take a picture of the Munsell color sample.</div>

                <div
                    x-data="{ isHovered: false }"
                    @mouseenter="isHovered = true"
                    @mouseleave="isHovered = false"
                    :style="isHovered || {{ ($image || $existingImage) ? 'true' : 'false' }}
                        ? 'border:2px dashed #22c55e; background:#f0fdf4; border-radius:14px; padding:16px 14px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; transition:border-color 0.25s ease, background 0.25s ease; box-shadow:0 4px 12px rgba(68,175,53,0.1);'
                        : 'border:2px dashed #e5e7eb; background:#fafafa; border-radius:14px; padding:16px 14px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; transition:border-color 0.25s ease, background 0.25s ease;'"
                >

                    @if($image)
                        <div style="position:relative; width:100%; display:flex; flex-direction:column; align-items:center; gap:8px;">
                            <img src="{{ $image->temporaryUrl() }}" style="width:110px; height:110px; object-fit:cover; border-radius:14px; border:2px solid #22c55e; box-shadow:0 4px 12px rgba(68,175,53,0.15);">
                            <div style="display:flex; gap:8px; margin-top:2px;">
                                <label
                                    style="display:flex; align-items:center; gap:6px; padding:6px 13px; border-radius:10px; background:#ffffff; border:1.5px solid #22c55e; font-size:12px; font-weight:600; color:#22c55e; cursor:pointer; transition:background 0.2s ease;"
                                    onmouseover="this.style.background='#f0fdf4';"
                                    onmouseout="this.style.background='#ffffff';"
                                >
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Change Photo
                                    <input @change="handleFileUpload($event)" wire:model="image" type="file" accept="image/*" style="display:none;">
                                </label>
                                <button
                                    type="button"
                                    wire:click="$set('image', null)"
                                    style="display:flex; align-items:center; gap:6px; padding:6px 13px; border-radius:10px; background:#fef2f2; border:1.5px solid #fca5a5; font-size:12px; font-weight:600; color:#dc2626; cursor:pointer; transition:background 0.2s ease;"
                                    onmouseover="this.style.background='#fee2e2';"
                                    onmouseout="this.style.background='#fef2f2';"
                                >
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                    @elseif($existingImage)
                        <div style="position:relative; width:100%; display:flex; flex-direction:column; align-items:center; gap:8px;">
                            <img src="{{ asset('storage/'.$existingImage) }}" style="width:110px; height:110px; object-fit:cover; border-radius:14px; border:2px solid #22c55e; box-shadow:0 4px 12px rgba(68,175,53,0.15);">
                            <div style="display:flex; gap:8px; margin-top:2px;">
                                <label
                                    style="display:flex; align-items:center; gap:6px; padding:6px 13px; border-radius:10px; background:#ffffff; border:1.5px solid #22c55e; font-size:12px; font-weight:600; color:#22c55e; cursor:pointer; transition:background 0.2s ease;"
                                    onmouseover="this.style.background='#f0fdf4';"
                                    onmouseout="this.style.background='#ffffff';"
                                >
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Change Photo
                                    <input @change="handleFileUpload($event)" wire:model="image" type="file" accept="image/*" style="display:none;">
                                </label>
                            </div>
                        </div>
                    @else
                        <div
                            :style="isHovered
                                ? 'width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:background 0.25s ease; background:#bbf7d0;'
                                : 'width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:background 0.25s ease; background:#dcfce7;'"
                        >
                            <svg width="20" height="20" fill="none" stroke="#22c55e" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <p :style="isHovered ? 'color:#22c55e; font-weight:600;' : 'color:#6b7280; font-weight:400;'" style="font-size:12.5px; margin:0; text-align:center; transition:color 0.2s ease;">
                            Drag & drop an image here, or use the buttons below
                        </p>

                        <div style="display:flex; gap:8px;">

                            <label
                                style="display:flex; align-items:center; gap:6px; padding:8px 14px; border:1.5px solid #e5e7eb; border-radius:10px; background:#fff; font-size:12.5px; font-weight:600; color:#374151; cursor:pointer; transition:all 0.2s ease; box-shadow:0 1px 2px rgba(0,0,0,0.04);"
                                onmouseover="this.style.borderColor='#22c55e'; this.style.color='#2d8a39'; this.style.background='#f0fdf4';"
                                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='#374151'; this.style.background='#ffffff';"
                            >
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload Image
                                <input @change="handleFileUpload($event)" wire:model="image" type="file" accept="image/*" style="display:none;">
                            </label>

                            <button
                                type="button"
                                @click="startWebcam()"
                                style="display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; background:#22c55e; border:none; font-size:12.5px; font-weight:600; color:#fff; cursor:pointer; transition:all 0.2s ease; box-shadow:0 2px 4px rgba(68,175,53,0.25);"
                                onmouseover="this.style.background='#3b992e'; this.style.boxShadow='0 4px 10px rgba(68,175,53,0.35)';"
                                onmouseout="this.style.background='#22c55e'; this.style.boxShadow='0 2px 4px rgba(68,175,53,0.25)';"
                            >
                                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Take Photo
                            </button>
                            <input x-ref="cameraFileInput" @change="handleFileUpload($event)" wire:model="image" type="file" accept="image/*" capture="environment" style="display:none;">

                        </div>
                    @endif

                </div>
                @error('image')
                    <div style="color:#ef4444; font-size:11px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>


            {{-- 2. COLOR PREVIEW CARD --}}
            @php
                $previewColor = '#22c55e';
                if ($hue && $value !== '' && $chroma !== '') {
                    try {
                        $previewColor = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($hue, $value, $chroma);
                    } catch (\Exception $e) {}
                } elseif ($hue) {
                    try {
                        $previewColor = \App\Livewire\Admin\MunsellColor\ManageMunsellColor::munsellToHex($hue, 5, 6);
                    } catch (\Exception $e) {}
                }
            @endphp

            <div style="display:flex; align-items:center; gap:12px; background:#f9fafb; border-radius:14px; padding:10px 14px;">
                <div style="position:relative; width:40px; height:40px; border-radius:10px; overflow:hidden; flex-shrink:0; border:1px solid rgba(0,0,0,0.12);">
                    @if($image)
                        <img src="{{ $image->temporaryUrl() }}" style="width:100%; height:100%; object-fit:cover;">
                    @elseif($existingImage)
                        <img src="{{ asset('storage/'.$existingImage) }}" style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <div style="width:100%; height:100%; background:{{ $previewColor }}; transition:background 0.3s ease;"></div>
                    @endif

                    <div wire:loading wire:target="image" style="position:absolute; inset:0; background:rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center;">
                        <svg class="animate-spin" width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="white" stroke-opacity="0.3" stroke-width="2"></circle>
                            <path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="2"></path>
                        </svg>
                    </div>
                </div>

                <div>
                    <div style="font-size:13px; font-weight:600; color:#374151;">Color Preview</div>
                    <div style="font-size:12px; color:#9ca3af; margin-top:1px;">
                        @if($image)
                            Uploaded Photo Preview
                        @elseif($full_code)
                            {{ $full_code }}
                        @else
                            Munsell Profile
                        @endif
                    </div>
                </div>
            </div>


            {{-- 3. COLOR NAME (SOIL NAME) --}}
            <div>
                <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                    Color Name
                </div>
                <input
                    wire:model="name"
                    type="text"
                    placeholder="e.g. Dark Olive Brown"
                    style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#111827; outline:none; font-family:inherit;"
                    onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                >
                @error('name')
                    <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                @enderror
            </div>


            {{-- 4. DESCRIPTION --}}
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                    <div style="font-size:12.5px; font-weight:600; color:#374151;">
                        Description
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


            {{-- 4.5 SCAN NOTE --}}
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                    <div style="font-size:12.5px; font-weight:600; color:#374151;">
                        Scan Note
                    </div>
                    <div style="font-size:11px; color:#9ca3af; font-weight:500;" x-data="{}" x-text="($wire.scan_note && $wire.scan_note.trim().length > 0 ? $wire.scan_note.trim().split(/\s+/).filter(Boolean).length : 0) + ' words'">
                    </div>
                </div>
                <textarea
                    wire:model="scan_note"
                    placeholder="Enter additional scan notes, field survey details, or scan observations..."
                    style="width:100%; box-sizing:border-box; height:80px; min-height:70px; max-height:200px; border:1.5px solid #e5e7eb; border-radius:12px; padding:10px 12px; font-size:13px; color:#111827; resize:vertical; outline:none; font-family:inherit; line-height:1.5; overflow-y:auto;"
                    onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                ></textarea>
                @error('scan_note')
                    <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                @enderror
            </div>


            {{-- 4.8 SOIL TYPE & SOIL ORGANIC INDEX (2-COLUMN GRID) --}}
            <div class="soil-form-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                        Soil Type / Drainage
                    </div>
                    <input
                        wire:model="soil_type"
                        type="text"
                        placeholder="e.g. Well-Drained Loam / Latosol"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#111827; outline:none; font-family:inherit;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                    @error('soil_type')
                        <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                        Soil Organic Index
                    </div>
                    <input
                        wire:model="organic_matter"
                        type="text"
                        placeholder="e.g. High (3.8% – 5.4%)"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#111827; outline:none; font-family:inherit;"
                        onfocus="this.style.borderColor='#86efac'; this.style.boxShadow='0 0 0 3px rgba(134,239,172,0.25)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                    >
                    @error('organic_matter')
                        <div style="color:#ef4444; font-size:11px; margin-top:3px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>


            {{-- 5. HUE, VALUE, CHROMA (3-COLUMN GRID) --}}
            <div class="soil-form-grid-3" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">

                {{-- HUE --}}
                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">
                        Hue
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
                        Value
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
                        Chroma
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


            {{-- 6. FULL MUNSELL CODE --}}
            <div>
                <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:2px;">
                    Full Munsell Code
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


            {{-- 7. STATUS + ADMIN ID + USER ID --}}
            <div class="soil-form-grid-3" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">

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
                        placeholder="ADM-001"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#6b7280; outline:none; background:#f9fafb; font-family:inherit; cursor:not-allowed;"
                    >
                </div>

                <div>
                    <div style="font-size:12.5px; font-weight:600; color:#374151; margin-bottom:3px;">User ID</div>
                    <input
                        wire:model="user_id"
                        type="text"
                        readonly
                        placeholder="226-0001"
                        style="width:100%; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:8px 12px; font-size:13px; color:#6b7280; outline:none; background:#f9fafb; font-family:inherit; cursor:not-allowed;"
                    >
                </div>

            </div>


        </div>


        {{-- FOOTER --}}
        <div class="soil-form-modal-footer" style="padding: 16px 24px 20px; background: #ffffff; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; flex-shrink: 0;">
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
                title="Save Changes"
            >
                <span>Save Changes</span>
            </button>
        </div>


    </div>


    {{-- WEBCAM LIVE CAMERA MODAL OVERLAY --}}
    <template x-if="showCameraModal">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center"
            style="background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);"
        >
            <div style="background:#1e293b; border-radius:20px; width:100%; max-width:540px; margin:16px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);">
                <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #334155;">
                    <span style="color:#fff; font-weight:600; font-size:15px; display:flex; align-items:center; gap:8px;">
                        <span style="width:8px; height:8px; border-radius:50%; background:#ef4444; display:inline-block;"></span>
                        Live Camera Feed
                    </span>
                    <x-close-button @click="stopWebcam()" class="text-slate-400 hover:text-white hover:bg-slate-700" />
                </div>
                <div style="position:relative; background:#000; display:flex; align-items:center; justify-content:center; min-height:300px;">
                    <video x-ref="webcamVideo" autoplay playsinline style="width:100%; max-height:400px; object-fit:contain;"></video>
                </div>
                <div style="display:flex; justify-content:center; align-items:center; gap:16px; padding:20px; background:#0f172a;">
                    <button
                        type="button"
                        @click="stopWebcam()"
                        style="padding:10px 20px; border-radius:12px; border:1px solid #475569; background:transparent; color:#94a3b8; font-weight:600; cursor:pointer; font-size:13.5px;"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="snapPhoto()"
                        style="display:flex; align-items:center; gap:8px; padding:10px 24px; border-radius:12px; background:#22c55e; border:none; color:#fff; font-weight:700; cursor:pointer; font-size:14px; box-shadow:0 4px 14px rgba(68,175,53,0.4);"
                    >
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Snap Photo
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>

@endif
</div>
