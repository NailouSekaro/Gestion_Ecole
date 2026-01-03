<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EleveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'matricule_educ_master' => 'MAT' . $this->faker->unique()->numerify('############'),
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'date_naissance' => $this->faker->dateTimeBetween('-15 years', '-10 years')->format('Y-m-d'),
            'lieu_de_naissance' => $this->faker->city,
            'sexe' => $this->faker->randomElement(['M', 'F']),
            'email_parent' => $this->faker->unique()->safeEmail,
            'contact_parent' => '229' . $this->faker->numerify('9#######'),
            'aptitude_sport' => $this->faker->randomElement(['Oui', 'Non']),
            'photo' => 'assets/images/faces/default-avatar.jpg',
            'nationalite' => $this->faker->country,
        ];
    }
}
