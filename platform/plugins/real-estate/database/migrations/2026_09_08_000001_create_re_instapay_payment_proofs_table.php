<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('re_instapay_payment_proofs')) {
            return;
        }

        Schema::create('re_instapay_payment_proofs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('account_id')->index();
            $table->foreignId('package_id')->nullable()->index();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('phone', 60)->nullable();
            $table->string('receipt_path')->nullable();
            $table->json('receipt_data')->nullable();
            $table->string('status', 60)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('re_instapay_payment_proofs');
    }
};
