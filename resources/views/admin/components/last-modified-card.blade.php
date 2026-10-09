@props([
    'record' => null,
    'adminId' => null,
    'changedDate' => null,
    'label' => 'Admin ID',
    'showDivider' => true,
])

@php
    if ($record) {
        $rawAdminId = $record->last_admin_id ?? null;
        
        $hasAdmin = !empty($rawAdminId) && $rawAdminId !== '—';
        
        // Check if record has a last_admin_id AND updated_at
        $isModified = $hasAdmin && !empty($record->updated_at);

        $adminIdDisplay = $isModified ? \App\Models\Admin::formatId($rawAdminId) : '—';
        $changedDateDisplay = $isModified ? $record->updated_at->timezone('Asia/Manila')->format('m/d/Y h:i A') : '—';
    } else {
        $hasValidAdmin = !empty($adminId) && $adminId !== '—';
        $hasValidDate  = !empty($changedDate) && $changedDate !== '—' && $changedDate !== 'N/A';

        $isModified = $hasValidAdmin && $hasValidDate;

        $adminIdDisplay = $isModified ? $adminId : '—';
        $changedDateDisplay = $isModified ? $changedDate : '—';
    }
@endphp

<!-- LAST MODIFIED Component Card -->
<div class="mt-4 pt-3" style="{{ $showDivider ? 'border-top: 1px solid #f1f5f9; margin-top: 16px; padding-top: 14px;' : 'margin-top: 14px;' }}">
    <!-- Section Header Title -->
    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
        LAST MODIFIED
    </h4>
    
    <!-- Audit Information Box -->
    <div class="bg-slate-50/80 border border-slate-100 p-4 rounded-2xl flex justify-between items-center" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
        <!-- Left Column: Admin ID -->
        <div>
            <span class="text-xs text-gray-400 font-medium block mb-0.5" style="font-size: 11px; font-weight: 500; color: #9ca3af; display: block; margin-bottom: 3px;">
                {{ $label }}
            </span>
            <span class="text-sm font-bold text-slate-800 font-mono" style="font-size: 13.5px; font-weight: 800; color: #1e293b; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">
                {{ $adminIdDisplay }}
            </span>
        </div>

        <!-- Right Column: Changed Date -->
        <div class="text-right" style="text-align: right;">
            <span class="text-xs text-gray-400 font-medium block mb-0.5" style="font-size: 11px; font-weight: 500; color: #9ca3af; display: block; margin-bottom: 3px;">
                Changed Date
            </span>
            <span class="text-sm font-bold text-slate-800" style="font-size: 13.5px; font-weight: 800; color: #1e293b;">
                {{ $changedDateDisplay }}
            </span>
        </div>
    </div>
</div>
