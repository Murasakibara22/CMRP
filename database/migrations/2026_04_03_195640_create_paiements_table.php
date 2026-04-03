<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('type_cotisation_id')->constrained('type_cotisation')->restrictOnDelete();
            $table->foreignId('cotisation_id')->nullable()->constrained('cotisation')->nullOnDelete();

            $table->unsignedBigInteger('montant');
            $table->enum('mode_paiement', ['espece', 'mobile_money', 'virement']);
            $table->string('reference')->nullable();
            $table->enum('statut', ['success', 'echec', 'en_attente'])->default('en_attente');

            // Métadonnées opérateur mobile, etc.
            $table->json('metadata')->nullable()->comment('ex: {operateur, reference, telephone, erreur}');

            $table->timestamp('date_paiement');
            $table->timestamps();

            $table->index(['customer_id', 'statut']);
            $table->index(['type_cotisation_id', 'statut']);
            $table->index('date_paiement');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiement');
    }
};
