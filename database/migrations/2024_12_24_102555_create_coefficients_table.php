<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Unique;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coefficients', function (Blueprint $table) {
            $table->id();
            $table->float('valeur_coefficient');
            $table->foreignId('classe_id')->constrained('classes');
            $table->foreignId('matiere_id')->constrained('matieres');
            $table->timestamps();

            $table->unique(['classe_id', 'matiere_id'],'unique_classe_matiere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coefficients');
    }
};
