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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('statut');
            $table->string('type');
            $table->foreignId('eleve_id')->constrained('eleves');
            $table->foreignId('classe_id')->constrained('classes');
            $table->foreignId('annee_academique_id')->constrained('annee_academiques');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
