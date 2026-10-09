<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Normalize existing status data
        DB::table('location_mappings')->whereRaw('LOWER(TRIM(status)) = ?', ['inactive'])->update(['status' => 'Inactive']);
        DB::table('location_mappings')->where(function ($q) {
            $q->whereNull('status')->orWhere('status', '')->orWhereRaw('LOWER(TRIM(status)) = ?', ['active']);
        })->update(['status' => 'Active']);

        // 2. Add index on status
        Schema::table('location_mappings', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('location_mappings', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
