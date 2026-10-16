<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::table('users', function (Blueprint $table) { $table->unsignedBigInteger('balance_mxn_cents')->default(0); $table->boolean('is_admin')->default(false); }); }
    public function down(): void { Schema::table('users', function (Blueprint $table) { $table->dropColumn(['balance_mxn_cents','is_admin']); }); }
};
