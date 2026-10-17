<div>
    @if($showModal)
        <div
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
            style="background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 16px 12px;"
            @keydown.escape.window="$wire.closeModal()"
        >
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-350"
                x-transition:enter-start="opacity-0 scale-90 translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="admin-profile-modal-card agrico-standard-popup"
                style="background: #ffffff; border-radius: 28px; width: 100%; max-width: 390px; margin: auto; max-height: min(85vh, calc(100dvh - 24px)); overflow-y: auto; -webkit-overflow-scrolling: touch; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.25); padding: 18px; box-sizing: border-box; position: relative;"
            >

                {{-- FLASH MESSAGE NOTIFICATION --}}
                @if(session()->has('message'))
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #22c55e; padding: 10px 14px; border-radius: 14px; font-size: 12.5px; font-weight: 600; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
                        <span>{{ session('message') }}</span>
                        <button type="button" @click="$el.parentElement.style.display='none'" style="background: transparent; border: none; cursor: pointer; color: #22c55e; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(34,197,94,0.15)'" onmouseout="this.style.background='transparent'" title="Dismiss" aria-label="Dismiss">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- EXCLUSIVE TOP-RIGHT CLOSE X BUTTON WITH FROSTED GLASS --}}
                <div style="position: absolute; top: 26px; right: 26px; z-index: 40;">
                    <x-close-button wire:click="closeModal" />
                </div>

                @if(!$isEditing)
                    {{-- ========================================================================= --}}
                    {{-- VIEW MODE: EXACT SIENNA BROOKS DESIGN WITH FROSTED GLASS BLUR HEADER --}}
                    {{-- ========================================================================= --}}
                    <div>

                        {{-- MAIN PORTRAIT IMAGE CARD CONTAINER (TEXT & PHOTO BOTH INSIDE) --}}
                        <div style="width: 100%; height: 380px; max-height: min(380px, 45vh); border-radius: 24px; overflow: hidden; position: relative; display: flex; flex-direction: column; align-items: center; justify-content: space-between; box-sizing: border-box; margin-bottom: 16px; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.06); transform: translateZ(0); -webkit-mask-image: -webkit-radial-gradient(white, black);">
                            
                            {{-- BACKGROUND IMAGE OR CLEAN PAST LIGHT GREEN --}}
                            @if($profilePhotoUrl)
                                <img
                                    src="{{ $profilePhotoUrl }}"
                                    alt="{{ $adminName }}"
                                    style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; z-index: 1;"
                                />
                            @else
                                <div style="position: absolute; inset: 0; background: #22c55e; z-index: 1;"></div>
                            @endif

                            {{-- FROSTED GLASS BACKDROP BLUR HEADER WITH MATCHING 24px TOP ROUNDED CORNERS --}}
                            <div style="position: relative; z-index: 10; text-align: center; width: 100%; padding: 22px 16px 20px 16px; background: linear-gradient(180deg, rgba(15,23,42,0.55) 0%, rgba(15,23,42,0.35) 60%, rgba(15,23,42,0) 100%); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-radius: 24px 24px 0 0; overflow: hidden; box-sizing: border-box;">
                                <h2 style="font-size: 25px; font-weight: 800; color: #ffffff; margin: 0; line-height: 1.2; letter-spacing: -0.5px; text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                                    {{ $adminName }}
                                </h2>
                                <div style="font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.95); margin-top: 5px; display: flex; align-items: center; justify-content: center; gap: 6px; text-shadow: 0 1px 6px rgba(0,0,0,0.4);">
                                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #4ade80; box-shadow: 0 0 8px #4ade80;"></span>
                                    <span>{{ $adminRole }}</span>
                                </div>
                            </div>

                            {{-- MONOGRAM INITIAL IF NO PHOTO (DEAD CENTER IN CARD) --}}
                            @if(!$profilePhotoUrl)
                                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 5; padding-top: 50px; pointer-events: none;">
                                    <span style="font-size: 110px; font-weight: 800; color: #ffffff; text-shadow: 0 6px 24px rgba(0,0,0,0.3); font-family: sans-serif; line-height: 1;">
                                        {{ $adminInitial }}
                                    </span>
                                </div>
                            @endif

                        </div>

                        {{-- BOTTOM FOOTER ROW (OUTSIDE MAIN CARD BOX AT BOTTOM) --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 0 4px;">
                            {{-- LEFT: SMALL AVATAR + USERNAME & JOINED DATE --}}
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="position: relative;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; background: #22c55e; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">
                                        @if($profilePhotoUrl)
                                            <img src="{{ $profilePhotoUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                        @else
                                            {{ $adminInitial }}
                                        @endif
                                    </div>
                                    <span style="position: absolute; bottom: 0; right: 0; width: 9px; height: 9px; border-radius: 50%; background: #22c55e; border: 2px solid #ffffff;"></span>
                                </div>
                                <div>
                                    <!-- Line 1: Email -->
                                    <div style="font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.2; letter-spacing: -0.2px;">
                                        {{ $adminEmail }}
                                    </div>
                                    <!-- Line 2: Joined Date -->
                                    <div style="font-size: 11.5px; font-weight: 500; color: #94a3b8; line-height: 1.2; margin-top: 2px;">
                                        Joined {{ $joinedDate }}
                                    </div>
                                    <!-- Line 3: User ID -->
                                    <div style="font-size: 11.5px; font-weight: 500; color: #94a3b8; line-height: 1.2; margin-top: 2px;">
                                        {{ auth()->user()?->admin_id ?? auth()->user()?->user_id ?? auth()->user()?->id ?? $adminId }}
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: BUTTONS (EDIT PROFILE) --}}
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <button
                                    type="button"
                                    wire:click="toggleEdit"
                                    class="agri-btn agri-btn--primary agri-btn--sm"
                                    style="font-size: 13px; font-weight: 700; border-radius: 9999px; padding: 9px 16px;"
                                >
                                    <span>Edit Profile</span>
                                </button>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- ========================================================================= --}}
                    {{-- EDIT MODE: EDIT ADMIN DETAILS FORM --}}
                    {{-- ========================================================================= --}}
                    <div>
                        <div style="margin-bottom: 18px; padding-right: 36px;">
                            <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;">
                                Edit Admin Details
                            </h3>
                            <p style="font-size: 12px; font-weight: 500; color: #64748b; margin: 2px 0 0 0;">
                                Update account credentials & profile picture
                            </p>
                        </div>

                        <form wire:submit.prevent="save" x-data="{ showPass: false, showConfirmPass: false }">
                            
                            {{-- PROFILE PHOTO UPLOAD PICKER --}}
                            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 18px;">
                                <label style="position: relative; cursor: pointer;" title="Click to upload new photo">
                                    <div style="width: 86px; height: 86px; border-radius: 50%; overflow: hidden; background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 16px rgba(34,197,94,0.35); border: none; display: flex; align-items: center; justify-content: center;">
                                        @if($photo)
                                            <img src="{{ $photo->temporaryUrl() }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                        @elseif($profilePhotoUrl)
                                            <img src="{{ $profilePhotoUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                                        @else
                                            <span style="font-size: 36px; font-weight: 800; color: #ffffff !important; user-select: none;">{{ $adminInitial }}</span>
                                        @endif
                                    </div>
                                    <div style="position: absolute; bottom: 0; right: 0; background: #0f172a; color: #ffffff; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <circle cx="12" cy="13" r="3"/>
                                        </svg>
                                    </div>
                                    <input type="file" wire:model="photo" accept="image/*" style="display: none;" />
                                </label>
                                <span style="font-size: 11px; font-weight: 600; color: #64748b; margin-top: 5px;">
                                    Click avatar to change picture
                                </span>

                                @if(($profilePhotoUrl || $photo) && !$removePhoto)
                                    <button
                                        type="button"
                                        wire:click="deletePhoto"
                                        style="margin-top: 4px; background: none; border: none; font-size: 11px; font-weight: 700; color: #ef4444; cursor: pointer; text-decoration: underline; padding: 2px 6px;"
                                    >
                                        Reset to default photo
                                    </button>
                                @elseif($removePhoto)
                                    <span style="margin-top: 4px; font-size: 11px; font-weight: 700; color: #f59e0b;">
                                        Default photo selected (click Save to confirm)
                                    </span>
                                @endif

                                @error('photo')
                                    <span style="font-size: 11px; font-weight: 600; color: #ef4444; margin-top: 2px;">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- FULL NAME --}}
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    wire:model="name"
                                    style="width: 100%; padding: 9px 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 12.5px; font-weight: 500; color: #1e293b; outline: none; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); box-sizing: border-box;"
                                    onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                                    onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                                    required
                                />
                                @error('name')
                                    <span style="font-size: 11px; font-weight: 600; color: #ef4444; margin-top: 2px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- EMAIL ADDRESS --}}
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    wire:model="email"
                                    style="width: 100%; padding: 9px 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 12.5px; font-weight: 500; color: #1e293b; outline: none; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); box-sizing: border-box;"
                                    onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                                    onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                                    required
                                />
                                @error('email')
                                    <span style="font-size: 11px; font-weight: 600; color: #ef4444; margin-top: 2px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- SIDE-BY-SIDE 2-COLUMN PASSWORDS --}}
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px;">
                                
                                {{-- LEFT: NEW PASSWORD --}}
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 4px; white-space: nowrap;">
                                        New Password (Optional)
                                    </label>
                                    <div style="position: relative;">
                                        <input
                                            :type="showPass ? 'text' : 'password'"
                                            wire:model="password"
                                            placeholder="Leave blank to keep"
                                            autocomplete="new-password"
                                            style="width: 100%; padding: 8px 34px 8px 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 11.5px; font-weight: 400; color: #1e293b; outline: none; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); box-sizing: border-box;"
                                            onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                                            onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                                        />
                                        <button
                                            type="button"
                                            @click="showPass = !showPass"
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 2px; display: flex; align-items: center; justify-content: center;"
                                            title="Toggle password visibility"
                                        >
                                            <template x-if="!showPass">
                                                <svg width="15" height="15" fill="none" stroke="#94a3b8" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"/>
                                                </svg>
                                            </template>
                                            <template x-if="showPass">
                                                <svg width="15" height="15" fill="none" stroke="#22c55e" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span style="font-size: 10.5px; font-weight: 600; color: #ef4444; margin-top: 2px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- RIGHT: CONFIRM PASSWORD --}}
                                <div>
                                    <label style="display: block; font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 4px; white-space: nowrap;">
                                        Confirm Password
                                    </label>
                                    <div style="position: relative;">
                                        <input
                                            :type="showConfirmPass ? 'text' : 'password'"
                                            wire:model="password_confirmation"
                                            placeholder="Confirm password"
                                            autocomplete="new-password"
                                            style="width: 100%; padding: 8px 34px 8px 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 11.5px; font-weight: 400; color: #1e293b; outline: none; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); box-sizing: border-box;"
                                            onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                                            onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                                        />
                                        <button
                                            type="button"
                                            @click="showConfirmPass = !showConfirmPass"
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 2px; display: flex; align-items: center; justify-content: center;"
                                            title="Toggle password visibility"
                                        >
                                            <template x-if="!showConfirmPass">
                                                <svg width="15" height="15" fill="none" stroke="#94a3b8" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"/>
                                                </svg>
                                            </template>
                                            <template x-if="showConfirmPass">
                                                <svg width="15" height="15" fill="none" stroke="#22c55e" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <span style="font-size: 10.5px; font-weight: 600; color: #ef4444; margin-top: 2px; display: block;">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            {{-- ACTION BUTTONS --}}
                            <div style="display: flex; align-items: center; gap: 12px; margin-top: 20px;">
                                <button
                                    type="button"
                                    wire:click="toggleEdit"
                                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                                    title="Cancel"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="flex-1 py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer text-center flex items-center justify-center"
                                    style="box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);"
                                    title="Save Changes"
                                >
                                    <span wire:loading.remove wire:target="save">Save Changes</span>
                                    <span wire:loading wire:target="save">Saving...</span>
                                </button>
                            </div>

                        </form>
                    </div>
                @endif

            </div>
        </div>
    @endif
</div>
