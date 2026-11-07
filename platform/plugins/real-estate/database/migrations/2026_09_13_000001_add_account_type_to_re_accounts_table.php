<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_accounts', 'account_type')) {
            Schema::table('re_accounts', function (Blueprint $table): void {
                $table->string('account_type', 20)->default('user')->after('username')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('re_accounts', 'account_type')) {
            Schema::table('re_accounts', function (Blueprint $table): void {
                $table->dropColumn('account_type');
            });
        }
    }
};
