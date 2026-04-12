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
        Schema::table('reclammations', function (Blueprint $table) {
            if (! Schema::hasColumn('reclammations', 'reponse')) {
                $table->text('reponse')->nullable()->after('description');
            }
            if (! Schema::hasColumn('reclammations', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('reponse');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reclammations', function (Blueprint $table) {
            if (Schema::hasColumn('reclammations', 'reponse')) {
                $table->dropColumn('reponse');
            }
            if (Schema::hasColumn('reclammations', 'resolved_at')) {
                $table->dropColumn('resolved_at');
            }
        });
    }
};
