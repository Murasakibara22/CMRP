<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('dial_code')->default('+225');
            $table->string('phone')->unique();
            $table->string('adresse')->nullable();
            $table->unsignedBigInteger('montant_engagement')->nullable()->comment('null = pas de mensuel');
            $table->date('date_adhesion');
            $table->enum('status', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->timestamps();

            $table->index('phone');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
