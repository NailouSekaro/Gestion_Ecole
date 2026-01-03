<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Matiere;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EnseignantFactory extends Factory
{
    public function definition(): array
    {
        // Créer un utilisateur avec le rôle enseignant
        $user = User::create([
            'name' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('123456'),
            'role' => 'enseignant',
            'photo' => 'images/default-avatar.jpg', // chemin relatif au dossier public
            'password_changed_at' => now(),
        ]);

        // Choisir une matière aléatoire
        $matiere = Matiere::inRandomOrder()->first();

        return [
            'user_id' => $user->id,
            'matricule' => 'ENS' . $this->faker->unique()->numberBetween(100, 999),
            'telephone' => '97' . $this->faker->unique()->numberBetween(1000000, 9999999),
            'sexe' => $this->faker->randomElement(['M', 'F']),
            'matiere_id' => $matiere ? $matiere->id : 1,
            'diplomes' => $this->faker->randomElement([
                'Licence en Mathématiques',
                'Licence en Physique',
                'Licence en Informatique',
                'Master en Lettres modernes',
                'CAPES',
                'Doctorat en Histoire',
            ]),
            'adresse' => 'Quartier ' . $this->faker->city,
        ];
    }
}
