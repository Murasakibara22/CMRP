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
        Schema::create('reclammations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('sujet');
            $table->text('description');
            $table->enum('status', ['en_attente', 'en_cours', 'resolu'])->default('en_attente');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreignId('user_charged_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cotisation_id')->nullable()->constrained('cotisation')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclammations');
    }
};
