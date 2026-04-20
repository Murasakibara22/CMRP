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
        Schema::table('type_cotisation', function (Blueprint $table) {
            $table->integer('montant_minimum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_cotisation', function (Blueprint $table) {
            $table->dropColumn('montant_minimum');
        });
    }
};
