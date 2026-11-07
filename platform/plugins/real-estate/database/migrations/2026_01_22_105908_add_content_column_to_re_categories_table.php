<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_categories', function (Blueprint $table): void {
            $table->longText('content')->nullable()->after('description');
        });

        Schema::table('re_categories_translations', function (Blueprint $table): void {
            $table->longText('content')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('re_categories', function (Blueprint $table): void {
            $table->dropColumn('content');
        });

        Schema::table('re_categories_translations', function (Blueprint $table): void {
            $table->dropColumn('content');
        });
    }
};
