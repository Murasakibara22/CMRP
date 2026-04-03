<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depense', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_depense_id')->constrained('type_depense')->restrictOnDelete();
            $table->string('libelle');
            $table->unsignedBigInteger('montant');
            $table->date('date_depense');
            $table->text('note')->nullable();
            $table->string('justificatif')->nullable()->comment('Chemin du fichier justificatif');

            // Validation
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            $table->index(['type_depense_id', 'date_depense']);
            $table->index('date_depense');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depense');
    }
};
