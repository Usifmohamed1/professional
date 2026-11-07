<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('career_applications')) {
            return;
        }

        Schema::create('career_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('career_id')->constrained('careers')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('phone', 60);
            $table->string('email', 120);
            $table->string('cv_path');
            $table->string('cv_original_name')->nullable();
            $table->string('cv_mime_type', 120)->nullable();
            $table->unsignedInteger('cv_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_applications');
    }
};
