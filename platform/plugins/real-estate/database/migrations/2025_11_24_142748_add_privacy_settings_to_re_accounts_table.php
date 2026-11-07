<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('re_accounts', 'hide_phone')) {
            return;
        }

        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->boolean('hide_phone')->default(false)->after('is_public_profile');
            $table->boolean('hide_email')->default(false)->after('hide_phone');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('re_accounts', 'hide_phone')) {
            return;
        }

        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->dropColumn(['hide_phone', 'hide_email']);
        });
    }
};
