<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Annee_academique;

class InscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'eleve_id' => Eleve::factory(), // génère un élève automatiquement
            'classe_id' => Classe::inRandomOrder()->first()->id ?? 1,
            'annee_academique_id' => Annee_academique::inRandomOrder()->first()->id ?? 1,
            'type' => $this->faker->randomElement(['inscription', 'reinscription']),
            'statut' => $this->faker->randomElement(['Passant(e)', 'Doublant(e)']),
        ];
    }
}
