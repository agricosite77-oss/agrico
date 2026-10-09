<div>
@if($showModal)
<div
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="background: rgba(0,0,0,0.35); backdrop-filter: blur(8px);"
    wire:click.self="closeModal"
>
    <div style="background:#fff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); width:100%; max-width:460px; overflow:hidden; animation: modalFadeIn 0.2s ease-out;">

        @if(! $isVerified)
        {{-- STEP 1: ADMIN PASSWORD SECURITY VERIFICATION --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:38px; height:38px; border-radius:12px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Admin Verification Required
                    </h3>
                    <p style="font-size:11.5px; color:#9ca3af; margin:0;">
                        Security authorization step
                    </p>
                </div>
            </div>
            <x-close-button wire:click="closeModal" />
        </div>

        <form wire:submit.prevent="verifyAdminPassword" autocomplete="off" style="padding:24px; display:flex; flex-direction:column; gap:16px;">
            <p style="font-size:13px; color:#4b5563; margin:0; line-height:1.5;">
                To protect user credentials, please enter your <strong style="color:#111827;">admin password</strong> to authorize editing account details for <strong style="color:#111827;">{{ $userName }}</strong>.
            </p>

            <div>
                <label style="font-size:12.5px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">Your Admin Password</label>
                <input
                    wire:model="admin_password"
                    type="password"
                    name="admin_verification_password"
                    autocomplete="new-password"
                    placeholder="Enter your password..."
                    style="width:100%; height:42px; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:0 14px; font-size:13.5px; color:#111827; outline:none;"
                    onfocus="this.style.borderColor='#f59e0b'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,0.2)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                >
                @if($admin_password_error)
                    <span style="color:#ef4444; font-size:11.5px; margin-top:4px; display:block; font-weight:600;">{{ $admin_password_error }}</span>
                @endif
                @error('admin_password')
                    <span style="color:#ef4444; font-size:11.5px; margin-top:4px; display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; margin-top: 12px;">
                <button
                    type="button"
                    wire:click="closeModal"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer text-center min-h-[44px]"
                    title="Cancel"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="flex-1 py-3 px-3 sm:px-4 rounded-full font-bold text-sm shadow-md transition cursor-pointer flex items-center justify-center text-center whitespace-nowrap min-h-[44px]"
                    style="background: #f59e0b !important; color: #ffffff !important; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35); border: none;"
                    onmouseover="this.style.background='#d97706'"
                    onmouseout="this.style.background='#f59e0b'"
                    title="Authorize & Continue"
                >
                    <span>Authorize & Continue</span>
                </button>
            </div>
        </form>

        @else

        {{-- STEP 2: EDIT USER CREDENTIALS (AFTER ADMIN AUTHORIZATION) --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:38px; height:38px; border-radius:12px; background:#dcfce7; color:#22c55e; display:flex; align-items:center; justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0;">
                        Edit User Credentials
                    </h3>
                    <span style="font-size:11px; font-weight:700; color:#22c55e; background:#dcfce7; padding:2px 8px; border-radius:9999px; display:inline-block; margin-top:2px;">
                        ✓ Admin Authorized
                    </span>
                </div>
            </div>
            <x-close-button wire:click="closeModal" />
        </div>

        <form wire:submit.prevent="save" autocomplete="off" style="padding:24px; display:flex; flex-direction:column; gap:16px;">
            {{-- EMAIL --}}
            <div>
                <label style="font-size:12.5px; font-weight:600; color:#374151; display:block; margin-bottom:4px;">User Email Address</label>
                <input
                    wire:model="email"
                    type="email"
                    name="target_user_email"
                    autocomplete="off"
                    placeholder="user@example.com"
                    style="width:100%; height:40px; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:0 12px; font-size:13px; color:#111827; outline:none;"
                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                >
                @error('email') <span style="color:#ef4444; font-size:11px; margin-top:2px; display:block;">{{ $message }}</span> @enderror
            </div>

            {{-- NEW PASSWORD --}}
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <label style="font-size:12.5px; font-weight:600; color:#374151;">New Password</label>
                    <span style="font-size:11px; color:#9ca3af;">Leave empty if unchanged</span>
                </div>
                <input
                    wire:model="new_password"
                    type="password"
                    name="target_new_password"
                    autocomplete="new-password"
                    placeholder="Enter new password (min. 6 characters)"
                    style="width:100%; height:40px; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:0 12px; font-size:13px; color:#111827; outline:none;"
                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                >
                @error('new_password') <span style="color:#ef4444; font-size:11px; margin-top:2px; display:block;">{{ $message }}</span> @enderror
            </div>

            {{-- CONFIRM PASSWORD --}}
            @if(!empty($new_password))
            <div>
                <label style="font-size:12.5px; font-weight:600; color:#374151; display:block; margin-bottom:4px;">Confirm New Password</label>
                <input
                    wire:model="new_password_confirmation"
                    type="password"
                    name="target_new_password_confirmation"
                    autocomplete="new-password"
                    placeholder="Re-enter new password..."
                    style="width:100%; height:40px; box-sizing:border-box; border:1.5px solid #e5e7eb; border-radius:12px; padding:0 12px; font-size:13px; color:#111827; outline:none;"
                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.2)'"
                    onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                >
            </div>
            @endif

            <div style="display: flex; gap: 12px; align-items: center; width: 100%; box-sizing: border-box; margin-top: 12px;">
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
                    class="flex-1 py-3 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/35 transition cursor-pointer flex items-center justify-center text-center"
                    style="box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);"
                    title="Update Credentials"
                >
                    <span>Update Credentials</span>
                </button>
            </div>
        </form>
        @endif

    </div>
</div>
@endif
</div>
