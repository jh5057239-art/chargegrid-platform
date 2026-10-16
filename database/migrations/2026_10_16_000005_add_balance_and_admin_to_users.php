<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'balance_mxn_cents')) {
                $table->unsignedBigInteger('balance_mxn_cents')->default(0);
            }
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            if (Schema::hasColumn('users', 'balance_mxn_cents')) {
                $table->dropColumn('balance_mxn_cents');
            }
        });
    }
};
