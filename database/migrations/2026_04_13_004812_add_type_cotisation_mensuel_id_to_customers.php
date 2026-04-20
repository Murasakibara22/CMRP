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
        Schema::table('customers', function (Blueprint $table) {
                $table->unsignedBigInteger('type_cotisation_mensuel_id')->nullable();

                $table->foreign('type_cotisation_mensuel_id')
                    ->references('id')
                    ->on('type_cotisation')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['type_cotisation_mensuel_id']);
            $table->dropColumn('type_cotisation_mensuel_id');
        });
    }
};
