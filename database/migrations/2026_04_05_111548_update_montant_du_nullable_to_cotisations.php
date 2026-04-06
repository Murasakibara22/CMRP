<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cotisation', function (Blueprint $table) {
            $table->unsignedBigInteger('montant_du')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotisation', function (Blueprint $table) {
            $table->unsignedBigInteger('montant_du')->nullable(false)->change();
        });
    }
};
