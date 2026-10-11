<div>
@if($showModal)
<div
    x-data
    x-init="document.body.classList.add('overflow-hidden')"
    x-on:unmount="document.body.classList.remove('overflow-hidden')"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 99999; padding: 32px 16px;"
    wire:click.self="closeModal"
>
    @if(!$isVerified)
        {{-- STEP 1: ADMIN VERIFICATION REQUIRED MODAL --}}
        <div style="background:#ffffff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); width:100%; max-width:440px; margin:auto; max-height:calc(100vh - 64px); overflow-y:auto; position:relative;" class="no-scrollbar animate-in fade-in zoom-in-95 duration-200">
            
            {{-- HEADER WITH LOCK BADGE AND CLOSE BUTTON --}}
            <div style="padding:28px 28px 12px 28px; display:flex; align-items:flex-start; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; border-radius:14px; background:#fef3c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16.5px; font-weight:800; color:#0f172a; margin:0; line-height:1.2;">
                            Admin Verification Required
                        </h3>
                        <p style="font-size:12px; color:#94a3b8; font-weight:500; margin:3px 0 0 0;">
                            Security authorization step
                        </p>
                    </div>
                </div>

                <x-close-button wire:click="closeModal" />
            </div>

            {{-- BODY --}}
            <div style="padding:12px 28px 20px 28px; display:flex; flex-direction:column; gap:16px;">
                <p style="font-size:13px; color:#64748b; font-weight:400; line-height:1.5; margin:0;">
                    To protect user credentials, please enter your <strong style="color:#1e293b; font-weight:700;">admin password</strong> to authorize editing account details for <strong style="color:#0f172a; font-weight:700;">{{ $userRoleName }}</strong>.
                </p>

                <div>
                    <label style="font-size:12.5px; font-weight:700; color:#334155; display:block; margin-bottom:6px;">
                        Your Admin Password
                    </label>
                    <input
                        wire:model="adminPassword"
                        type="password"
                        placeholder="Enter your password..."
                        style="width:100%; height:44px; box-sizing:border-box; border:1px solid #cbd5e1; border-radius:12px; padding:0 14px; font-size:13.5px; color:#0f172a; outline:none; transition:all 0.2s ease;"
                        onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.15)';"
                        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                        wire:keydown.enter="verifyAdminPassword"
                    />
                    @if($adminPasswordError)
                        <span style="color:#ef4444; font-size:12px; font-weight:600; margin-top:6px; display:block;">
                            {{ $adminPasswordError }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- FOOTER BUTTONS --}}
            <div style="padding: 16px 28px 24px 28px; display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; background: #ffffff; border-top: 1px solid #f1f5f9;">
                <button
                    type="button"
                    wire:click="closeModal"
                    class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                    title="Cancel"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="verifyAdminPassword"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer flex items-center justify-center text-center whitespace-nowrap min-h-[44px]"
                    style="background: #f59e0b !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35); border: none;"
                    onmouseover="this.style.background='#d97706'"
                    onmouseout="this.style.background='#f59e0b'"
                    title="Authorize & Continue"
                >
                    <span>Authorize & Continue</span>
                </button>
            </div>
        </div>

    @else
        {{-- STEP 2: EDIT USER DETAILS FORM MODAL --}}
        <div style="background:#ffffff; border-radius:28px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.3); border:1px solid #f1f5f9; width:100%; max-width:680px; height:75vh; max-height:75vh; margin:auto; display:flex; flex-direction:column; overflow:hidden; position:relative; padding:24px; box-sizing:border-box;" class="no-scrollbar animate-in fade-in zoom-in-95 duration-200" x-data="{ showPass: false, showConfirmPass: false }">

            <x-close-button wire:click="closeModal" style="position: absolute; top: 18px; right: 18px; z-index: 50;" />

            <div style="margin-bottom: 18px; padding-right: 36px;">
                <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0;">
                    Edit User Details
                </h3>
                <p style="font-size: 12px; font-weight: 500; color: #64748b; margin: 2px 0 0 0;">
                    Update account credentials & profile picture
                </p>
            </div>

            <form wire:submit.prevent="save" style="overflow-y: auto; flex: 1 1 auto; min-height: 0; display: flex; flex-direction: column; padding-right: 4px; scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
                
                {{-- PROFILE PHOTO UPLOAD PICKER --}}
                <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 18px;">
                    <label style="position: relative; cursor: pointer;" title="Click to upload new photo">
                        <div style="width: 86px; height: 86px; border-radius: 50%; overflow: hidden; background: #22c55e !important; color: #ffffff !important; box-shadow: 0 4px 16px rgba(34,197,94,0.35); border: none; display: flex; align-items: center; justify-content: center;">
                            @if($photo)
                                <img src="{{ $photo->temporaryUrl() }}" style="width: 100%; height: 100%; object-fit: cover;" />
                            @elseif($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" style="width: 100%; height: 100%; object-fit: cover;" />
                            @else
                                <span style="font-size: 36px; font-weight: 800; color: #ffffff !important; user-select: none;">{{ $userInitial }}</span>
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

                {{-- CONDITIONAL STUDENT ID & SECTION FIELDS FOR STUDENT ROLE --}}
                @if(strtolower($role) === 'student')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                            Student ID <span style="font-size: 10px; font-weight: 500; color: #64748b;">(Auto-assigned if blank)</span>
                        </label>
                        <input
                            type="text"
                            wire:model="student_id"
                            placeholder="e.g. 2026-00001"
                            style="width: 100%; padding: 9px 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 12.5px; font-weight: 500; color: #1e293b; outline: none; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); box-sizing: border-box;"
                            onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                            onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                        />
                        @error('student_id')
                            <span style="font-size: 11px; font-weight: 600; color: #ef4444; margin-top: 2px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                            Section
                        </label>
                        <input
                            type="text"
                            wire:model="section"
                            placeholder="e.g. BSCS 4-A"
                            style="width: 100%; padding: 9px 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 12.5px; font-weight: 500; color: #1e293b; outline: none; transition: all 0.2s cubic-bezier(0.4,0,0.2,1); box-sizing: border-box;"
                            onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                            onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                        />
                        @error('section')
                            <span style="font-size: 11px; font-weight: 600; color: #ef4444; margin-top: 2px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @endif

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
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                    
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

                {{-- SIDE-BY-SIDE ROLE & STATUS GRID --}}
                @php
                    $isSuperAdminActor = $this->isSuperAdminActor();
                    $isMainSuperAdminAccount = (strtolower(trim($email ?? '')) === 'admin@agrico.com');
                    $isRoleDisabled = $isMainSuperAdminAccount;
                @endphp
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 18px;">
                    
                    {{-- ROLE --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                            Role
                        </label>
                        <div style="position: relative;">
                            <select
                                wire:model.live="role"
                                @if($isRoleDisabled) disabled @endif
                                style="width: 100%; padding: 9px 32px 9px 14px; background: {{ $isRoleDisabled ? '#f8fafc' : '#ffffff' }}; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 12px; font-weight: 600; color: #334155; appearance: none; outline: none; box-sizing: border-box; cursor: {{ $isRoleDisabled ? 'not-allowed' : 'pointer' }};"
                                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                            >
                                @if($isSuperAdminActor)
                                    <option value="superadmin">Super Admin</option>
                                    <option value="admin">Admin</option>
                                @elseif(in_array(strtolower($role), ['admin', 'superadmin']))
                                    <option value="{{ $role }}">{{ in_array(strtolower($role), ['superadmin', 'super admin']) ? 'Super Admin' : 'Admin' }}</option>
                                @endif
                                <option value="user">User</option>
                                <option value="student">Student</option>
                            </select>
                            <svg width="14" height="14" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        @if($isMainSuperAdminAccount)
                            <span style="font-size:10px; font-weight:600; color:#64748b; margin-top:2px; display:block;">🔒 Main Super Admin role locked</span>
                        @endif
                    </div>

                    {{-- STATUS --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 4px;">
                            Status
                        </label>
                        <div style="position: relative;">
                            <select
                                wire:model="status"
                                style="width: 100%; padding: 9px 32px 9px 14px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9999px; font-size: 12px; font-weight: 600; color: #166534; appearance: none; outline: none; box-sizing: border-box;"
                                onfocus="this.style.borderColor='#22c55e'; this.style.boxShadow='0 0 0 3.5px rgba(68,175,53,0.2)';"
                                onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';"
                            >
                                <option value="Active">Active</option>
                                <option value="Suspended">Suspended</option>
                            </select>
                            <svg width="14" height="14" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                </div>

                {{-- ACTION BUTTONS --}}
                <div style="display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; margin-top: 20px;">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center"
                        title="Cancel"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-3 rounded-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm shadow-md shadow-green-500/35 transition cursor-pointer flex items-center justify-center text-center"
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
@endif
</div>
