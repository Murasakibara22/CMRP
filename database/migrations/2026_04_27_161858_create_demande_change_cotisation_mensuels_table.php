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
        Schema::create('demande_change_cotisations', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
 
            /* Type de demande */
            $table->enum('type_demande', ['changement', 'arret']);
 
            /* Type actuel */
            $table->foreignId('ancien_type_cotisation_id')->nullable()->constrained('type_cotisation')->nullOnDelete();
            $table->integer('ancien_montant_engagement')->nullable();
 
            /* Type demandé (null si arret) */
            $table->foreignId('nouveau_type_cotisation_id')->nullable()->constrained('type_cotisation')->nullOnDelete();
            $table->integer('nouveau_montant_engagement')->nullable();
 
            /* Options */
            $table->boolean('supprimer_cotisations_retard')->default(false);
            $table->text('motif')->nullable();
 
            /* Statut */
            $table->enum('statut', ['en_attente', 'validee', 'rejetee'])->default('en_attente');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('motif_rejet')->nullable();
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_change_cotisations');
    }
};
