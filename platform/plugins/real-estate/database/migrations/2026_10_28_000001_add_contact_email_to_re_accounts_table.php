<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->string('contact_email', 60)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->dropColumn('contact_email');
        });
    }
};
