<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_cotisation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotisation_id')->constrained('cotisation')->cascadeOnDelete();
            $table->string('type_operation');
            $table->unsignedBigInteger('montant');
            $table->text('note')->nullable();
            $table->json('snapshot_cotisation')->comment('État complet de la cotisation au moment de l\'opération');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['cotisation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_cotisation');
    }
};
