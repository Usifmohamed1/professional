<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_property_agents')) {
            Schema::create('re_property_agents', function (Blueprint $table): void {
                $table->foreignId('property_id')->constrained('re_properties')->cascadeOnDelete();
                $table->foreignId('account_id')->constrained('re_accounts')->cascadeOnDelete();
                $table->primary(['property_id', 'account_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('re_property_agents');
    }
};