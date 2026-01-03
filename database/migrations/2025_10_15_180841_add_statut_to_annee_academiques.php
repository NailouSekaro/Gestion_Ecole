<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('annee_academiques', function (Blueprint $table) {
            $table->enum('statut', ['active', 'inactive'])->default('inactive');
        });
    }

    public function down()
    {
        Schema::table('annee_academiques', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};
