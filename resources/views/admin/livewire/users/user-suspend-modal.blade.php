<div>
@if($showModal && $user)
<div
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto"
    style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; min-width: 100%; height: 100vh; height: 100dvh; min-height: 100%; margin: 0; box-sizing: border-box; background: rgba(0,0,0,0.30); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 99999; padding: 16px 12px;"
    wire:click.self="closeModal"
>
    <div style="background:#fff; border-radius:20px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); width:100%; max-width:440px; margin:auto; max-height:calc(100dvh - 24px); overflow-y:auto; -webkit-overflow-scrolling:touch; padding:24px; position:relative; animation: modalFadeIn 0.2s ease-out;">

        {{-- CLOSE BUTTON (TOP RIGHT) --}}
        <x-close-button wire:click="closeModal" class="absolute top-4 right-4" />

        {{-- WARNING ICON & TITLE HEADER --}}
        <div style="display:flex; align-items:flex-start; gap:16px; margin-bottom:16px;">
            <div style="width:42px; height:42px; border-radius:50%; background:#fef3c7; color:#f59e0b; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <div>
                <h3 style="font-size:16px; font-weight:800; color:#111827; margin:0 0 4px 0;">
                    Suspend User Account
                </h3>
                <p style="font-size:12.5px; color:#6b7280; margin:0; line-height:1.5;">
                    Suspending <strong style="color:#111827;">{{ $user->name }}</strong> will prevent them from logging in and submitting scans.
                </p>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; width: 100%; box-sizing: border-box; margin-top: 24px;">
            <button
                type="button"
                wire:click="closeModal"
                class="agri-btn agri-btn--secondary flex-1 min-w-[110px]"
                style="padding: 12px 16px; border-radius: 9999px; font-size: 14px; font-weight: 700; min-height: 44px;"
                title="Cancel"
            >
                Cancel
            </button>
            <button
                type="button"
                wire:click="confirmSuspend"
                class="agri-btn {{ $user->status === 'Suspended' ? 'agri-btn--primary' : 'agri-btn--danger' }} flex-1 min-w-[130px]"
                style="padding: 12px 16px; border-radius: 9999px; font-size: 14px; font-weight: 700; min-height: 44px;"
                title="{{ $user->status === 'Suspended' ? 'Unsuspend User' : 'Confirm Suspension' }}"
            >
                {{ $user->status === 'Suspended' ? 'Unsuspend User' : 'Confirm Suspension' }}
            </button>
        </div>

    </div>
</div>
@endif
</div>
