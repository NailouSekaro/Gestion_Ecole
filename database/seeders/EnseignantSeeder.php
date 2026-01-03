<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\Annee_Academique;
use Illuminate\Support\Facades\DB;

class EnseignantSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classe::all();
        $annee = Annee_Academique::latest()->first();

        if ($classes->isEmpty() || !$annee) {
            $this->command->warn("⚠️ Vous devez d'abord avoir des données dans 'classes' et 'annee_academiques'");
            return;
        }

        // Créer 10 enseignants avec leurs users automatiquement via la factory
        $enseignants = Enseignant::factory(10)->create();

        // Assigner chaque enseignant à une ou plusieurs classes
        foreach ($enseignants as $enseignant) {
            $nombreClasses = rand(1, 3);
            $classesAttribuees = $classes->random($nombreClasses);

            foreach ($classesAttribuees as $classe) {
                // Vérifie si cette classe n’a pas déjà un enseignant pour l’année en cours
                $dejaAttribue = DB::table('enseignant_classe')
                    ->where('classe_id', $classe->id)
                    ->where('annee_academique_id', $annee->id)
                    ->exists();

                if (!$dejaAttribue) {
                    DB::table('enseignant_classe')->insert([
                        'enseignant_id' => $enseignant->id,
                        'classe_id' => $classe->id,
                        'annee_academique_id' => $annee->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info("✅ Enseignants + Users + Assignations de classes créés avec succès !");
    }
}
