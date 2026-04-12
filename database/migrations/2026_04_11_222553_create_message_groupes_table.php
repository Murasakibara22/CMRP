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
       Schema::create('message_groupes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // admin créateur
            $table->string('titre');
            $table->text('message');
            $table->enum('canal', ['sms', 'email'])->default('sms');
            $table->boolean('tous_les_customers')->default(false);
            $table->timestamp('envoyer_le')->nullable();  // null = immédiat
            $table->enum('statut', ['planifie', 'en_cours', 'envoye', 'echec'])->default('planifie');
            $table->integer('nb_destinataires')->default(0);
            $table->integer('nb_envoyes')->default(0);
            $table->integer('nb_echecs')->default(0);
            $table->timestamps();
        });
 
        Schema::create('message_groupe_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_groupe_id')->constrained('message_groupes')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('statut', ['en_attente', 'envoye', 'echec'])->default('en_attente');
            $table->timestamp('envoye_le')->nullable();
            $table->text('erreur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_groupe_customer');
        Schema::dropIfExists('message_groupes');
    }
};
