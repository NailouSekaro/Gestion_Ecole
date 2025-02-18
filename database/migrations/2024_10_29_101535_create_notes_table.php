<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->float('valeur_note');
            $table->string('type_evaluation');
            $table->foreignId('eleve_id')->constrained('eleves');
            $table->foreignId('matiere_id')->constrained('matieres');
            $table->foreignId('classe_id')->constrained('classes');
            // $table->unsignedBigInteger('trimestre_id')->after('annee_academique_id');
            // $table->foreign('trimestre_id')->references('id')->on('trimestres')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
