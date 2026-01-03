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
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique();
            $table->string('telephone')->unique();
            $table->enum('sexe', ['M', 'F']);
            $table->unsignedBigInteger('matiere_id'); // ID de la matière enseignée
            $table->string('diplomes');
            $table->string('adresse');
            $table->timestamps();
             // Contraintes de clé étrangère
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};
