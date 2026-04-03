<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('type_cotisation', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false);
            $table->enum('type', ['mensuel', 'ordinaire', 'jour_precis',]);
            $table->string('jour_recurrence')->nullable()->comment('ex: vendredi — pour type=jour_precis');
            $table->unsignedBigInteger('montant_objectif')->nullable()->comment('pour ramadan');
            $table->enum('status', ['actif', 'inactif'])->default('actif');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_cotisation');
    }
};
