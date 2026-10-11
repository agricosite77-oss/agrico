<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the is_maintenance column to the applications table on Railway production.
     */
    public function up(): void
    {
        $isRailway = !empty(env('RAILWAY_ENVIRONMENT'))
            || !empty(env('RAILWAY_SERVICE_NAME'))
            || !empty(env('RAILWAY_PUBLIC_DOMAIN'))
            || !empty(env('RAILWAY_PROJECT_ID'))
            || !empty(env('RAILWAY_ENVIRONMENT_NAME'));

        $isProduction = app()->isProduction()
            || config('app.env') === 'production'
            || str_contains((string) config('app.url'), 'railway.app');

        // Strictly respect user instruction: "don't put that in the local only put that in my https://agrico-production-1eb3.up.railway.app/"
        if (!$isRailway && !$isProduction && app()->isLocal() && !env('FORCE_MUNSELL_PROD')) {
            return;
        }

        if (Schema::hasTable('applications') && !Schema::hasColumn('applications', 'is_maintenance')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->boolean('is_maintenance')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applications') && Schema::hasColumn('applications', 'is_maintenance')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('is_maintenance');
            });
        }
    }
};
