<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['entree', 'sortie']);
            $table->string('source')->nullable()->comment('Type de la transaction liée (ex: paiement, depense, etc.)');
            $table->unsignedBigInteger('source_id')->comment('ID de la paiement ou depense liée');
            $table->unsignedBigInteger('montant');
            $table->string('libelle');
            $table->timestamp('date_transaction');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['type', 'source']);
            $table->index('date_transaction');
            $table->index(['source', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
