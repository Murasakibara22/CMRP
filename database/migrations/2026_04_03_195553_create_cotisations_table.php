<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotisation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('type_cotisation_id')->constrained('type_cotisation')->restrictOnDelete();

            // Période — uniquement pour le mensuel
            $table->unsignedTinyInteger('mois')->nullable()->comment('1-12');
            $table->unsignedSmallInteger('annee')->nullable();

            // Montants
            $table->unsignedBigInteger('montant_du');
            $table->unsignedBigInteger('montant_paye')->default(0);
            $table->unsignedBigInteger('montant_restant');

            $table->enum('statut', ['a_jour', 'en_retard', 'partiel'])->default('en_retard');
            $table->enum('mode_paiement', ['espece', 'mobile_money', 'virement'])->nullable();

            // Validation admin
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            // Unicité mensuel : un seul enregistrement par fidèle/type/mois/année
            $table->unique(['customer_id', 'type_cotisation_id', 'mois', 'annee'], 'cotisation_unique_mensuel');

            $table->index(['customer_id', 'statut']);
            $table->index(['type_cotisation_id', 'statut']);
            $table->index(['mois', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotisation');
    }
};
