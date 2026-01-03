<?php

namespace App\Http\Controllers;

use App\Models\EmploiTemps;
use App\Models\classe;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\Annee_academique;
use App\Models\coefficient;
use App\Models\Eleve;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class EmploiTempsController extends Controller
{
    public function create()
    {
        $classes = Classe::all();
        $matieres = Matiere::all();
        $enseignants = Enseignant::all();
        $anneesAcademiques = Annee_academique::all();
        return view('emploi_temps.create', compact('classes', 'matieres', 'enseignants', 'anneesAcademiques'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'classe_id' => 'required|exists:classes,id',
    //         'matiere_id' => 'required|exists:matieres,id',
    //         'enseignant_id' => 'required|exists:enseignants,id',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //         'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
    //         'heure_debut' => 'required|date_format:H:i',
    //         'heure_fin' => 'required|date_format:H:i|after:heure_debut',
    //         'salle' => 'nullable|string|max:50',
    //     ]);

    //     EmploiTemps::create($request->all());

    //     return redirect()->route('emploi_temps.create')->with('success_message', 'Emploi du temps ajouté avec succès.');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'classe_id' => 'required|exists:classes,id',
    //         'matiere_id' => 'required|exists:matieres,id',
    //         'enseignant_id' => 'required|exists:enseignants,id',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //         'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
    //         'heure_debut' => 'required|date_format:H:i',
    //         'heure_fin' => 'required|date_format:H:i|after:heure_debut',
    //         'salle' => 'nullable|string|max:50',
    //     ]);

    //     $classeId = $request->classe_id;
    //     $matiereId = $request->matiere_id;
    //     $enseignantId = $request->enseignant_id;
    //     $anneeAcademiqueId = $request->annee_academique_id;
    //     $jour = $request->jour;
    //     $heureDebut = $request->heure_debut;
    //     $heureFin = $request->heure_fin;

    //     // Vérifier si l'enseignant est assigné à la classe pour l'année académique
    //     $isAssigned = DB::table('enseignant_classe')
    //         ->where('enseignant_id', $enseignantId)
    //         ->where('classe_id', $classeId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->exists();

    //     if (!$isAssigned) {
    //         return redirect()->back()->with('error_message', 'L\'enseignant n\'est pas assigné à cette classe pour l\'année académique sélectionnée.');
    //     }

    //     // Vérifier si la matière correspond à celle de l'enseignant
    //     $enseignant = Enseignant::find($enseignantId);
    //     if ($enseignant->matiere_id != $matiereId) {
    //         return redirect()->back()->with('error_message', 'La matière sélectionnée ne correspond pas à celle de l\'enseignant.');
    //     }

    //     // Vérifier les chevauchements horaires pour la classe
    //     $existingEmplois = EmploiTemps::where('classe_id', $classeId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->where('jour', $jour)
    //         ->get();

    //     foreach ($existingEmplois as $emploi) {
    //         $existingStart = strtotime($emploi->heure_debut);
    //         $existingEnd = strtotime($emploi->heure_fin);
    //         $newStart = strtotime($heureDebut);
    //         $newEnd = strtotime($heureFin);

    //         if (($newStart >= $existingStart && $newStart < $existingEnd) ||
    //             ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
    //             ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
    //             return redirect()->back()->with('error_message', 'Un chevauchement horaire existe pour cette classe à ce jour et cette heure.');
    //         }
    //     }

    //     // Vérifier les chevauchements pour l'enseignant
    //     $existingEmploisEnseignant = EmploiTemps::where('enseignant_id', $enseignantId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->where('jour', $jour)
    //         ->get();

    //     foreach ($existingEmploisEnseignant as $emploi) {
    //         $existingStart = strtotime($emploi->heure_debut);
    //         $existingEnd = strtotime($emploi->heure_fin);
    //         $newStart = strtotime($heureDebut);
    //         $newEnd = strtotime($heureFin);

    //         if (($newStart >= $existingStart && $newStart < $existingEnd) ||
    //             ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
    //             ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
    //             return redirect()->back()->with('error_message', 'L\'enseignant a déjà un cours à ce jour et cette heure.');
    //         }
    //     }

    //     // Enregistrer si toutes les validations passent
    //     EmploiTemps::create($request->all());

    //     return redirect()->route('emploi_temps.create')->with('success_message', 'Emploi du temps ajouté avec succès.');
    // }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'classe_id' => 'required|exists:classes,id',
    //         'matiere_id' => 'required|exists:matieres,id',
    //         'enseignant_id' => 'required|exists:enseignants,id',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //         'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
    //         'heure_debut' => 'required|date_format:H:i',
    //         'heure_fin' => 'required|date_format:H:i|after:heure_debut',
    //         'salle' => 'nullable|string|max:50',
    //     ]);

    //     $classeId = $request->classe_id;
    //     $matiereId = $request->matiere_id;
    //     $enseignantId = $request->enseignant_id;
    //     $anneeAcademiqueId = $request->annee_academique_id;
    //     $jour = $request->jour;
    //     $heureDebut = $request->heure_debut;
    //     $heureFin = $request->heure_fin;

    //     // Nouveau contrôle : Vérifier si la matière a un coefficient pour la classe
    //     $hasCoefficient = Coefficient::where('classe_id', $classeId)
    //         ->where('matiere_id', $matiereId)
    //         ->exists();

    //     if (!$hasCoefficient) {
    //         return redirect()->back()->with('error_message', 'Cette matière ne fait pas partie des matières de cette classe.');
    //     }

    //     // Vérifier si l'enseignant est assigné à la classe pour l'année académique
    //     $isAssigned = DB::table('enseignant_classe')
    //         ->where('enseignant_id', $enseignantId)
    //         ->where('classe_id', $classeId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->exists();

    //     if (!$isAssigned) {
    //         return redirect()->back()->with('error_message', 'L\'enseignant n\'est pas assigné à cette classe pour l\'année académique sélectionnée.');
    //     }

    //     // Vérifier si la matière correspond à celle de l'enseignant
    //     $enseignant = Enseignant::find($enseignantId);
    //     if ($enseignant->matiere_id != $matiereId) {
    //         return redirect()->back()->with('error_message', 'La matière sélectionnée ne correspond pas à celle de l\'enseignant.');
    //     }

    //     // Vérifier les chevauchements horaires pour la classe
    //     $existingEmplois = EmploiTemps::where('classe_id', $classeId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->where('jour', $jour)
    //         ->get();

    //     foreach ($existingEmplois as $emploi) {
    //         $existingStart = strtotime($emploi->heure_debut);
    //         $existingEnd = strtotime($emploi->heure_fin);
    //         $newStart = strtotime($heureDebut);
    //         $newEnd = strtotime($heureFin);

    //         if (($newStart >= $existingStart && $newStart < $existingEnd) ||
    //             ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
    //             ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
    //             return redirect()->back()->with('error_message', 'Un chevauchement horaire existe pour cette classe à ce jour et cette heure.');
    //         }
    //     }

    //     // Vérifier les chevauchements pour l'enseignant
    //     $existingEmploisEnseignant = EmploiTemps::where('enseignant_id', $enseignantId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->where('jour', $jour)
    //         ->get();

    //     foreach ($existingEmploisEnseignant as $emploi) {
    //         $existingStart = strtotime($emploi->heure_debut);
    //         $existingEnd = strtotime($emploi->heure_fin);
    //         $newStart = strtotime($heureDebut);
    //         $newEnd = strtotime($heureFin);

    //         if (($newStart >= $existingStart && $newStart < $existingEnd) ||
    //             ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
    //             ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
    //             return redirect()->back()->with('error_message', 'L\'enseignant a déjà un cours à ce jour et cette heure.');
    //         }
    //     }

    //     // Enregistrer si toutes les validations passent
    //     EmploiTemps::create($request->all());

    //     return redirect()->route('emploi_temps.create')->with('success_message', 'Emploi du temps ajouté avec succès.');
    // }


    // public function getEnseignant(Request $request)
    // {
    //     $classeId = $request->classe_id;
    //     $matiereId = $request->matiere_id;
    //     $anneeAcademiqueId = $request->annee_academique_id;

    //     $enseignant = DB::table('enseignant_classe')
    //         ->join('enseignants', 'enseignants.id', '=', 'enseignant_classe.enseignant_id')
    //         ->where('enseignant_classe.classe_id', $classeId)
    //         ->where('enseignant_classe.annee_academique_id', $anneeAcademiqueId)
    //         ->where('enseignants.matiere_id', $matiereId)
    //         ->select('enseignants.id', 'enseignants.user->name as name') // Assumes user relation
    //         ->first();

    //     return response()->json($enseignant ?? ['id' => null, 'name' => 'Aucun enseignant assigné']);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
            'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
        ]);

        $classeId = $request->classe_id;
        $matiereId = $request->matiere_id;
        $anneeAcademiqueId = $request->annee_academique_id;
        $jour = $request->jour;
        $heureDebut = $request->heure_debut;
        $heureFin = $request->heure_fin;

        // Nouveau contrôle : Vérifier si la matière a un coefficient pour la classe
        $hasCoefficient = Coefficient::where('classe_id', $classeId)
            ->where('matiere_id', $matiereId)
            ->exists();

        if (!$hasCoefficient) {
            return redirect()->back()->with('error_message', 'Cette matière ne fait pas partie des matières de cette classe.');
        }

        // Vérifier les chevauchements horaires pour la classe
        $existingEmplois = EmploiTemps::where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeAcademiqueId)
            ->where('jour', $jour)
            ->get();

        foreach ($existingEmplois as $emploi) {
            $existingStart = strtotime($emploi->heure_debut);
            $existingEnd = strtotime($emploi->heure_fin);
            $newStart = strtotime($heureDebut);
            $newEnd = strtotime($heureFin);

            if (($newStart >= $existingStart && $newStart < $existingEnd) ||
                ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
                ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
                return redirect()->back()->with('error_message', 'Un chevauchement horaire existe pour cette classe à ce jour et cette heure.');
            }
        }

        // Enregistrer sans enseignant ni salle (ils seront déduits via relations si besoin)
        EmploiTemps::create([
            'classe_id' => $classeId,
            'matiere_id' => $matiereId,
            'annee_academique_id' => $anneeAcademiqueId,
            'jour' => $jour,
            'heure_debut' => $heureDebut,
            'heure_fin' => $heureFin,
        ]);

        return redirect()->route('emploi_temps.create')->with('success_message', 'Emploi du temps ajouté avec succès.');
    }


    public function getEnseignant(Request $request)
    {
        $classeId = $request->classe_id;
        $matiereId = $request->matiere_id;
        $anneeAcademiqueId = $request->annee_academique_id;

        $enseignant = DB::table('enseignant_classe')
            ->join('enseignants', 'enseignants.id', '=', 'enseignant_classe.enseignant_id')
            ->join('users', 'users.id', '=', 'enseignants.user_id') // Joindre la table users
            ->where('enseignant_classe.classe_id', $classeId)
            ->where('enseignant_classe.annee_academique_id', $anneeAcademiqueId)
            ->where('enseignants.matiere_id', $matiereId)
            ->select('enseignants.id', 'users.name as name')
            ->first();

        return response()->json($enseignant ? $enseignant : ['id' => null, 'name' => 'Aucun enseignant assigné']);
    }

    // public function showHebdomadaire($classeId, $anneeAcademiqueId)
    // {
    //     $classe = Classe::findOrFail($classeId);
    //     $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);
    //     $emplois = EmploiTemps::where('classe_id', $classeId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->orderBy('jour')
    //         ->orderBy('heure_debut')
    //         ->get()
    //         ->groupBy('jour');

    //     return view('emploi_temps.hebdomadaire', compact('classe', 'anneeAcademique', 'emplois'));
    // }

    // public function showHebdomadaire($classeId, $anneeAcademiqueId)
    // {
    //     $classe = Classe::findOrFail($classeId);
    //     $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);
    //     $emplois = EmploiTemps::where('classe_id', $classeId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->orderBy('jour')
    //         ->orderBy('heure_debut')
    //         ->get()
    //         ->groupBy('jour');

    //     return view('emploi_temps.hebdomadaire', compact('classe', 'anneeAcademique', 'emplois'));
    // }


    public function index()
    {
        $emplois = EmploiTemps::with(['classe', 'matiere', 'annee_academique'])->get();
        return view('emploi_temps.index', compact('emplois'));
    }

    // public function generateAutomatic(Request $request)
    // {
    //     $anneeAcademiqueId = $request->annee_academique_id; // Passé via formulaire ou route
    //     $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    //     $heuresDisponibles = [ '08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00' ]; // Plage standard, ajustable

    //     $classes = Classe::all();

    //     foreach ($classes as $classe) {
    //         $matieres = Coefficient::where('classe_id', $classe->id)->with('matiere')->get();

    //         foreach ($matieres as $coeff) {
    //             $matiere = $coeff->matiere;
    //             $enseignant = Enseignant::where('matiere_id', $matiere->id)->first(); // Assume un enseignant par matière, ajuste si multiple

    //             if ($enseignant) {
    //                 // Vérifier assignation
    //                 $isAssigned = DB::table('enseignant_classe')
    //                     ->where('enseignant_id', $enseignant->id)
    //                     ->where('classe_id', $classe->id)
    //                     ->where('annee_academique_id', $anneeAcademiqueId)
    //                     ->exists();

    //                 if ($isAssigned) {
    //                     // Générer créneau aléatoire sans chevauchement
    //                     $generated = false;
    //                     while (!$generated) {
    //                         $jour = $jours[array_rand($jours)];
    //                         $heureDebut = $heuresDisponibles[array_rand($heuresDisponibles)];
    //                         $heureFin = date('H:i', strtotime($heureDebut . ' +1 hour'));

    //                         $chevauchement = EmploiTemps::where('classe_id', $classe->id)
    //                             ->where('annee_academique_id', $anneeAcademiqueId)
    //                             ->where('jour', $jour)
    //                             ->where(function ($query) use ($heureDebut, $heureFin) {
    //                                 $query->whereBetween('heure_debut', [$heureDebut, $heureFin])
    //                                     ->orWhereBetween('heure_fin', [$heureDebut, $heureFin]);
    //                             })
    //                             ->exists();

    //                         if (!$chevauchement) {
    //                             EmploiTemps::create([
    //                                 'classe_id' => $classe->id,
    //                                 'matiere_id' => $matiere->id,
    //                                 'enseignant_id' => $enseignant->id,
    //                                 'annee_academique_id' => $anneeAcademiqueId,
    //                                 'jour' => $jour,
    //                                 'heure_debut' => $heureDebut,
    //                                 'heure_fin' => $heureFin,
    //                                 'salle' => 'Salle ' . rand(1, 10), // Aleatoire, ajuste
    //                             ]);
    //                             $generated = true;
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return redirect()->route('emploi_temps.index')->with('success_message', 'Emplois du temps générés automatiquement pour toutes les classes.');
    // }

    // public function verify(Request $request)
    // {
    //     $request->validate(['educ_master' => 'required|string|max:20']);

    //     $eleve = Eleve::where('matricule_educ_master', $request->educ_master)->first();

    //     if (!$eleve) {
    //         return response()->json(['error' => 'Numéro Educ Master invalide.']);
    //     }

    //     $inscription = Inscription::where('eleve_id', $eleve->id)->latest()->first();
    //     $classeId = $inscription->classe_id;
    //     $anneeAcademiqueId = $inscription->annee_academique_id;

    //     return response()->json(['redirect' => route('emploi_temps.hebdomadaire', ['classeId' => $classeId, 'anneeAcademiqueId' => $anneeAcademiqueId])]);
    // }

    // public function verify(Request $request)
    // {
    //     $request->validate(['educ_master' => 'required|string|max:20']);

    //     $eleve = Eleve::where('matricule_educ_master', $request->educ_master)->first();

    //     if (!$eleve) {
    //         return response()->json(['error' => 'Numéro Educ Master invalide.']);
    //     }

    //     $inscription = Inscription::where('eleve_id', $eleve->id)->latest()->first();
    //     if (!$inscription) {
    //         return response()->json(['error' => 'Aucune inscription trouvée pour cet élève.']);
    //     }

    //     $classeId = $inscription->classe_id;
    //     $anneeAcademiqueId = $inscription->annee_academique_id;


    //     return response()->json(['redirect' => route('emploi_temps.hebdomadaire', ['classeId' => $classeId, 'anneeAcademiqueId' => $anneeAcademiqueId])]);
    // }

    // Méthode pour vérifier le numéro Educ Master (PUBLIQUE)
public function verify(Request $request)
{
    try {
        $request->validate([
            'educ_master' => 'required|string|max:20'
        ]);

        $eleve = Eleve::where('matricule_educ_master', $request->educ_master)->first();

        if (!$eleve) {
            return response()->json([
                'success' => false,
                'error' => 'Numéro Educ Master invalide. Veuillez vérifier et réessayer.'
            ]);
        }

        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->latest()
            ->first();

        if (!$inscription) {
            return response()->json([
                'success' => false,
                'error' => 'Aucune inscription trouvée pour cet élève.'
            ]);
        }

        $classeId = $inscription->classe_id;
        $anneeAcademiqueId = $inscription->annee_academique_id;

        // Vérifier si l'emploi du temps existe
        $emploiExists = EmploiTemps::where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeAcademiqueId)
            ->exists();

        if (!$emploiExists) {
            return response()->json([
                'success' => false,
                'error' => 'Aucun emploi du temps disponible pour votre classe.'
            ]);
        }

        return response()->json([
            'success' => true,
            'redirect' => route('emploi_temps.consulter', [
                'classeId' => $classeId,
                'anneeAcademiqueId' => $anneeAcademiqueId
            ])
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Une erreur est survenue. Veuillez réessayer.'
        ], 500);
    }
}

// Méthode pour afficher l'emploi du temps (VERSION PUBLIQUE pour les élèves)
public function showHebdomadairePublic($classeId, $anneeAcademiqueId)
{
    $classe = Classe::findOrFail($classeId);
    $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);

    $emplois = EmploiTemps::where('classe_id', $classeId)
        ->where('annee_academique_id', $anneeAcademiqueId)
        ->with('matiere') // Charge la relation matière
        ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
        ->orderBy('heure_debut')
        ->get()
        ->groupBy('jour');

    return view('emploi_temps.consulter', compact('classe', 'anneeAcademique', 'emplois'));
}

// Méthode pour afficher l'emploi du temps (VERSION ADMIN avec auth)
public function showHebdomadaire($classeId, $anneeAcademiqueId)
{
    $classe = Classe::findOrFail($classeId);
    $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);

    $emplois = EmploiTemps::where('classe_id', $classeId)
        ->where('annee_academique_id', $anneeAcademiqueId)
        ->with('matiere')
        ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
        ->orderBy('heure_debut')
        ->get()
        ->groupBy('jour');

    return view('emploi_temps.hebdomadaire', compact('classe', 'anneeAcademique', 'emplois'));
}

    public function exportPdf($classeId, $anneeAcademiqueId)
    {
        $classe = Classe::findOrFail($classeId);
        $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);
        $emplois = EmploiTemps::where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeAcademiqueId)
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour');

        $pdf = Pdf::loadView('emploi_temps.pdf', compact('classe', 'anneeAcademique', 'emplois'));
        $pdf->setPaper('A4', 'landscape'); // Paysage pour une meilleure lisibilité de la grille

        return $pdf->download('emploi_temps_' . $classe->nom_classe . '_' . $anneeAcademique->annee . '.pdf');
    }

    // public function edit($classeId, $anneeAcademiqueId)
    // {
    //     $classe = Classe::findOrFail($classeId);
    //     $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);
    //     $emplois = EmploiTemps::where('classe_id', $classeId)
    //         ->where('annee_academique_id', $anneeAcademiqueId)
    //         ->orderBy('jour')
    //         ->orderBy('heure_debut')
    //         ->get()
    //         ->groupBy('jour');

    //     return view('emploi_temps.edit', compact('classe', 'anneeAcademique', 'emplois'));
    // }


    // public function update(Request $request, $classeId, $anneeAcademiqueId)
    // {
    //     Log::info('Update appelé pour classeId: ' . $classeId . ', anneeAcademiqueId: ' . $anneeAcademiqueId);

    //     if ($request->ajax()) {
    //         $request->validate([
    //             'emploi_id' => 'required|exists:emplois_temps,id',
    //             'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
    //             'heure_debut' => 'required|date_format:H:i',
    //             'heure_fin' => 'required|date_format:H:i|after:heure_debut',
    //             'matiere_id' => 'required|exists:matieres,id',
    //         ], [
    //             'emploi_id.required' => 'L\'ID de l\'emploi est requis.',
    //             'jour.required' => 'Le jour est requis.',
    //             'heure_debut.required' => 'L\'heure de début est requise.',
    //             'heure_fin.required' => 'L\'heure de fin est requise.',
    //             'matiere_id.required' => 'La matière est requise.',
    //         ]);

    //         $emploi = EmploiTemps::findOrFail($request->input('emploi_id'));
    //         $emploi->update([
    //             'jour' => $request->input('jour'),
    //             'heure_debut' => $request->input('heure_debut'),
    //             'heure_fin' => $request->input('heure_fin'),
    //             'matiere_id' => $request->input('matiere_id'),
    //         ]);

    //         return response()->json(['success' => true, 'message' => 'Emploi modifié avec succès.']);
    //     }

    //     // Logique existante pour la soumission complète
    //     $request->validate([
    //         'emplois.*.jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
    //         'emplois.*.heure_debut' => 'required|date_format:H:i',
    //         'emplois.*.heure_fin' => 'required|date_format:H:i|after:heure_debut',
    //         'emplois.*.matiere_id' => 'required|exists:matieres,id',
    //     ]);

    //     $classe = Classe::findOrFail($classeId);
    //     $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);

    //     foreach ($request->input('emplois') as $emploiData) {
    //         if (isset($emploiData['id'])) {
    //             $emploi = EmploiTemps::findOrFail($emploiData['id']);
    //             $emploi->update([
    //                 'jour' => $emploiData['jour'],
    //                 'heure_debut' => $emploiData['heure_debut'],
    //                 'heure_fin' => $emploiData['heure_fin'],
    //                 'matiere_id' => $emploiData['matiere_id'],
    //             ]);
    //         } else {
    //             EmploiTemps::create([
    //                 'classe_id' => $classeId,
    //                 'matiere_id' => $emploiData['matiere_id'],
    //                 'annee_academique_id' => $anneeAcademiqueId,
    //                 'jour' => $emploiData['jour'],
    //                 'heure_debut' => $emploiData['heure_debut'],
    //                 'heure_fin' => $emploiData['heure_fin'],
    //             ]);
    //         }
    //     }

    //     return redirect()->route('emploi_temps.edit', ['classeId' => $classeId, 'anneeAcademiqueId' => $anneeAcademiqueId])
    //         ->with('success_message', 'Emploi du temps modifié avec succès.');
    // }


    public function edit($classeId, $anneeAcademiqueId)
    {
        $classe = Classe::findOrFail($classeId);
        $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);
        $emplois = EmploiTemps::where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeAcademiqueId)
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour');

        return view('emploi_temps.edit', compact('classe', 'anneeAcademique', 'emplois'));
    }

// Nouvelle méthode pour modifier un seul créneau via AJAX
// public function updateCreneau(Request $request)
//     {
//         try {
//             // Nettoyer les heures
//             $heureDebut = substr($request->heure_debut, 0, 5);
//             $heureFin = substr($request->heure_fin, 0, 5);

//             $validated = $request->validate([
//                 'emploi_id' => 'required|exists:emplois_temps,id',
//                 'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
//                 'heure_debut' => 'required',
//                 'heure_fin' => 'required|after:heure_debut',
//                 'matiere_id' => 'required|exists:matieres,id',
//             ]);

//             $validated['heure_debut'] = $heureDebut;
//             $validated['heure_fin'] = $heureFin;

//             $emploi = EmploiTemps::findOrFail($validated['emploi_id']);

//             $emploi->update([
//                 'jour' => $validated['jour'],
//                 'heure_debut' => $validated['heure_debut'],
//                 'heure_fin' => $validated['heure_fin'],
//                 'matiere_id' => $validated['matiere_id'],
//             ]);

//             return response()->json([
//                 'success' => true,
//                 'message' => 'Créneau modifié avec succès.'
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Erreur: ' . $e->getMessage()
//             ], 500);
//         }
//     }

public function updateCreneau(Request $request)
{
    try {
        // Nettoyer les heures
        $heureDebut = substr($request->heure_debut, 0, 5);
        $heureFin = substr($request->heure_fin, 0, 5);

        $validated = $request->validate([
            'emploi_id' => 'required|exists:emplois_temps,id',
            'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'matiere_id' => 'required|exists:matieres,id',
        ]);

        $validated['heure_debut'] = $heureDebut;
        $validated['heure_fin'] = $heureFin;

        $emploi = EmploiTemps::findOrFail($validated['emploi_id']);
        $classeId = $emploi->classe_id;
        $anneeAcademiqueId = $emploi->annee_academique_id;
        $jour = $validated['jour'];
        $heureDebut = $validated['heure_debut'];
        $heureFin = $validated['heure_fin'];

        // Vérifier les chevauchements horaires pour la classe, sauf l'emploi actuel
        $existingEmplois = EmploiTemps::where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeAcademiqueId)
            ->where('jour', $jour)
            ->where('id', '!=', $emploi->id) // Exclure l'emploi en cours de modification
            ->get();

        foreach ($existingEmplois as $existingEmploi) {
            $existingStart = strtotime($existingEmploi->heure_debut);
            $existingEnd = strtotime($existingEmploi->heure_fin);
            $newStart = strtotime($heureDebut);
            $newEnd = strtotime($heureFin);

            if (($newStart >= $existingStart && $newStart < $existingEnd) ||
                ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
                ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
                return response()->json([
                    'danger' => true,
                    'message' => 'Un chevauchement horaire existe pour cette classe à ce jour et cette heure.'
                ], 422);
            }
        }

        // Vérifier si la matière a un coefficient pour la classe
        $hasCoefficient = Coefficient::where('classe_id', $classeId)
            ->where('matiere_id', $validated['matiere_id'])
            ->exists();

        if (!$hasCoefficient) {
            return response()->json([
                'danger' => true,
                'message' => 'Cette matière ne fait pas partie des matières de cette classe.'
            ], 422);
        }

        $emploi->update([
            'jour' => $validated['jour'],
            'heure_debut' => $validated['heure_debut'],
            'heure_fin' => $validated['heure_fin'],
            'matiere_id' => $validated['matiere_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Créneau modifié avec succès.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}



// Méthode pour supprimer un créneau
public function deleteCreneau($id)
{
    try {
        $emploi = EmploiTemps::findOrFail($id);
        $emploi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Créneau supprimé avec succès.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
}

// Mise à jour globale (formulaire complet)
// public function update(Request $request, $classeId, $anneeAcademiqueId)
// {
//     // Nettoyer les heures pour enlever les secondes si présentes
//     $emplois = $request->input('emplois', []);
//     foreach ($emplois as $key => $emploi) {
//         if (isset($emploi['heure_debut'])) {
//             $emplois[$key]['heure_debut'] = substr($emploi['heure_debut'], 0, 5);
//         }
//         if (isset($emploi['heure_fin'])) {
//             $emplois[$key]['heure_fin'] = substr($emploi['heure_fin'], 0, 5);
//         }
//     }
//     $request->merge(['emplois' => $emplois]);

//     $request->validate([
//         'emplois.*.id' => 'sometimes|exists:emplois_temps,id',
//         'emplois.*.jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
//         'emplois.*.heure_debut' => 'required',
//         'emplois.*.heure_fin' => 'required|after:emplois.*.heure_debut',
//         'emplois.*.matiere_id' => 'required|exists:matieres,id',
//     ]);

//     $classe = Classe::findOrFail($classeId);
//     $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);

//     foreach ($request->input('emplois', []) as $emploiData) {
//         if (isset($emploiData['id'])) {
//             // Modification d'un emploi existant
//             $emploi = EmploiTemps::findOrFail($emploiData['id']);
//             $emploi->update([
//                 'jour' => $emploiData['jour'],
//                 'heure_debut' => $emploiData['heure_debut'],
//                 'heure_fin' => $emploiData['heure_fin'],
//                 'matiere_id' => $emploiData['matiere_id'],
//             ]);
//         }
//     }

//     // Gestion des nouveaux créneaux
//     if ($request->has('emplois.new')) {
//         $newEmplois = $request->input('emplois.new');
//         $count = count($newEmplois['jour'] ?? []);

//         for ($i = 0; $i < $count; $i++) {
//             EmploiTemps::create([
//                 'classe_id' => $classeId,
//                 'matiere_id' => $newEmplois['matiere_id'][$i],
//                 'annee_academique_id' => $anneeAcademiqueId,
//                 'jour' => $newEmplois['jour'][$i],
//                 'heure_debut' => $newEmplois['heure_debut'][$i],
//                 'heure_fin' => $newEmplois['heure_fin'][$i],
//             ]);
//         }
//     }

//     return redirect()->route('emploi_temps.edit', [
//         'classeId' => $classeId,
//         'anneeAcademiqueId' => $anneeAcademiqueId
//     ])->with('success', 'Emploi du temps modifié avec succès.');
// }


    public function update(Request $request, $classeId, $anneeAcademiqueId)
{
    Log::info('Update appelé pour classeId: ' . $classeId . ', anneeAcademiqueId: ' . $anneeAcademiqueId);

    // Nettoyer et valider les données
    $emplois = $request->input('emplois', []);
    foreach ($emplois as $key => $emploi) {
        if (isset($emploi['heure_debut']) && is_string($emploi['heure_debut'])) {
            $emplois[$key]['heure_debut'] = substr($emploi['heure_debut'], 0, 5);
        }
        if (isset($emploi['heure_fin']) && is_string($emploi['heure_fin'])) {
            $emplois[$key]['heure_fin'] = substr($emploi['heure_fin'], 0, 5);
        }
    }
    $request->merge(['emplois' => $emplois]);

    $request->validate([
        'emplois.*.id' => 'sometimes|exists:emplois_temps,id',
        'emplois.*.jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
        'emplois.*.heure_debut' => 'required|date_format:H:i',
        'emplois.*.heure_fin' => 'required|date_format:H:i|after:emplois.*.heure_debut',
        'emplois.*.matiere_id' => 'required|exists:matieres,id',
    ]);

    $classe = Classe::findOrFail($classeId);
    $anneeAcademique = Annee_Academique::findOrFail($anneeAcademiqueId);

    // Vérifier les chevauchements pour les emplois existants
    foreach ($request->input('emplois', []) as $emploiData) {
        $emploiId = $emploiData['id'] ?? null;
        $jour = $emploiData['jour'];
        $heureDebut = $emploiData['heure_debut'];
        $heureFin = $emploiData['heure_fin'];

        // Vérifier si la matière a un coefficient
        $hasCoefficient = Coefficient::where('classe_id', $classeId)
            ->where('matiere_id', $emploiData['matiere_id'])
            ->exists();

        if (!$hasCoefficient) {
            return redirect()->back()->with('error_message', 'La matière ' . \App\Models\Matiere::find($emploiData['matiere_id'])->nom . ' ne fait pas partie des matières de cette classe.');
        }

        // Vérifier les chevauchements
        $existingEmplois = EmploiTemps::where('classe_id', $classeId)
            ->where('annee_academique_id', $anneeAcademiqueId)
            ->where('jour', $jour)
            ->where('id', '!=', $emploiId)
            ->get();

        foreach ($existingEmplois as $existingEmploi) {
            $existingStart = strtotime($existingEmploi->heure_debut);
            $existingEnd = strtotime($existingEmploi->heure_fin);
            $newStart = strtotime($heureDebut);
            $newEnd = strtotime($heureFin);

            if (($newStart >= $existingStart && $newStart < $existingEnd) ||
                ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
                ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
                return redirect()->back()->with('error_message', 'Un chevauchement horaire existe pour ' . $jour . ' entre ' . $heureDebut . ' et ' . $heureFin . '.');
            }
        }
    }

    // Mettre à jour les emplois existants
    foreach ($request->input('emplois', []) as $emploiData) {
        if (isset($emploiData['id'])) {
            $emploi = EmploiTemps::findOrFail($emploiData['id']);
            $emploi->update([
                'jour' => $emploiData['jour'],
                'heure_debut' => $emploiData['heure_debut'],
                'heure_fin' => $emploiData['heure_fin'],
                'matiere_id' => $emploiData['matiere_id'],
            ]);
        }
    }

    // Gestion des nouveaux créneaux
    if ($request->has('emplois.new')) {
        $newEmplois = $request->input('emplois.new');
        $count = count($newEmplois['jour'] ?? []);

        for ($i = 0; $i < $count; $i++) {
            $jour = $newEmplois['jour'][$i];
            $heureDebut = substr($newEmplois['heure_debut'][$i], 0, 5);
            $heureFin = substr($newEmplois['heure_fin'][$i], 0, 5);
            $matiereId = $newEmplois['matiere_id'][$i];

            // Vérifier si la matière a un coefficient
            $hasCoefficient = Coefficient::where('classe_id', $classeId)
                ->where('matiere_id', $matiereId)
                ->exists();

            if (!$hasCoefficient) {
                return redirect()->back()->with('error_message', 'La matière ' . \App\Models\Matiere::find($matiereId)->nom . ' ne fait pas partie des matières de cette classe.');
            }

            // Vérifier les chevauchements pour les nouveaux créneaux
            $existingEmplois = EmploiTemps::where('classe_id', $classeId)
                ->where('annee_academique_id', $anneeAcademiqueId)
                ->where('jour', $jour)
                ->get();

            foreach ($existingEmplois as $existingEmploi) {
                $existingStart = strtotime($existingEmploi->heure_debut);
                $existingEnd = strtotime($existingEmploi->heure_fin);
                $newStart = strtotime($heureDebut);
                $newEnd = strtotime($heureFin);

                if (($newStart >= $existingStart && $newStart < $existingEnd) ||
                    ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
                    ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
                    return redirect()->back()->with('error_message', 'Un chevauchement horaire existe pour ' . $jour . ' entre ' . $heureDebut . ' et ' . $heureFin . '.');
                }
            }

            EmploiTemps::create([
                'classe_id' => $classeId,
                'matiere_id' => $matiereId,
                'annee_academique_id' => $anneeAcademiqueId,
                'jour' => $jour,
                'heure_debut' => $heureDebut,
                'heure_fin' => $heureFin,
            ]);
        }
    }

    return redirect()->route('emploi_temps.edit', [
        'classeId' => $classeId,
        'anneeAcademiqueId' => $anneeAcademiqueId
    ])->with('success', 'Emploi du temps modifié avec succès.');
}

    // public function verify(Request $request)
    // {
    //     $request->validate(['educ_master' => 'required|string|max:20']);

    //     $eleve = Eleve::where('matricule_educ_master', $request->educ_master)->first();

    //     if (!$eleve) {
    //         return response()->json(['error' => 'Numéro Educ Master invalide.']);
    //     }

    //     $inscription = Inscription::where('eleve_id', $eleve->id)->latest()->first();
    //     if (!$inscription) {
    //         return response()->json(['error' => 'Aucune inscription trouvée pour cet élève.']);
    //     }

    //     $classeId = $inscription->classe_id;
    //     $anneeAcademiqueId = $inscription->annee_academique_id;

    //     Log::info('Classe ID: ' . $classeId . ', AnneeAcademique ID: ' . $anneeAcademiqueId); // Journalisation

    //     // Vérifier les IDs
    //     if (!$classeId || !$anneeAcademiqueId) {
    //         Log::error('IDs invalides: classeId=' . $classeId . ', anneeAcademiqueId=' . $anneeAcademiqueId);
    //         return response()->json(['error' => 'Données d\'inscription invalides.']);
    //     }

    //     // Générer la route
    //     $redirectUrl = route('emploi_temps.hebdomadaire', ['classeId' => $classeId, 'anneeAcademiqueId' => $anneeAcademiqueId]);
    //     Log::info('Route générée: ' . $redirectUrl);

    //     return response()->json(['redirect' => $redirectUrl]);
    // }
}
