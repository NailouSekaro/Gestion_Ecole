<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\Annee_academique;

class EleveSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère une année académique et une classe existantes
        $annee = Annee_academique::first();
        $classe = Classe::first();

        if (!$annee || !$classe) {
            $this->command->warn('⚠️ Aucune année académique ou classe trouvée. Exécutez d’abord les seeders correspondants.');
            return;
        }

        // Exemple : créer 5 élèves
        for ($i = 1; $i <= 5; $i++) {
            $eleve = Eleve::create([
                'matricule_educ_master' => 'MAT' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nom' => 'Nom' . $i,
                'prenom' => 'Prenom' . $i,
                'date_naissance' => now()->subYears(10 + $i)->format('Y-m-d'),
                'lieu_de_naissance' => 'Parakou',
                'sexe' => $i % 2 == 0 ? 'M' : 'F',
                'email_parent' => "parent$i@example.com",
                'contact_parent' => '2299000000' . $i,
                'aptitude_sport' => 'Oui',
                'photo' => 'assets/images/faces/default-avatar.jpg',
                'nationalite' => 'Béninoise',
            ]);

            // Créer aussi l’inscription correspondante
            Inscription::create([
                'eleve_id' => $eleve->id,
                'classe_id' => $classe->id,
                'annee_academique_id' => $annee->id,
                'type' => 'inscription',
                'statut' => 'Passant(e)',
            ]);
        }
    }
}
