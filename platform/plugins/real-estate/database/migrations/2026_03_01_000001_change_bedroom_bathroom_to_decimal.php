<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('re_properties')
            ->whereNotNull('number_bedroom')
            ->where('number_bedroom', '<', 0)
            ->update(['number_bedroom' => 0]);

        DB::table('re_properties')
            ->whereNotNull('number_bathroom')
            ->where('number_bathroom', '<', 0)
            ->update(['number_bathroom' => 0]);

        Schema::table('re_properties', function (Blueprint $table) {
            $table->decimal('number_bedroom', 8, 1)->nullable()->default(0)->change();
            $table->decimal('number_bathroom', 8, 1)->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('re_properties', function (Blueprint $table) {
            $table->integer('number_bedroom')->nullable()->change();
            $table->integer('number_bathroom')->nullable()->change();
        });
    }
};
