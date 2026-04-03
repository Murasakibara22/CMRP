<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cout_engagement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('montant');
            $table->string('libelle');
            $table->enum('status', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cout_engagement');
    }
};
