<div>
@if($showModal && $mapping)
<div
    class="fixed inset-0 z-[9999] flex items-center justify-center"
    style="z-index: 9999; background: rgba(0,0,0,0.30); backdrop-filter: blur(8px);"
    wire:click.self="closeModal"
>
    <div style="background:#fff; border-radius:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); width:100%; max-width:440px; margin:0 16px; padding:24px; text-align:center; position:relative; animation: modalScaleIn 0.2s ease-out;">
        <div style="position:absolute; top:16px; right:16px;">
            <x-close-button wire:click="closeModal" />
        </div>

        {{-- WARNING ICON --}}
        <div class="w-11 h-11 rounded-full bg-red-50 flex items-center justify-center shrink-0 mx-auto mb-4">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>

        <h3 style="font-size:18px; font-weight:800; color:#111827; margin:0 0 6px 0;">
            Delete Mapping Record?
        </h3>

        <p style="font-size:13px; color:#6b7280; margin:0 0 20px 0; line-height:1.5;">
            Are you sure you want to delete this mapping history record for <strong style="color:#111827;">{{ $mapping->soil->soil_name ?? 'this soil' }}</strong>? This action cannot be undone.
        </p>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-3 mt-5">
            <button
                wire:click="closeModal"
                class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition cursor-pointer"
            >
                Cancel
            </button>
            <button
                wire:click="confirmDelete"
                class="flex-1 py-3 rounded-full bg-red-500 hover:bg-red-600 text-white font-bold text-sm shadow-md shadow-red-500/35 transition cursor-pointer"
                style="box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);"
            >
                Delete Record
            </button>
        </div>

    </div>
</div>
@endif
</div>
