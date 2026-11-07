<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_projects', 'payment_type')) {
            Schema::table('re_projects', function (Blueprint $table): void {
                $table->string('payment_type', 20)->default('cash')->after('currency_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('re_projects', 'payment_type')) {
            Schema::table('re_projects', function (Blueprint $table): void {
                $table->dropColumn('payment_type');
            });
        }
    }
};
