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
        Schema::create('historique_reclammations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reclammation_id');
            $table->text('description')->nullable();
            $table->foreign('reclammation_id')->references('id')->on('reclammations')->onDelete('cascade');
            $table->string('status')->default('en_attente');
            $table->json('snapshot_reclammation')->nullable();
            $table->unsignedBigInteger('user_charged_id')->nullable();
            $table->unsignedBigInteger('cotisation_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_reclammations');
    }
};
