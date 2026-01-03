<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->string('statut')->default('approved')->after('montant'); // Champ statut avec valeur par défaut
            $table->string('preuve')->nullable()->after('statut'); // Champ preuve, nullable pour les paiements Fedapay
        });
    }

    public function down()
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['statut', 'preuve']);
        });
    }
};
