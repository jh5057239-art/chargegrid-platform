<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('charging_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('charging_type', 40)->default('AC');
            $table->unsignedInteger('power_kw')->default(7);
            $table->unsignedBigInteger('price_mxn_cents')->default(0);
            $table->string('status', 30)->default('draft');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('money_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('type', 20); // deposit or withdrawal
            $table->unsignedBigInteger('amount_mxn_cents');
            $table->string('status', 30)->default('pending');
            $table->string('reference')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('money_requests');
        Schema::dropIfExists('charging_stations');
    }
};
