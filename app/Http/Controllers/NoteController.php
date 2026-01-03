<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Note;
use App\Models\Annee_academique;
use App\Models\Trimestre;
use App\Models\Coefficient;
use App\Models\Inscription;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Mail\NotesEleveMailable;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Etablissement;
use App\Models\Conduite;

class NoteController extends Controller
{
    public function create($eleve_id, $annee_academique_id)
    {
        $eleve = Eleve::findOrFail($eleve_id);
        $annee_academique = Annee_academique::findOrFail($annee_academique_id);
        $trimestres = trimestre::where('annee_academique_id', $annee_academique_id)->get();
        $classe = $eleve->classe;

        // Rechercher l'inscription de l'élève pour l'année académique
        $inscription = Inscription::where('eleve_id', $eleve_id)->where('annee_academique_id', $annee_academique_id)->first();

        if (!$inscription) {
            return redirect()
                ->back()
                ->withErrors([
                    'error' => 'Aucune classe trouvée pour cet élève pour cette année académique.',
                ]);
        }

        // Récupérer les matières associées à la classe
        $classe = Classe::findOrFail($inscription->classe_id);
        // $matieres = Matiere::where('classe_id', $classe->id)->get();

        $notesExistantes = note::where('eleve_id', $eleve_id)->where('annee_academique_id', $annee_academique_id)->get()->groupBy('matiere_id', 'type_evaluation');

        // ajout conduite
        // $conduiteExistante = Conduite::where('eleve_id', $eleve_id)
        // ->where('annee_academique_id', $annee_academique_id)
        // ->where('trimestre_id', $trimestres->first()->id ?? null)
        // ->first();

        // Vérifier si l'utilisateur est admin
        if (auth()->user()->role == 'Admin') {
            $matieres = Matiere::all(); // L'admin voit toutes les matières
        } else {
            // Récupérer l'enseignant connecté
            $enseignant = Enseignant::where('user_id', auth()->user()->id)->first();

            // Vérifier si l'enseignant existe et récupérer sa matière
            if ($enseignant) {
                $matieres = Matiere::where('id', $enseignant->matiere_id)->get();
            } else {
                $matieres = collect(); // Si l'utilisateur n'est pas un enseignant, il ne voit aucune matière
            }
        }

        return view('note.create', compact('eleve', 'matieres', 'annee_academique', 'notesExistantes', 'trimestres', 'classe'));
    }

    public function verifyCoefficients($classeId)
    {
        // Récupérer toutes les matières liées à la classe via la table coefficients
        $coefficients = Coefficient::where('classe_id', $classeId)->get();

        // Vérifier si chaque coefficient a une valeur définie
        foreach ($coefficients as $coefficient) {
            if (is_null($coefficient->valeur_coefficient) || $coefficient->valeur_coefficient === 0) {
                return false; // Un coefficient n'est pas défini ou est nul
            }
        }

        // Vérifier s'il y a des matières sans coefficient pour cette classe
        $matieresSansCoefficient = Matiere::whereDoesntHave('coefficients', function ($query) use ($classeId) {
            $query->where('classe_id', $classeId);
        })->exists();

        if ($matieresSansCoefficient) {
            return false; // Une matière n'a pas de coefficient
        }

        return true; // Tous les coefficients sont définis pour toutes les matières de la classe
    }

    public function store(Request $request, $eleve_id, $annee_academique_id)
    {
        $user = auth()->user();
        $trimestre = Trimestre::find($request->trimestre_id);
        $dateActuelle = now();

        // Vérification des dates de début et de fin du trimestre
        if ($dateActuelle < $trimestre->date_debut) {
            return redirect()
                ->back()
                ->with(['error_message' => 'La saisie des notes n\'a pas encore commencé pour ce trimestre.']);
        }

        if ($dateActuelle > $trimestre->date_fin) {
            return redirect()
                ->back()
                ->with(['error_message' => 'La saisie des notes est fermée pour ce trimestre.']);
        }

        $classe_id = $request->input('classe_id');

        // Vérifier que toutes les matières de la classe ont des coefficients
        // if (!$this->verifyCoefficients($classe_id)) {
        //     return redirect()->back()->with('error_message', 'Toutes les matières des classes doivent avoir un coefficient avant d\'insérer des notes.');
        // }

        // Insérer ou mettre à jour les notes
        $notesData = $request->input('notes', []);
        $trimestre_id = $request->input('trimestre_id');

        foreach ($notesData as $matiere_id => $evaluations) {
            foreach ($evaluations as $type_evaluation => $valeur) {
                // Vérifier si l'enseignant peut noter cette matière
                // if ($user->role === 'enseignant') {
                //     $enseignantEnseigneMatiere = Matiere::whereHas('enseignants', function ($query) use ($user, $matiere_id) {
                //         $query->where('enseignant_id', $user->id)->where('id', $matiere_id);
                //     })->exists();

                //     if (!$enseignantEnseigneMatiere) {
                //         return redirect()->back()->with('error_message', 'Vous ne pouvez noter que vos matières.');
                //     }
                // }

                if (!is_null($valeur)) {
                    Note::updateOrCreate(
                        [
                            'eleve_id' => $eleve_id,
                            'annee_academique_id' => $annee_academique_id,
                            'matiere_id' => $matiere_id,
                            'trimestre_id' => $trimestre_id,
                            'classe_id' => $classe_id,
                            'type_evaluation' => $type_evaluation,
                        ],
                        [
                            'valeur_note' => $valeur,
                        ],
                    );
                }
            }
        }

        // Gestion de la note de conduite
    $conduiteNote = $request->input('conduite');
    if (!is_null($conduiteNote)) {
        Conduite::updateOrCreate(
            [
                'eleve_id' => $eleve_id,
                'annee_academique_id' => $annee_academique_id,
                'trimestre_id' => $trimestre_id,
                'classe_id' => $classe_id,
            ],
            ['valeur_note' => $conduiteNote],
        );
    }


        return redirect()
            ->route('note.index', [$eleve_id, $annee_academique_id])
            ->with('success_message', 'Notes enregistrées avec succès. Un mail a été envoyé aux parents');
    }


    public function saisieCollective($classe_id, $annee_academique_id)
{
    $user = auth()->user();
    $classe = Classe::findOrFail($classe_id);
    $annee_academique = Annee_Academique::findOrFail($annee_academique_id);

    // Récupérer les trimestres de l'année académique
    $trimestres = Trimestre::where('annee_academique_id', $annee_academique_id)
        ->orderBy('date_debut')
        ->get();

    // Récupérer les matières selon le rôle de l'utilisateur
    if ($user->role === 'enseignant') {

    // Récupérer l'enseignant lié à cet utilisateur
    $enseignant = Enseignant::where('user_id', $user->id)->first();

    if ($enseignant) {
        // L'enseignant voit uniquement sa matière pour cette classe
        $matieres = Matiere::where('id', $enseignant->matiere_id)
                            ->whereHas('classes', function ($query) use ($classe_id) {
                                $query->where('classe_id', $classe_id);
                            })
                            ->get();
    } else {
        $matieres = collect(); // Aucun résultat si pas trouvé
    }

} else {

    // L'admin voit toutes les matières de la classe
    $matieres = $classe->matieres;

}

    // Récupérer tous les élèves de la classe pour cette année académique
    $eleves = Eleve::whereHas('inscriptions', function ($query) use ($classe_id, $annee_academique_id) {
        $query->where('classe_id', $classe_id)
              ->where('annee_academique_id', $annee_academique_id);
    })
    ->orderBy('nom')
    ->orderBy('prenom')
    ->get();

    return view('note.note_generale', compact(
        'classe',
        'annee_academique',
        'trimestres',
        'matieres',
        'eleves'
    ));
}

/**
 * Enregistrer les notes collectivement
 */
public function storeCollective(Request $request)
{
    $user = auth()->user();
    $trimestre = Trimestre::findOrFail($request->trimestre_id);
    $dateActuelle = now();

    // Vérification des dates de saisie
    if ($dateActuelle < $trimestre->date_debut) {
        return redirect()->back()
            ->with(['error_message' => '⚠️ La saisie des notes n\'a pas encore commencé pour ce trimestre.']);
    }

    if ($dateActuelle > $trimestre->date_fin) {
        return redirect()->back()
            ->with(['error_message' => '⚠️ La saisie des notes est fermée pour ce trimestre.']);
    }

    $matiere_id = $request->input('matiere_id');
    $type_evaluation = $request->input('type_evaluation');
    $trimestre_id = $request->input('trimestre_id');
    $classe_id = $request->input('classe_id');
    $annee_academique_id = $request->input('annee_academique_id');
    $notes = $request->input('notes', []);

    // Vérification des permissions pour les enseignants
    $enseignant = Enseignant::where('user_id', $user->id)->first();
    if ($user->role === 'enseignant') {
        $enseignantEnseigneMatiere = Matiere::where('id', $enseignant->matiere_id)
                            ->whereHas('classes', function ($query) use ($classe_id) {
                                $query->where('classe_id', $classe_id);
                            })
                            ->get();

        if (!$enseignantEnseigneMatiere) {
            return redirect()->back()
                ->with('error_message', '❌ Vous ne pouvez noter que vos matières assignées pour cette classe.');
        }
    }

    // Compteur de notes enregistrées
    $compteur = 0;

    // Insertion/Mise à jour des notes pour tous les élèves
    foreach ($notes as $eleve_id => $valeur) {
        if (!is_null($valeur) && $valeur !== '') {
            // Validation de la note
            if ($valeur < 0 || $valeur > 20) {
                continue; // Ignorer les notes invalides
            }

            Note::updateOrCreate(
                [
                    'eleve_id' => $eleve_id,
                    'annee_academique_id' => $annee_academique_id,
                    'matiere_id' => $matiere_id,
                    'trimestre_id' => $trimestre_id,
                    'classe_id' => $classe_id,
                    'type_evaluation' => $type_evaluation,
                ],
                [
                    'valeur_note' => $valeur,
                ]
            );

            $compteur++;
        }
    }

    if ($compteur === 0) {
        return redirect()->back()
            ->with('error_message', '⚠️ Aucune note valide n\'a été saisie.');
    }

    // Message de succès avec statistiques
    $matiere = Matiere::find($matiere_id);
    return redirect()->back()
        ->with('success_message', "✅ $compteur note(s) enregistrée(s) avec succès pour la matière {$matiere->nom} - {$type_evaluation}.");
}

/**
 * Récupérer les notes existantes via AJAX
 */
// public function getNotes(Request $request)
// {
//     $notes = Note::where('trimestre_id', $request->trimestre_id)
//         ->where('matiere_id', $request->matiere_id)
//         ->where('type_evaluation', $request->type_evaluation)
//         ->where('classe_id', $request->classe_id)
//         ->where('annee_academique_id', $request->annee_academique_id)
//         ->pluck('valeur_note', 'eleve_id');

//     return response()->json(['notes' => $notes]);
// }

public function getNotes(Request $request)
{
    try {
        // Log pour debug (optionnel)
        \Log::info('Requête getNotes', $request->all());

        $notes = Note::where('trimestre_id', $request->trimestre_id)
            ->where('matiere_id', $request->matiere_id)
            ->where('type_evaluation', $request->type_evaluation)
            ->where('classe_id', $request->classe_id)
            ->where('annee_academique_id', $request->annee_academique_id)
            ->pluck('valeur_note', 'eleve_id')
            ->toArray(); // Important : convertir en array

        // Log pour debug (optionnel)
        \Log::info('Notes trouvées', ['count' => count($notes)]);

        return response()->json([
            'success' => true,
            'notes' => $notes,
            'count' => count($notes)
        ]);

    } catch (\Exception $e) {
        \Log::error('Erreur getNotes: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur: ' . $e->getMessage()
        ], 500);
    }
}

    // public function verifyCoefficients($classeId)
    // {
    //     $classe = Classe::with('matieres')->findOrFail($classeId);

    //     foreach ($classe->matieres as $matiere) {
    //         $hasCoefficient = Coefficient::where('classe_id', $classe->id)
    //             ->where('matiere_id', $matiere->id)
    //             ->exists();

    //         if (!$hasCoefficient) {
    //             return false; // Une matière n'a pas de coefficient
    //         }
    //     }

    //     return true; // Toutes les matières ont des coefficients
    // }

    // public function store(Request $request, $eleve_id, $annee_academique_id)
    // {
    //     $trimestre = Trimestre::find($request->trimestre_id);
    //     $classe = classe::find($request->classe_id);
    //     $dateActuelle = now();
    //     if ($dateActuelle < $trimestre->date_debut) {
    //         return redirect()
    //             ->back()
    //             ->with(['error_message' => 'La saisie des notes n\'a pas encore commencé pour ce trimestre.']);
    //     }

    //     if ($dateActuelle > $trimestre->date_fin) {
    //         return redirect()
    //             ->back()
    //             ->with(['error_message' => 'La saisie des notes est fermée pour ce trimestre.']);
    //     }

    //     // Vérifier les coefficients
    //     // if (!$this->checkAllCoefficientsDefined($classe)) {
    //     //     return redirect()
    //     //         ->back()
    //     //         ->with(['error_message' => 'Tous les coefficients ne sont pas définis pour cette classe. Veuillez d\'abord définir les coefficients.']);
    //     // }

    //     // if (now()->greaterThan($trimestre->date_fin)) {
    //     //     return redirect()->back()->with(['error_message' => 'Ce trimestre est clôturé.']);
    //     // }

    //     $classe_id = $request->input('classe_id');
    //     // Vérifier les coefficients
    //     if (!$this->verifyCoefficients($classe_id)) {
    //         return redirect()->back()->with('error_message', 'Toutes les matières de cette classe doivent avoir un coefficient avant d\'insérer des notes.');
    //     }

    //     $notesData = $request->input('notes', []);
    //     $trimestre_id = $request->input('trimestre_id');

    //     foreach ($notesData as $matiere_id => $evaluations) {
    //         foreach ($evaluations as $type_evaluation => $valeur) {
    //             if (!is_null($valeur)) {
    //                 Note::updateOrCreate(
    //                     [
    //                         'eleve_id' => $eleve_id,
    //                         'annee_academique_id' => $annee_academique_id,
    //                         'matiere_id' => $matiere_id,
    //                         'trimestre_id' => $trimestre_id,
    //                         'classe_id' => $classe_id,
    //                         'type_evaluation' => $type_evaluation,
    //                     ],
    //                     [
    //                         'valeur_note' => $valeur,
    //                     ],
    //                 );
    //             }
    //         }
    //     }

    //     return redirect()
    //         ->route('note.index', [$eleve_id, $annee_academique_id])
    //         ->with('success_message', 'Notes enregistrées avec succès.');
    // }

    public function index($eleve_id, $annee_academique_id)
    {
        $eleve = Eleve::findOrFail($eleve_id);
        $annee_academique = Annee_academique::findOrFail($annee_academique_id);
        $annees = Annee_academique::all();
        if (auth()->user()->role == 'Admin') {
            // ✅ L'admin voit toutes les notes
            $notes = Note::where('eleve_id', $eleve_id)->where('annee_academique_id', $annee_academique_id)->with('matiere', 'trimestre')->get();
        } else {
            // ✅ Si l'utilisateur est un enseignant, il ne voit que ses propres notes
            $enseignant = Enseignant::where('user_id', auth()->user()->id)->first();

            if ($enseignant) {
                $notes = Note::where('eleve_id', $eleve_id)
                    ->where('annee_academique_id', $annee_academique_id)
                    ->whereHas('matiere', function ($query) use ($enseignant) {
                        $query->where('id', $enseignant->matiere_id);
                    })
                    ->with('matiere', 'trimestre')
                    ->get();
            } else {
                $notes = collect(); // Aucun accès aux notes
            }
        }

        // Regrouper les notes par matière pour l'affichage
        $notesGroupées = $notes->groupBy(function ($note) {
            return $note->matiere->nom;
        });
        return view('note.index', compact('eleve', 'annee_academique', 'notesGroupées', 'annees'));
    }

    // public function rechercher(Request $request)
    // {
    //     $eleveId = $request->input('eleve_id'); // Assurez-vous que cet ID est fourni
    //     $annee_academique_id = $request->input('annee_academique_id');

    //     $eleve = Eleve::findOrFail($eleveId);
    //     $annees = Annee_academique::all();
    //     $annee_academique = Annee_academique::findOrFail($annee_academique_id);
    //     $notes = [];
    //     if ($annee_academique_id) {
    //         $anneeAcademique = Annee_academique::findOrFail($annee_academique_id);
    //         $notes = Note::where('eleve_id', $eleveId)->where('annee_academique_id', $annee_academique_id)->get()->groupBy('matiere.nom');
    //     }

    //     return view('note.voir', compact('eleve', 'annees', 'notes', 'annee_academique'));
    // }

    // public function voir()
    // {
    //     $eleve_id = $request->input('eleve_id'); // Assurez-vous que cet ID est fourni
    //     $annee_academique_id = $request->input('annee_academique_id');

    //     $eleve = Eleve::findOrFail($eleve_id);
    //     $annee_academique = Annee_academique::findOrFail($annee_academique_id);
    //     $annees = Annee_academique::all();
    //     // $trimestres = Trimestre::where('annee_academique_id', $annee_academique_id)->get();
    //     $notes = note::where('eleve_id', $eleve_id)->where('annee_academique_id', $annee_academique_id)->with('matiere', 'trimestre')->get()->groupBy('matiere.nom');
    //     return view('note.voir', compact('eleve', 'annee_academique', 'notes', 'annees'));
    // }

    // public function voir()
    // {
    //     // Affiche simplement le formulaire de recherche avec les années académiques disponibles
    //     $eleve = Eleve::findOrFail($eleve_id);
    //     $annees = Annee_academique::all();
    //     return view('note.voir', compact('annees','eleve')); // Aucun élève ou note par défaut
    // }

    public function voir($eleve_id)
    {
        $eleve = Eleve::findOrFail($eleve_id); // Trouve l'élève à partir de l'ID
        $annees = Annee_academique::all(); // Récupère toutes les années académiques
        return view('note.voir', compact('annees', 'eleve')); // Envoie les données à la vue
    }

    // public function rechercher(Request $request)
    // {
    //     $request->validate([
    //         'eleve_id' => 'required|exists:eleves,id', // Valide que l'élève existe
    //         'annee_academique_id' => 'nullable|exists:annee_academiques,id', // Année académique optionnelle
    //     ]);

    //     $eleveId = $request->input('eleve_id');
    //     $annee_academique_id = $request->input('annee_academique_id');

    //     // Charger l'élève et ses notes filtrées
    //     $eleve = Eleve::findOrFail($eleveId);
    //     $annees = Annee_academique::all();
    //     $notes = [];

    //     if ($annee_academique_id) {
    //         $notes = Note::where('eleve_id', $eleveId)
    //             ->where('annee_academique_id', $annee_academique_id)
    //             ->with('matiere', 'trimestre') // Charger les relations
    //             ->get()
    //             ->groupBy('matiere.nom');
    //     }

    //     $annee_academique = $annee_academique_id ? Annee_academique::findOrFail($annee_academique_id) : null;

    //     return view('note.voir', compact('eleve', 'annees', 'notes', 'annee_academique'));
    // }

    public function rechercher(Request $request)
    {
        $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'annee_academique_id' => 'nullable|exists:annee_academiques,id',
        ]);

        $eleveId = $request->input('eleve_id');
        $annee_academique_id = $request->input('annee_academique_id');

        $eleve = Eleve::findOrFail($eleveId);
        $annees = Annee_academique::all();
        $notes = [];
        $message = null;

        $user = auth()->user(); // Récupérer l'utilisateur connecté

        if ($annee_academique_id) {
            $query = Note::where('eleve_id', $eleveId)->where('annee_academique_id', $annee_academique_id)->with('matiere', 'trimestre');

            if ($user->role === 'enseignant') {
                // Récupérer l'enregistrement enseignant lié à cet utilisateur
                $enseignant = Enseignant::where('user_id', $user->id)->first();
                if ($enseignant) {
                    // Filtrer pour ne voir que les notes de sa matière
                    $query->where('matiere_id', $enseignant->matiere_id);
                } else {
                    // Si l'enregistrement enseignant n'est pas trouvé, aucune note ne sera retournée
                    $query->whereRaw('1 = 0');
                }
            }

            $notes = $query->get()->groupBy('matiere.nom');

            if ($notes->isEmpty()) {
                $message = "Pas de notes pour l'élève pour l'année académique sélectionnée.";
            }
        }

        $annee_academique = $annee_academique_id ? Annee_academique::findOrFail($annee_academique_id) : null;

        return view('note.voir', compact('eleve', 'annees', 'notes', 'annee_academique', 'message'));
    }

    public function update($eleve_id, $matiere_id)
    {
        $eleve = Eleve::findOrFail($eleve_id);
        $matiere = Matiere::findOrFail($matiere_id);
        $note = Note::where('eleve_id', $eleve_id)->where('matiere_id', $matiere_id)->get();
        return view('note.edit', compact('eleve', 'matiere', 'notes'));
    }

    // Dans NoteController.php

/**
 * Afficher toutes les notes de la classe
 */
public function voirClasse($classe_id, $annee_academique_id)
{
    $classe = Classe::findOrFail($classe_id);
    $annee_academique = Annee_academique::findOrFail($annee_academique_id);
    $trimestres = Trimestre::where('annee_academique_id', $annee_academique_id)->get();

    // Récupérer tous les élèves de la classe
    $eleves = Eleve::whereHas('inscriptions', function ($query) use ($classe_id, $annee_academique_id) {
        $query->where('classe_id', $classe_id)
              ->where('annee_academique_id', $annee_academique_id);
    })->orderBy('nom')->orderBy('prenom')->get();

    return view('note.voir_classe', compact('classe', 'annee_academique', 'trimestres', 'eleves'));
}

/**
 * Rechercher et afficher les notes selon les filtres
 */
public function rechercherClasse(Request $request)
{
    $request->validate([
        'classe_id' => 'required|exists:classes,id',
        'annee_academique_id' => 'required|exists:annee_academiques,id',
        'trimestre_id' => 'nullable|exists:trimestres,id',
        'matiere_id' => 'nullable|exists:matieres,id',
    ]);

    $classe_id = $request->input('classe_id');
    $annee_academique_id = $request->input('annee_academique_id');
    $trimestre_id = $request->input('trimestre_id');
    $matiere_id = $request->input('matiere_id');

    $classe = Classe::findOrFail($classe_id);
    $annee_academique = Annee_academique::findOrFail($annee_academique_id);
    $trimestres = Trimestre::where('annee_academique_id', $annee_academique_id)->get();

    // Récupérer les élèves
    $eleves = Eleve::whereHas('inscriptions', function ($query) use ($classe_id, $annee_academique_id) {
        $query->where('classe_id', $classe_id)
              ->where('annee_academique_id', $annee_academique_id);
    })->orderBy('nom')->orderBy('prenom')->get();

    $user = auth()->user();

    // Construction de la requête pour les notes
    $query = Note::where('classe_id', $classe_id)
        ->where('annee_academique_id', $annee_academique_id)
        ->with(['eleve', 'matiere', 'trimestre']);

    // Filtrer par trimestre si spécifié
    if ($trimestre_id) {
        $query->where('trimestre_id', $trimestre_id);
    }

    // Filtrer par matière si spécifié
    if ($matiere_id) {
        $query->where('matiere_id', $matiere_id);
    }

    // Si enseignant, ne voir que ses matières
    if ($user->role === 'enseignant') {
        $matiereIds = Matiere::whereHas('enseignants', function ($q) use ($user, $classe_id, $annee_academique_id) {
            $q->where('enseignant_id', $user->id)
              ->where('classe_id', $classe_id)
              ->where('annee_academique_id', $annee_academique_id);
        })->pluck('id');

        $query->whereIn('matiere_id', $matiereIds);
    }

    $notes = $query->get();

    // Récupérer les matières pour le filtre
    if ($user->role === 'enseignant') {
        $matieres = Matiere::whereHas('enseignants', function ($q) use ($user, $classe_id, $annee_academique_id) {
            $q->where('enseignant_id', $user->id)
              ->where('classe_id', $classe_id)
              ->where('annee_academique_id', $annee_academique_id);
        })->get();
    } else {
        $matieres = $classe->matieres;
    }

    $message = $notes->isEmpty() ? 'Aucune note trouvée pour ces critères.' : null;

    return view('note.voir_classe', compact(
        'classe',
        'annee_academique',
        'trimestres',
        'eleves',
        'notes',
        'matieres',
        'message',
        'trimestre_id',
        'matiere_id'
    ));
}

/**
 * Afficher le formulaire de modification d'une note
 */
public function editNote($note_id)
{
    $note = Note::with(['eleve', 'matiere', 'trimestre', 'classe', 'annee_Academique'])->findOrFail($note_id);

    // Vérifier les permissions pour les enseignants
    $user = auth()->user();
    if ($user->role === 'enseignant') {
        $enseigneMatiere = Matiere::whereHas('enseignants', function ($query) use ($user, $note) {
            $query->where('enseignant_id', $user->id)
                  ->where('id', $note->matiere_id);
        })->exists();

        if (!$enseigneMatiere) {
            return redirect()->back()->with('error_message', 'Vous ne pouvez modifier que vos matières.');
        }
    }

    return view('note.edit_note', compact('note'));
}

/**
 * Mettre à jour une note individuelle
 */
public function updateNote(Request $request, $note_id)
{
    $request->validate([
        'valeur_note' => 'required|numeric|min:0|max:20',
    ]);

    $note = Note::findOrFail($note_id);

    // Vérifier les permissions
    $user = auth()->user();
    if ($user->role === 'enseignant') {
        $enseigneMatiere = Matiere::whereHas('enseignants', function ($query) use ($user, $note) {
            $query->where('enseignant_id', $user->id)
                  ->where('id', $note->matiere_id);
        })->exists();

        if (!$enseigneMatiere) {
            return redirect()->back()->with('error_message', 'Vous ne pouvez modifier que vos matières.');
        }
    }

    // Vérifier les dates du trimestre
    $trimestre = $note->trimestre;
    $dateActuelle = now();

    if ($dateActuelle < $trimestre->date_debut || $dateActuelle > $trimestre->date_fin) {
        return redirect()->back()
            ->with('error_message', 'La modification des notes est fermée pour ce trimestre.');
    }

    $note->valeur_note = $request->valeur_note;
    $note->save();

    return redirect()->route('note.voir_classe', [
        'classe_id' => $note->classe_id,
        'annee_academique_id' => $note->annee_academique_id
    ])->with('success_message', 'Note modifiée avec succès.');
}

    // public function calculerMoyennes($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with('classe', 'eleve')->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();

    //     $classeId = $inscription->classe->id;

    //     $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();

    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();

    //     $moyennesParTrimestre = [];
    //     $moyennesGenerales = [];
    //     $moyennesInterrogations = [];
    //     $totaux = []; // Pour stocker les sommes

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('eleve_id', $eleveId)
    //             ->where('trimestre_id', $trimestre->id)
    //             ->with('matiere')
    //             ->get();

    //         $totalPoints = 0;
    //         $totalCoefficients = 0;
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);

    //             // Calcul des moyennes des interrogations
    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->sum('valeur_note') / $notesInterrogations->count() : null;

    //             // Calcul des moyennes des devoirs
    //             $devoir1 = $notesMatiere->where('type_evaluation', 'Devoir 1')->first();
    //             $devoir2 = $notesMatiere->where('type_evaluation', 'Devoir 2')->first();

    //             // Calcul de la moyenne finale pour la matière
    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $devoir1 !== null && $devoir2 !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $devoir1->valeur_note + $devoir2->valeur_note) / 3;
    //             }

    //             $moyennesParTrimestre[$trimestre->id][$matiere->nom] = $moyenneFinale;
    //             $moyennesInterrogations[$trimestre->id][$matiere->nom] = $moyenneInterrogations;

    //             if ($moyenneFinale !== null) {
    //                 $totalPoints += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
    //             }
    //         }

    //         $moyennesGenerales[$trimestre->id] = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
    //         $totaux[$trimestre->id] = [
    //             'somme_coefficients' => $totalCoefficients,
    //             'somme_moyennes' => $sommeMoyennes,
    //             'somme_moyennes_coefficientees' => $sommeMoyennesCoefficientees,
    //         ];
    //     }

    //     return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'moyennesParTrimestre', 'moyennesGenerales', 'moyennesInterrogations', 'coefficients', 'totaux'));
    // }

    // public function determinerMention($moyenne)
    // {
    //     if ($moyenne >= 16) {
    //         return 'Très Bien';
    //     } elseif ($moyenne >= 14) {
    //         return 'Bien';
    //     } elseif ($moyenne >= 12) {
    //         return 'Assez Bien';
    //     } elseif ($moyenne >= 10) {
    //         return 'Passable';
    //     } else {
    //         return 'Insuffisant';
    //     }
    // }

    // public function calculerMoyennes($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with('classe', 'eleve')->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
    //     $classeId = $inscription->classe->id;
    //     $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();

    //     $moyennesParTrimestre = [];
    //     $moyennesGenerales = [];
    //     $moyennesInterrogations = [];
    //     $rangsMatiere = [];
    //     $rangsTrimestriels = [];
    //     $mentions = [];
    //     $totaux = [];

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();

    //         $totalPoints = 0;
    //         $totalCoefficients = 0;
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;
    //         $hasAllNotes = true;

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);
    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);

    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->sum('valeur_note') / $notesInterrogations->count() : null;
    //             $devoir1 = $notesMatiere->where('type_evaluation', 'Devoir 1')->first();
    //             $devoir2 = $notesMatiere->where('type_evaluation', 'Devoir 2')->first();

    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $devoir1 !== null && $devoir2 !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $devoir1->valeur_note + $devoir2->valeur_note) / 3;
    //             } else {
    //                 $hasAllNotes = false;
    //             }

    //             $moyennesParTrimestre[$trimestre->id][$matiere->nom] = $moyenneFinale;
    //             $moyennesInterrogations[$trimestre->id][$matiere->nom] = $moyenneInterrogations;

    //             if ($moyenneFinale !== null) {
    //                 $totalPoints += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;

    //                 // Ajout du rang de l'élève pour chaque matière dans sa classe
    //                 $rangsMatiere[$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id);

    //                 // Ajout de la mention
    //                 $mentions[$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
    //             }
    //         }

    //         if ($hasAllNotes) {
    //             $moyennesGenerales[$trimestre->id] = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
    //             $totaux[$trimestre->id] = [
    //                 'somme_coefficients' => $totalCoefficients,
    //                 'somme_moyennes' => $sommeMoyennes,
    //                 'somme_moyennes_coefficientees' => $sommeMoyennesCoefficientees,
    //             ];

    //             // Calcul du rang trimestriel pour chaque élève
    //             $rangsTrimestriels[$trimestre->id] = $this->calculerRangTrimestriel($classeId, $eleveId, $trimestre->id);
    //         } else {
    //             $moyennesGenerales[$trimestre->id] = null;
    //             $totaux[$trimestre->id] = [
    //                 'somme_coefficients' => 0,
    //                 'somme_moyennes' => 0,
    //                 'somme_moyennes_coefficientees' => 0,
    //             ];
    //             $rangsTrimestriels[$trimestre->id] = 'N/A';
    //         }
    //     }

    //     return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'moyennesParTrimestre', 'moyennesGenerales', 'moyennesInterrogations', 'coefficients', 'totaux', 'rangsMatiere', 'mentions', 'rangsTrimestriels'));
    // }

    // public function calculerRangMatiereClasse($classeId, $matiereId, $trimestreId)
    // {
    //     $notesMatiereClasse = Note::where('classe_id', $classeId)->where('matiere_id', $matiereId)->where('trimestre_id', $trimestreId)->with('eleve')->get();

    //     // Calculer les moyennes des élèves pour la matière spécifiée
    //     $moyennes = [];
    //     foreach ($notesMatiereClasse as $note) {
    //         if (!isset($moyennes[$note->eleve->id])) {
    //             $moyennes[$note->eleve->id] = 0;
    //         }
    //         $moyennes[$note->eleve->id] += $note->valeur_note;
    //     }

    //     // Trier les moyennes par ordre décroissant
    //     arsort($moyennes);

    //     // Attribuer les rangs en fonction des moyennes
    //     $rang = 1;
    //     $rangs = [];
    //     foreach ($moyennes as $eleveId => $moyenne) {
    //         $rangs[$eleveId] = $rang++;
    //     }

    //     return $rangs;
    // }

    // public function calculerRangTrimestriel($classeId, $eleveId, $trimestreId)
    // {
    //     // Récupérer tous les élèves de la classe
    //     $elevesClasse = Eleve::whereHas('inscriptions', function ($query) use ($classeId) {
    //         $query->where('classe_id', $classeId);
    //     })->get();

    //     // Calculer les moyennes générales pour chaque élève de la classe pour le trimestre spécifié
    //     $moyennes = [];
    //     foreach ($elevesClasse as $eleve) {
    //         $moyenne = $this->calculerMoyenneGeneralePourTrimestre($eleve->id, $trimestreId);
    //         if ($moyenne !== null) {
    //             $moyennes[$eleve->id] = $moyenne;
    //         }
    //     }

    //     // Trier les moyennes par ordre décroissant pour déterminer les rangs
    //     arsort($moyennes);

    //     // Attribuer les rangs en fonction des moyennes
    //     $rang = 1;
    //     $rangs = [];
    //     foreach ($moyennes as $id => $moyenne) {
    //         $rangs[$id] = $rang++;
    //     }

    //     // Vérification du résultat du rang pour l'élève spécifié
    //     return isset($rangs[$eleveId]) ? $rangs[$eleveId] : 'N/A';
    // }

    // public function calculerMoyenneGeneralePourTrimestre($eleveId, $trimestreId)
    // {
    //     // Récupérer toutes les notes de l'élève pour le trimestre spécifié
    //     $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestreId)->with('matiere')->get();

    //     if ($notes->isEmpty()) {
    //         return null;
    //     }

    //     $totalPoints = 0;
    //     $totalCoefficients = 0;

    //     foreach ($notes as $note) {
    //         $coefficient = $note->matiere->coefficient;
    //         $totalPoints += $note->valeur_note * $coefficient;
    //         $totalCoefficients += $coefficient;
    //     }

    //     return $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
    // }

    // public function calculerMoyenneGeneralePourTrimestre($eleveId, $trimestreId)
    // {
    //     // Récupérer toutes les notes de l'élève pour le trimestre spécifié
    //     $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestreId)->with('matiere')->get();

    //     if ($notes->isEmpty()) {
    //         return null;
    //     }

    //     $totalPoints = 0;
    //     $totalCoefficients = 0;

    //     foreach ($notes as $note) {
    //         $coefficient = $note->matiere->coefficient;
    //         $totalPoints += $note->valeur_note * $coefficient;
    //         $totalCoefficients += $coefficient;
    //     }

    //     return $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
    // }

    // public function calculerMoyenneGeneralePourTrimestre($eleveId, $trimestreId)
    // {
    //     // Récupérer toutes les notes de l'élève pour le trimestre spécifié
    //     $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestreId)->with('matiere')->get();

    //     if ($notes->isEmpty()) {
    //         return null;
    //     }

    //     $totalPoints = 0;
    //     $totalCoefficients = 0;

    //     foreach ($notes as $note) {
    //         $coefficient = $note->matiere->coefficient;
    //         $totalPoints += $note->valeur_note * $coefficient;
    //         $totalCoefficients += $coefficient;
    //     }

    //     return $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
    // }

    // public function calculerMoyenneGeneralePourTrimestre($eleveId, $trimestreId)
    // {
    //     // Récupérer toutes les notes de l'élève pour le trimestre spécifié
    //     $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestreId)->with('matiere')->get();

    //     if ($notes->isEmpty()) {
    //         return null;
    //     }

    //     $totalPoints = 0;
    //     $totalCoefficients = 0;

    //     foreach ($notes as $note) {
    //         $coefficient = $note->matiere->coefficient;
    //         $totalPoints += $note->valeur_note * $coefficient;
    //         $totalCoefficients += $coefficient;
    //     }

    //     return $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
    // }

    // public function calculerMoyennes($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with('classe', 'eleve')->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();

    //     $classeId = $inscription->classe->id;
    //     $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();

    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();

    //     $moyennesParTrimestre = [];
    //     $moyennesGenerales = [];
    //     $moyennesInterrogations = [];
    //     $totaux = []; // Pour stocker les sommes
    //     $rangsParTrimestre = [];
    //     $appreciations = [];

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('trimestre_id', $trimestre->id)
    //             ->whereIn('eleve_id', function ($query) use ($classeId) {
    //                 $query->select('eleve_id')->from('inscriptions')->where('classe_id', $classeId);
    //             })
    //             ->with('matiere')
    //             ->get();

    //         $totalPoints = 0;
    //         $totalCoefficients = 0;
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;
    //         $moyennesClasse = []; // Moyennes de tous les élèves pour calcul du rang

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);

    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->sum('valeur_note') / $notesInterrogations->count() : null;

    //             $devoir1 = $notesMatiere->where('type_evaluation', 'Devoir 1')->first();
    //             $devoir2 = $notesMatiere->where('type_evaluation', 'Devoir 2')->first();

    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $devoir1 !== null && $devoir2 !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $devoir1->valeur_note + $devoir2->valeur_note) / 3;
    //             }

    //             $moyenneCoefficientee = $moyenneFinale !== null ? $moyenneFinale * $coefficient->valeur_coefficient : null;

    //             $moyennesParTrimestre[$trimestre->id][$matiere->nom] = [
    //                 'moyenne_finale' => $moyenneFinale,
    //                 'moyenne_coeff' => $moyenneCoefficientee,
    //                 'moyenne_interrogations' => $moyenneInterrogations,
    //             ];

    //             if ($moyenneFinale !== null) {
    //                 $totalPoints += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneCoefficientee;
    //                 $moyennesClasse[] = $moyenneFinale;
    //             }
    //         }

    //         // Classement
    //         rsort($moyennesClasse);
    //         $rangsParTrimestre[$trimestre->id] = array_search($moyennesParTrimestre[$trimestre->id]['moyenne_finale'] ?? null, $moyennesClasse) + 1;

    //         $moyennesGenerales[$trimestre->id] = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;

    //         $totaux[$trimestre->id] = [
    //             'somme_coefficients' => $totalCoefficients,
    //             'somme_moyennes' => $sommeMoyennes,
    //             'somme_moyennes_coefficientees' => $sommeMoyennesCoefficientees,
    //         ];

    //         // Appréciation
    //         $appreciations[$trimestre->id] = $this->determinerAppreciation($moyennesGenerales[$trimestre->id]);
    //     }

    //     return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'moyennesParTrimestre', 'moyennesGenerales', 'moyennesInterrogations', 'coefficients', 'totaux', 'rangsParTrimestre', 'appreciations'));
    // }

    // Fonction pour déterminer l'appréciation
    // private function determinerAppreciation($moyenne)
    // {
    //     if ($moyenne === null) {
    //         return 'Non calculée';
    //     }

    //     if ($moyenne >= 16) {
    //         return 'Très Bien';
    //     } elseif ($moyenne >= 14) {
    //         return 'Bien';
    //     } elseif ($moyenne >= 12) {
    //         return 'Assez Bien';
    //     } elseif ($moyenne >= 10) {
    //         return 'Passable';
    //     }

    //     return 'Insuffisant';
    // }

    // public function envoyerNotes()
    // {
    //     $eleves = Eleve::with(['parent', 'notes.matiere'])->get(); // Assurez-vous que 'parent' est bien une relation

    //     foreach ($eleves as $eleve) {
    //         $notes = $eleve->notes;

    //         if ($eleve->parent && $eleve->parent->email) {
    //             Mail::to($eleve->parent->email)->send(new NotesEleveMailable($eleve, $notes));
    //         }
    //     }

    //     return back()->with('success', 'Les notes ont été envoyées aux parents avec succès.');
    // }

    // public function envoyerBulletin($eleve_id)
    // {
    //     $eleve = Eleve::with([
    //         'inscription.classe',
    //         'notes' => function ($query) {
    //             $query->where('envoye', false); // Récupérer uniquement les notes non envoyées
    //         },
    //     ])->findOrFail($eleve_id);

    //     // Vérifier s'il y a des notes à envoyer
    //     if ($eleve->notes->isEmpty()) {
    //         return back()->with('info', 'Aucune nouvelle note à envoyer.');
    //     }

    //     // Envoyer l'email
    //     Mail::to($eleve->email_parent)->send(new NotesEleveMail($eleve));

    //     // Mettre à jour les notes envoyées
    //     Note::where('eleve_id', $eleve->id)
    //         ->where('envoye', false)
    //         ->update(['envoye' => true]);

    //     return back()->with('success', 'Bulletin envoyé avec succès.');
    // }


    public function determinerMention($moyenne)
    {
        if ($moyenne >= 16) return 'Très Bien';
        if ($moyenne >= 14) return 'Bien';
        if ($moyenne >= 12) return 'Assez Bien';
        if ($moyenne >= 10) return 'Passable';
        return 'Insuffisant';
    }

    // public function calculerMoyennes($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with(['classe', 'eleve'])->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
    //     $classeId = $inscription->classe->id;
    //     $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();

    //     $data = [
    //         'moyennesParTrimestre' => [],
    //         'moyennesGenerales' => [],
    //         'moyennesInterrogations' => [],
    //         'moyennesDevoirs' => [],
    //         'notesDetaillees' => [],
    //         'rangsMatiere' => [],
    //         'rangsTrimestriels' => [],
    //         'mentions' => [],
    //         'totaux' => [],
    //     ];

    //     // Calculer rangs trimestriels pour toute la classe
    //     $elevesClasse = Inscription::where('classe_id', $classeId)->pluck('eleve_id');
    //     $rangsTrimestrielsParTrimestre = [];
    //     foreach ($trimestres as $trimestre) {
    //         $rangsTrimestrielsParTrimestre[$trimestre->id] = $this->calculerRangsTrimestrielsClasse($elevesClasse, $trimestre->id, $coefficients);
    //         $data['rangsTrimestriels'][$trimestre->id] = $rangsTrimestrielsParTrimestre[$trimestre->id][$eleveId] ?? 'N/A';
    //     }

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;
    //         $totalCoefficients = 0;
    //         $hasAllNotes = true;

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);

    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->avg('valeur_note') : null;

    //             $notesDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2']);
    //             $moyenneDevoirs = $notesDevoirs->isNotEmpty() ? $notesDevoirs->avg('valeur_note') : null;

    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
    //             } else {
    //                 $hasAllNotes = false;
    //             }

    //             $data['notesDetaillees'][$trimestre->id][$matiere->nom] = $notesMatiere->pluck('valeur_note', 'type_evaluation')->toArray();
    //             $data['moyennesInterrogations'][$trimestre->id][$matiere->nom] = $moyenneInterrogations ? number_format($moyenneInterrogations, 2) : 'N/A';
    //             $data['moyennesDevoirs'][$trimestre->id][$matiere->nom] = $moyenneDevoirs ? number_format($moyenneDevoirs, 2) : 'N/A';
    //             $data['moyennesParTrimestre'][$trimestre->id][$matiere->nom] = $moyenneFinale ? number_format($moyenneFinale, 2) : 'N/A';

    //             if ($moyenneFinale !== null) {
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;

    //                 $data['rangsMatiere'][$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id)[$eleveId] ?? 'N/A';
    //                 $data['mentions'][$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
    //             }
    //         }

    //         $data['moyennesGenerales'][$trimestre->id] = $totalCoefficients > 0 ? number_format($sommeMoyennesCoefficientees / $totalCoefficients, 2) : 'N/A';
    //         $data['totaux'][$trimestre->id] = [
    //             'somme_coefficients' => $totalCoefficients,
    //             'somme_moyennes' => number_format($sommeMoyennes, 2),
    //             'somme_moyennes_coefficientees' => number_format($sommeMoyennesCoefficientees, 2),
    //         ];
    //     }

    //     return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'coefficients') + $data);
    // }

    // public function calculerMoyennes($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with(['classe', 'eleve'])->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
    //     $classeId = $inscription->classe->id;
    //     $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();
    //     $etablissement = Etablissement::first(); // Récupère l'établissement

    //     $data = [
    //         'moyennesParTrimestre' => [],
    //         'moyennesGenerales' => [],
    //         'moyennesInterrogations' => [],
    //         'moyennesDevoirs' => [],
    //         'notesDetaillees' => [],
    //         'rangsMatiere' => [],
    //         'rangsTrimestriels' => [],
    //         'mentions' => [],
    //         'totaux' => [],
    //         'moyenneFaible' => [], // Ajout
    //         'moyenneForte' => [], // Ajout
    //         'moyenneClasse' => [], // Ajout
    //         'recompenses' => [], // Ajout
    //         'appreciationPrincipal' => [], // Ajout
    //         'bilanAnnuel' => null, // Ajout pour 3ème trimestre
    //         'moyenneGeneraleAnnuelle' => null, // Ajout
    //         'rangAnnuel' => null, // Ajout
    //         'decisionConseil' => 'Passe', // Ajout
    //         'etablissement' => $etablissement, // Ajout
    //     ];

    //     // Calculer rangs trimestriels pour toute la classe
    //     $elevesClasse = Inscription::where('classe_id', $classeId)->pluck('eleve_id');
    //     $rangsTrimestrielsParTrimestre = [];
    //     foreach ($trimestres as $trimestre) {
    //         $rangsTrimestrielsParTrimestre[$trimestre->id] = $this->calculerRangsTrimestrielsClasse($elevesClasse, $trimestre->id, $coefficients);
    //         $data['rangsTrimestriels'][$trimestre->id] = $rangsTrimestrielsParTrimestre[$trimestre->id][$eleveId] ?? 'N/A';

    //         // Calcul bilan classe (moy faible/forte/classe)
    //         $moyennesClasse = [];
    //         foreach ($elevesClasse as $eleveClasseId) {
    //             $moyClasse = $this->calculerMoyenneGeneralePourTrimestre($eleveClasseId, $trimestre->id);
    //             if ($moyClasse !== null) $moyennesClasse[] = $moyClasse;
    //         }
    //         $data['moyenneFaible'][$trimestre->id] = !empty($moyennesClasse) ? min($moyennesClasse) : 'N/A';
    //         $data['moyenneForte'][$trimestre->id] = !empty($moyennesClasse) ? max($moyennesClasse) : 'N/A';
    //         $data['moyenneClasse'][$trimestre->id] = !empty($moyennesClasse) ? round(array_sum($moyennesClasse) / count($moyennesClasse), 2) : 'N/A';

    //         // Récompenses/sanctions basées sur moyenne générale
    //         $moyGenerale = $data['moyennesGenerales'][$trimestre->id] ?? 0;
    //         $data['recompenses'][$trimestre->id] = [
    //             'felicitations' => $moyGenerale >= 16,
    //             'encouragements' => $moyGenerale >= 12 && $moyGenerale < 16,
    //             'tableau_honneur' => $moyGenerale >= 14,
    //             'avertissement' => $moyGenerale < 10,
    //             'blame' => $moyGenerale < 8,
    //         ];

    //         // Appréciation professeur principal (logique simple, adapte)
    //         $data['appreciationPrincipal'][$trimestre->id] = match(true) {
    //             $moyGenerale >= 16 => 'Excellent travail, continuez !',
    //             $moyGenerale >= 12 => 'Bon travail, effort à poursuivre.',
    //             $moyGenerale >= 10 => 'Satisfaisant, travail régulier.',
    //             default => 'Amélioration nécessaire en discipline et travail.',
    //         };
    //     }

    //     // Bilan annuel seulement pour 3ème trimestre
    //     $trimestre3 = $trimestres->where('nom', 'like', '%3ème%')->first();
    //     if ($trimestre3) {
    //         $moy1 = $data['moyennesGenerales'][1] ?? 0;
    //         $moy2 = $data['moyennesGenerales'][2] ?? 0;
    //         $moy3 = $data['moyennesGenerales'][3] ?? 0;
    //         $data['bilanAnnuel'] = round(($moy1 + $moy2 + $moy3) / 3, 2);
    //         $data['moyenneGeneraleAnnuelle'] = $data['bilanAnnuel'];
    //         $data['rangAnnuel'] = $this->calculerRangAnnuel($elevesClasse, $anneeAcademiqueId, $coefficients)[$eleveId] ?? 'N/A';
    //         $data['decisionConseil'] = $data['moyenneGeneraleAnnuelle'] >= 10 ? 'Passe' : 'Redouble';
    //     }

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;
    //         $totalCoefficients = 0;
    //         $hasAllNotes = true;

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);

    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->avg('valeur_note') : null;

    //             $notesDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2']);
    //             $moyenneDevoirs = $notesDevoirs->isNotEmpty() ? $notesDevoirs->avg('valeur_note') : null;

    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
    //             } else {
    //                 $hasAllNotes = false;
    //             }

    //             $data['notesDetaillees'][$trimestre->id][$matiere->nom] = $notesMatiere->pluck('valeur_note', 'type_evaluation')->toArray();
    //             $data['moyennesInterrogations'][$trimestre->id][$matiere->nom] = $moyenneInterrogations ? number_format($moyenneInterrogations, 2) : 'N/A';
    //             $data['moyennesDevoirs'][$trimestre->id][$matiere->nom] = $moyenneDevoirs ? number_format($moyenneDevoirs, 2) : 'N/A';
    //             $data['moyennesParTrimestre'][$trimestre->id][$matiere->nom] = $moyenneFinale ? number_format($moyenneFinale, 2) : 'N/A';

    //             if ($moyenneFinale !== null) {
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;

    //                 $data['rangsMatiere'][$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id)[$eleveId] ?? 'N/A';
    //                 $data['mentions'][$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
    //             }
    //         }

    //         $data['moyennesGenerales'][$trimestre->id] = $totalCoefficients > 0 ? number_format($sommeMoyennesCoefficientees / $totalCoefficients, 2) : 'N/A';
    //         $data['totaux'][$trimestre->id] = [
    //             'somme_coefficients' => $totalCoefficients,
    //             'somme_moyennes' => number_format($sommeMoyennes, 2),
    //             'somme_moyennes_coefficientees' => number_format($sommeMoyennesCoefficientees, 2),
    //         ];
    //     }

    //     return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'coefficients') + $data);
    // }

    public function calculerRangMatiereClasse($classeId, $matiereId, $trimestreId)
    {
        $notesMatiereClasse = Note::where('classe_id', $classeId)->where('matiere_id', $matiereId)->where('trimestre_id', $trimestreId)->get()->groupBy('eleve_id');

        $moyennes = [];
        foreach ($notesMatiereClasse as $eleveId => $notes) {
            $moyenneInterrogations = $notes->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3'])->avg('valeur_note');
            $moyenneDevoirs = $notes->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2'])->avg('valeur_note');
            $moyennes[$eleveId] = ($moyenneInterrogations + $moyenneDevoirs) / 2 ?? 0;
        }

        arsort($moyennes);
        $rang = 1;
        $rangs = [];
        foreach ($moyennes as $eleveId => $moyenne) {
            $rangs[$eleveId] = $rang++;
        }
        return $rangs;
    }

    protected function calculerRangsTrimestrielsClasse($elevesIds, $trimestreId, $coefficients)
    {
        $moyennes = [];
        foreach ($elevesIds as $eleveId) {
            $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestreId)->get()->groupBy('matiere_id');
            $sommePoints = 0;
            $totalCoefficients = 0;

            foreach ($coefficients as $coeff) {
                $notesMatiere = $notes[$coeff->matiere_id] ?? collect();
                $moyenneInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3'])->avg('valeur_note');
                $moyenneDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2'])->avg('valeur_note');
                $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2 ?? 0;

                $sommePoints += $moyenneFinale * $coeff->valeur_coefficient;
                $totalCoefficients += $coeff->valeur_coefficient;
            }

            $moyennes[$eleveId] = $totalCoefficients > 0 ? $sommePoints / $totalCoefficients : 0;
        }

        arsort($moyennes);
        $rang = 1;
        $rangs = [];
        foreach ($moyennes as $eleveId => $moyenne) {
            $rangs[$eleveId] = $rang++;
        }
        return $rangs;
    }

    public function exportPdf($eleveId, $anneeAcademiqueId)
    {
        $data = $this->calculerMoyennesData($eleveId, $anneeAcademiqueId);
        $pdf = Pdf::loadView('note.bulletin_pdf', $data);
        return $pdf->download('bulletin_' . $eleveId . '_annee_' . $anneeAcademiqueId . '.pdf');
    }

    // protected function calculerMoyennesData($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with(['classe', 'eleve'])->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
    //     $classeId = $inscription->classe->id;
    //     $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();
    //     $etablissement = Etablissement::first();

    //     $data = [
    //         'inscription' => $inscription,
    //         'anneeAcademique' => $anneeAcademique,
    //         'trimestres' => $trimestres,
    //         'coefficients' => $coefficients,
    //         'moyennesParTrimestre' => [],
    //         'moyennesGenerales' => [],
    //         'moyennesInterrogations' => [],
    //         'moyennesDevoirs' => [],
    //         'notesDetaillees' => [],
    //         'rangsMatiere' => [],
    //         'rangsTrimestriels' => [],
    //         'mentions' => [],
    //         'totaux' => [],
    //     ];

    //     $elevesClasse = Inscription::where('classe_id', $classeId)->pluck('eleve_id');
    //     $rangsTrimestrielsParTrimestre = [];
    //     foreach ($trimestres as $trimestre) {
    //         $rangsTrimestrielsParTrimestre[$trimestre->id] = $this->calculerRangsTrimestrielsClasse($elevesClasse, $trimestre->id, $coefficients);
    //         $data['rangsTrimestriels'][$trimestre->id] = $rangsTrimestrielsParTrimestre[$trimestre->id][$eleveId] ?? 'N/A';
    //     }

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;
    //         $totalCoefficients = 0;
    //         $hasAllNotes = true;

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);

    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->avg('valeur_note') : null;

    //             $notesDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2']);
    //             $moyenneDevoirs = $notesDevoirs->isNotEmpty() ? $notesDevoirs->avg('valeur_note') : null;

    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
    //             } else {
    //                 $hasAllNotes = false;
    //             }

    //             $data['notesDetaillees'][$trimestre->id][$matiere->nom] = $notesMatiere->pluck('valeur_note', 'type_evaluation')->toArray();
    //             $data['moyennesInterrogations'][$trimestre->id][$matiere->nom] = $moyenneInterrogations ? number_format($moyenneInterrogations, 2) : 'N/A';
    //             $data['moyennesDevoirs'][$trimestre->id][$matiere->nom] = $moyenneDevoirs ? number_format($moyenneDevoirs, 2) : 'N/A';
    //             $data['moyennesParTrimestre'][$trimestre->id][$matiere->nom] = $moyenneFinale ? number_format($moyenneFinale, 2) : 'N/A';

    //             if ($moyenneFinale !== null) {
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;

    //                 $data['rangsMatiere'][$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id)[$eleveId] ?? 'N/A';
    //                 $data['mentions'][$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
    //             }
    //         }

    //         $data['moyennesGenerales'][$trimestre->id] = $totalCoefficients > 0 ? number_format($sommeMoyennesCoefficientees / $totalCoefficients, 2) : 'N/A';
    //         $data['totaux'][$trimestre->id] = [
    //             'somme_coefficients' => $totalCoefficients,
    //             'somme_moyennes' => number_format($sommeMoyennes, 2),
    //             'somme_moyennes_coefficientees' => number_format($sommeMoyennesCoefficientees, 2),
    //         ];
    //     }

    //     return $data;
    // }


    // public function calculerMoyennes($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with(['classe', 'eleve'])->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
    //     $classeId = $inscription->classe->id;
    //     $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();
    //     $etablissement = Etablissement::first() ?? (object) ['republique' => 'République du Bénin', 'nom' => 'Nom de l\'École', 'contact' => '+123 456 789', 'devise' => 'Amour - Discipline - Travail'];

    //     $data = [
    //         'moyennesParTrimestre' => [],
    //         'moyennesGenerales' => [],
    //         'moyennesInterrogations' => [],
    //         'moyennesDevoirs' => [],
    //         'notesDetaillees' => [],
    //         'rangsMatiere' => [],
    //         'rangsTrimestriels' => [],
    //         'mentions' => [],
    //         'totaux' => [],
    //         'moyenneFaible' => [],
    //         'moyenneForte' => [],
    //         'moyenneClasse' => [],
    //         'recompenses' => [],
    //         'appreciationPrincipal' => [],
    //         'bilanAnnuel' => null,
    //         'moyenneGeneraleAnnuelle' => null,
    //         'rangAnnuel' => null,
    //         'decisionConseil' => 'Passe',
    //         'etablissement' => $etablissement,
    //     ];

    //     $elevesClasse = Inscription::where('classe_id', $classeId)->pluck('eleve_id');
    //     $rangsTrimestrielsParTrimestre = [];
    //     foreach ($trimestres as $trimestre) {
    //         $rangsTrimestrielsParTrimestre[$trimestre->id] = $this->calculerRangsTrimestrielsClasse($elevesClasse, $trimestre->id, $coefficients);
    //         $data['rangsTrimestriels'][$trimestre->id] = $rangsTrimestrielsParTrimestre[$trimestre->id][$eleveId] ?? 'N/A';

    //         // Calcul bilan classe (moy faible/forte/classe)
    //         $moyennesClasse = [];
    //         foreach ($elevesClasse as $eleveClasseId) {
    //             $moyClasse = $this->calculerMoyenneGeneralePourTrimestre($eleveClasseId, $trimestre->id);
    //             if ($moyClasse !== null) $moyennesClasse[] = $moyClasse;
    //         }
    //         $data['moyenneFaible'][$trimestre->id] = !empty($moyennesClasse) ? number_format(min($moyennesClasse), 2) : 'N/A';
    //         $data['moyenneForte'][$trimestre->id] = !empty($moyennesClasse) ? number_format(max($moyennesClasse), 2) : 'N/A';
    //         $data['moyenneClasse'][$trimestre->id] = !empty($moyennesClasse) ? number_format(array_sum($moyennesClasse) / count($moyennesClasse), 2) : 'N/A';

    //         // Récompenses/sanctions basées sur moyenne générale (comme mentions)
    //         $moyGenerale = $data['moyennesGenerales'][$trimestre->id] ?? 0;
    //         $data['recompenses'][$trimestre->id] = [
    //             'felicitations' => $moyGenerale >= 16,
    //             'encouragements' => $moyGenerale >= 14 && $moyGenerale < 16,
    //             'tableau_honneur' => $moyGenerale >= 12 && $moyGenerale < 14,
    //             'avertissement' => $moyGenerale < 10 && $moyGenerale >= 8,
    //             'blame' => $moyGenerale < 8,
    //         ];

    //         // Appréciation professeur principal (basée sur moyenne, comme mentions)
    //         $data['appreciationPrincipal'][$trimestre->id] = $this->determinerAppreciation($moyGenerale);
    //     }

    //     // Bilan annuel seulement pour 3ème trimestre
    //     $trimestre3 = $trimestres->where('nom', 'like', '%3ème%')->first();
    //     if ($trimestre3) {
    //         $moy1 = $data['moyennesGenerales'][1] ?? 0;
    //         $moy2 = $data['moyennesGenerales'][2] ?? 0;
    //         $moy3 = $data['moyennesGenerales'][3] ?? 0;
    //         $data['bilanAnnuel'] = number_format(($moy1 + $moy2 + $moy3) / 3, 2);
    //         $data['moyenneGeneraleAnnuelle'] = $data['bilanAnnuel'];
    //         $data['rangAnnuel'] = $this->calculerRangAnnuel($elevesClasse, $anneeAcademiqueId, $coefficients)[$eleveId] ?? 'N/A';
    //         $data['decisionConseil'] = $data['moyenneGeneraleAnnuelle'] >= 10 ? 'Passe' : 'Redouble';
    //     }

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;
    //         $totalCoefficients = 0;
    //         $hasAllNotes = true;

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);

    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->avg('valeur_note') : null;

    //             $notesDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2']);
    //             $moyenneDevoirs = $notesDevoirs->isNotEmpty() ? $notesDevoirs->avg('valeur_note') : null;

    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
    //             } else {
    //                 $hasAllNotes = false;
    //             }

    //             $data['notesDetaillees'][$trimestre->id][$matiere->nom] = $notesMatiere->pluck('valeur_note', 'type_evaluation')->toArray();
    //             $data['moyennesInterrogations'][$trimestre->id][$matiere->nom] = $moyenneInterrogations ? number_format($moyenneInterrogations, 2) : 'N/A';
    //             $data['moyennesDevoirs'][$trimestre->id][$matiere->nom] = $moyenneDevoirs ? number_format($moyenneDevoirs, 2) : 'N/A';
    //             $data['moyennesParTrimestre'][$trimestre->id][$matiere->nom] = $moyenneFinale ? number_format($moyenneFinale, 2) : 'N/A';

    //             if ($moyenneFinale !== null) {
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;

    //                 $data['rangsMatiere'][$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id)[$eleveId] ?? 'N/A';
    //                 $data['mentions'][$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
    //             }
    //         }

    //         $data['moyennesGenerales'][$trimestre->id] = $totalCoefficients > 0 ? number_format($sommeMoyennesCoefficientees / $totalCoefficients, 2) : 'N/A';
    //         $data['totaux'][$trimestre->id] = [
    //             'somme_coefficients' => $totalCoefficients,
    //             'somme_moyennes' => number_format($sommeMoyennes, 2),
    //             'somme_moyennes_coefficientees' => number_format($sommeMoyennesCoefficientees, 2),
    //         ];
    //     }

    //     return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'coefficients') + $data);
    // }

    // protected function determinerAppreciation($moyenne)
    // {
    //     if ($moyenne >= 16) return 'Excellent travail, élève exemplaire.';
    //     if ($moyenne >= 14) return 'Très bon travail, continuez ainsi.';
    //     if ($moyenne >= 12) return 'Bon travail, efforts appréciés.';
    //     if ($moyenne >= 10) return 'Travail passable, amélioration possible.';
    //     return 'Efforts supplémentaires nécessaires.';
    // }


    protected function calculerRangAnnuel($elevesIds, $anneeAcademiqueId, $coefficients)
    {
        $moyennesAnnuelles = [];
        foreach ($elevesIds as $eleveId) {
            $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
            $sommeMoyennesAnnuelles = 0;
            $countTrimestres = 0;
            foreach ($trimestres as $trimestre) {
                $moyTrim = $this->calculerMoyenneGeneralePourTrimestre($eleveId, $trimestre->id);
                if ($moyTrim !== null) {
                    $sommeMoyennesAnnuelles += $moyTrim;
                    $countTrimestres++;
                }
            }
            $moyennesAnnuelles[$eleveId] = $countTrimestres > 0 ? $sommeMoyennesAnnuelles / $countTrimestres : 0;
        }

        arsort($moyennesAnnuelles);
        $rang = 1;
        $rangs = [];
        foreach ($moyennesAnnuelles as $eleveId => $moyenne) {
            $rangs[$eleveId] = $rang++;
        }
        return $rangs;
    }


//     protected function calculerMoyenneGeneralePourTrimestre($eleveId, $trimestreId, $coefficients = null)
// {
//     $notes = Note::where('eleve_id', $eleveId)
//         ->where('trimestre_id', $trimestreId)
//         ->with('matiere')
//         ->get();

//     $totalPoints = 0;
//     $totalCoefficients = 0;

//     if ($coefficients === null) {
//         $eleve = Eleve::find($eleveId);
//         $classeId = $eleve->classe_id;

//         $coefficients = Coefficient::where('classe_id', $classeId)
//             ->with('matiere')
//             ->get();
//     }

//     foreach ($coefficients as $coeff) {
//         $notesMatiere = $notes->where('matiere_id', $coeff->matiere_id);

//         $moyenneInterrogations = $notesMatiere
//             ->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3'])
//             ->avg('valeur_note');

//         $moyenneDevoirs = $notesMatiere
//             ->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2'])
//             ->avg('valeur_note');

//         $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2 ?? null;

//         if ($moyenneFinale !== null) {
//             $totalPoints += $moyenneFinale * $coeff->valeur_coefficient;
//             $totalCoefficients += $coeff->valeur_coefficient;
//         }
//     }

//     return $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
// }

    protected function calculerMoyennesData($eleveId, $anneeAcademiqueId)
    {
        $inscription = Inscription::with(['classe', 'eleve'])->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
        $classeId = $inscription->classe->id;
        $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
        $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
        $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();
        $etablissement = Etablissement::first() ?? (object) ['republique' => 'République du Bénin', 'nom' => 'Nom de l\'École', 'contact' => '+123 456 789', 'devise' => 'Amour - Discipline - Travail'];

        $data = [
            'moyennesParTrimestre' => [],
            'moyennesGenerales' => [],
            'moyennesInterrogations' => [],
            'moyennesDevoirs' => [],
            'notesDetaillees' => [],
            'rangsMatiere' => [],
            'rangsTrimestriels' => [],
            'mentions' => [],
            'totaux' => [],
            'moyenneFaible' => [],
            'moyenneForte' => [],
            'moyenneClasse' => [],
            'recompenses' => [],
            'appreciationPrincipal' => [],
            'bilanAnnuel' => null,
            'moyenneGeneraleAnnuelle' => null,
            'rangAnnuel' => null,
            'decisionConseil' => 'Passe',
            'etablissement' => $etablissement,
        ];

        $elevesClasse = Inscription::where('classe_id', $classeId)->pluck('eleve_id');
        $rangsTrimestrielsParTrimestre = [];
        foreach ($trimestres as $trimestre) {
            $rangsTrimestrielsParTrimestre[$trimestre->id] = $this->calculerRangsTrimestrielsClasse($elevesClasse, $trimestre->id, $coefficients);
            $data['rangsTrimestriels'][$trimestre->id] = $rangsTrimestrielsParTrimestre[$trimestre->id][$eleveId] ?? 'N/A';

            $moyennesClasse = [];
            foreach ($elevesClasse as $eleveClasseId) {
                $moyClasse = $this->calculerMoyenneGeneralePourTrimestre($eleveClasseId, $trimestre->id, $coefficients);
                if ($moyClasse !== null) $moyennesClasse[] = $moyClasse;
            }
            $data['moyenneFaible'][$trimestre->id] = !empty($moyennesClasse) ? number_format(min($moyennesClasse), 2) : 'N/A';
            $data['moyenneForte'][$trimestre->id] = !empty($moyennesClasse) ? number_format(max($moyennesClasse), 2) : 'N/A';
            $data['moyenneClasse'][$trimestre->id] = !empty($moyennesClasse) ? number_format(array_sum($moyennesClasse) / count($moyennesClasse), 2) : 'N/A';

            $moyGenerale = $data['moyennesGenerales'][$trimestre->id] ?? 0;
            $data['recompenses'][$trimestre->id] = [
                'felicitations' => $moyGenerale >= 16,
                'encouragements' => $moyGenerale >= 14 && $moyGenerale < 16,
                'tableau_honneur' => $moyGenerale >= 12 && $moyGenerale < 14,
                'avertissement' => $moyGenerale < 10 && $moyGenerale >= 8,
                'blame' => $moyGenerale < 8,
            ];

            $data['appreciationPrincipal'][$trimestre->id] = $this->determinerAppreciation($moyGenerale);
        }

        $trimestre3 = $trimestres->where('nom', 'like', '%3ème%')->first();
        if ($trimestre3) {
            $moy1 = $data['moyennesGenerales'][1] ?? 0;
            $moy2 = $data['moyennesGenerales'][2] ?? 0;
            $moy3 = $data['moyennesGenerales'][3] ?? 0;
            $data['bilanAnnuel'] = number_format(($moy1 + $moy2 + $moy3) / 3, 2);
            $data['moyenneGeneraleAnnuelle'] = $data['bilanAnnuel'];
            $data['rangAnnuel'] = $this->calculerRangAnnuel($elevesClasse, $anneeAcademiqueId, $coefficients)[$eleveId] ?? 'N/A';
            $data['decisionConseil'] = $data['moyenneGeneraleAnnuelle'] >= 10 ? 'Passe' : 'Redouble';
        }

        foreach ($trimestres as $trimestre) {
            $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();
            // $conduite = Conduite::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->first();
            $sommeMoyennes = 0;
            $sommeMoyennesCoefficientees = 0;
            $totalCoefficients = 0;
            $hasAllNotes = true;

            foreach ($coefficients as $coefficient) {
                $matiere = $coefficient->matiere;
                $notesMatiere = $notes->where('matiere_id', $matiere->id);

                $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
                $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->avg('valeur_note') : null;

                $notesDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2']);
                $moyenneDevoirs = $notesDevoirs->isNotEmpty() ? $notesDevoirs->avg('valeur_note') : null;

                $moyenneFinale = null;
                if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
                    $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
                } else {
                    $hasAllNotes = false;
                }

                $data['notesDetaillees'][$trimestre->id][$matiere->nom] = $notesMatiere->pluck('valeur_note', 'type_evaluation')->toArray();
                $data['moyennesInterrogations'][$trimestre->id][$matiere->nom] = $moyenneInterrogations ? number_format($moyenneInterrogations, 2) : 'N/A';
                $data['moyennesDevoirs'][$trimestre->id][$matiere->nom] = $moyenneDevoirs ? number_format($moyenneDevoirs, 2) : 'N/A';
                $data['moyennesParTrimestre'][$trimestre->id][$matiere->nom] = $moyenneFinale ? number_format($moyenneFinale, 2) : 'N/A';

                if ($moyenneFinale !== null) {
                    $sommeMoyennes += $moyenneFinale;
                    $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
                    $totalCoefficients += $coefficient->valeur_coefficient;

                    $data['rangsMatiere'][$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id)[$eleveId] ?? 'N/A';
                    $data['mentions'][$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
                }

                // Ajouter la note de conduite
                // $conduiteNote = $conduite ? $conduite->valeur_note : null;
                // if ($conduiteNote !== null) {
                //     $conduiteCoefficient = 1; // Coefficient fixe pour la conduite
                //     $sommeMoyennes += $conduiteNote;
                //     $sommeMoyennesCoefficientees += $conduiteNote * $conduiteCoefficient;
                //     $totalCoefficients += $conduiteCoefficient;
                //     $data['moyennesParTrimestre'][$trimestre->id]['Conduite'] = number_format($conduiteNote, 2);
                //     $data['mentions'][$trimestre->id]['Conduite'] = $this->determinerMention($conduiteNote);
                // }
            }

            $data['moyennesGenerales'][$trimestre->id] = $totalCoefficients > 0 ? number_format($sommeMoyennesCoefficientees / $totalCoefficients, 2) : 'N/A';
            $data['totaux'][$trimestre->id] = [
                'somme_coefficients' => $totalCoefficients,
                'somme_moyennes' => number_format($sommeMoyennes, 2),
                'somme_moyennes_coefficientees' => number_format($sommeMoyennesCoefficientees, 2),
            ];
        }

        return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'coefficients') + $data);
    }





    public function calculerMoyennes($eleveId, $anneeAcademiqueId)
{
    $inscription = Inscription::with(['classe', 'eleve'])->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
    $classeId = $inscription->classe->id;
    $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
    $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
    $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();
    $etablissement = Etablissement::first() ?? (object) ['republique' => 'République du Bénin', 'nom' => 'Nom de l\'École', 'contact' => '+123 456 789', 'devise' => 'Amour - Discipline - Travail'];

    $data = [
        'moyennesParTrimestre' => [],
        'moyennesGenerales' => [],
        'moyennesInterrogations' => [],
        'moyennesDevoirs' => [],
        'notesDetaillees' => [],
        'rangsMatiere' => [],
        'rangsTrimestriels' => [],
        'mentions' => [],
        'totaux' => [],
        'moyenneFaible' => [],
        'moyenneForte' => [],
        'moyenneClasse' => [],
        'recompenses' => [],
        'appreciationPrincipal' => [],
        'bilanAnnuel' => null,
        'moyenneGeneraleAnnuelle' => null,
        'rangAnnuel' => null,
        'decisionConseil' => 'Passe',
        'etablissement' => $etablissement,
    ];

    $elevesClasse = Inscription::where('classe_id', $classeId)
        ->where('annee_academique_id', $anneeAcademiqueId) // IMPORTANT: ajouter cette condition
        ->pluck('eleve_id');

    // ÉTAPE 1: Calculer toutes les moyennes par matière et moyennes générales pour TOUS les trimestres
    foreach ($trimestres as $trimestre) {
        $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();
        $sommeMoyennes = 0;
        $sommeMoyennesCoefficientees = 0;
        $totalCoefficients = 0;

        foreach ($coefficients as $coefficient) {
            $matiere = $coefficient->matiere;
            $notesMatiere = $notes->where('matiere_id', $matiere->id);

            $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
            $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->avg('valeur_note') : null;

            $notesDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2']);
            $moyenneDevoirs = $notesDevoirs->isNotEmpty() ? $notesDevoirs->avg('valeur_note') : null;

            $moyenneFinale = null;
            if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
                $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
            } elseif ($moyenneInterrogations !== null && $moyenneDevoirs === null) {
                $moyenneFinale = $moyenneInterrogations; // Si pas de devoirs, prendre les interros
            } elseif ($moyenneInterrogations === null && $moyenneDevoirs !== null) {
                $moyenneFinale = $moyenneDevoirs; // Si pas d'interros, prendre les devoirs
            }

            // Stocker les détails
            $data['notesDetaillees'][$trimestre->id][$matiere->nom] = $notesMatiere->pluck('valeur_note', 'type_evaluation')->toArray();
            $data['moyennesInterrogations'][$trimestre->id][$matiere->nom] = $moyenneInterrogations ? number_format($moyenneInterrogations, 2) : 'N/A';
            $data['moyennesDevoirs'][$trimestre->id][$matiere->nom] = $moyenneDevoirs ? number_format($moyenneDevoirs, 2) : 'N/A';
            $data['moyennesParTrimestre'][$trimestre->id][$matiere->nom] = $moyenneFinale ? number_format($moyenneFinale, 2) : 'N/A';

            if ($moyenneFinale !== null) {
                $sommeMoyennes += $moyenneFinale;
                $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
                $totalCoefficients += $coefficient->valeur_coefficient;

                $data['rangsMatiere'][$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id)[$eleveId] ?? 'N/A';
                $data['mentions'][$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
            } else {
                $data['rangsMatiere'][$trimestre->id][$matiere->nom] = 'N/A';
                $data['mentions'][$trimestre->id][$matiere->nom] = 'N/A';
            }
        }

        // Calculer la moyenne générale pour ce trimestre
        $data['moyennesGenerales'][$trimestre->id] = $totalCoefficients > 0 ? number_format($sommeMoyennesCoefficientees / $totalCoefficients, 2) : 'N/A';
        $data['totaux'][$trimestre->id] = [
            'somme_coefficients' => $totalCoefficients,
            'somme_moyennes' => number_format($sommeMoyennes, 2),
            'somme_moyennes_coefficientees' => number_format($sommeMoyennesCoefficientees, 2),
        ];
    }

    // ÉTAPE 2: Maintenant calculer les rangs, bilans de classe et appréciations
    foreach ($trimestres as $trimestre) {
        // Calculer les rangs trimestriels
        $rangsTrimestrielsParTrimestre = $this->calculerRangsTrimestrielsClasse($elevesClasse, $trimestre->id, $coefficients);
        $data['rangsTrimestriels'][$trimestre->id] = $rangsTrimestrielsParTrimestre[$eleveId] ?? 'N/A';

        // Calculer le bilan de classe (moyennes faible/forte/classe)
        $moyennesClasse = [];
        foreach ($elevesClasse as $eleveClasseId) {
            $moyClasse = $this->calculerMoyenneGeneralePourTrimestre($eleveClasseId, $trimestre->id, $coefficients);
            if ($moyClasse !== null && $moyClasse > 0) {
                $moyennesClasse[] = $moyClasse;
            }
        }

        if (!empty($moyennesClasse)) {
            $data['moyenneFaible'][$trimestre->id] = number_format(min($moyennesClasse), 2);
            $data['moyenneForte'][$trimestre->id] = number_format(max($moyennesClasse), 2);
            $data['moyenneClasse'][$trimestre->id] = number_format(array_sum($moyennesClasse) / count($moyennesClasse), 2);
        } else {
            $data['moyenneFaible'][$trimestre->id] = 'N/A';
            $data['moyenneForte'][$trimestre->id] = 'N/A';
            $data['moyenneClasse'][$trimestre->id] = 'N/A';
        }

        // Utiliser la moyenne générale calculée (string vers float)
        $moyGeneraleStr = $data['moyennesGenerales'][$trimestre->id];
        $moyGenerale = is_numeric($moyGeneraleStr) ? (float) $moyGeneraleStr : 0;

        // Récompenses/sanctions basées sur moyenne générale
        $data['recompenses'][$trimestre->id] = [
            'felicitations' => $moyGenerale >= 16,
            'encouragements' => $moyGenerale >= 14 && $moyGenerale < 16,
            'tableau_honneur' => $moyGenerale >= 12 && $moyGenerale < 14,
            'avertissement' => $moyGenerale < 10 && $moyGenerale >= 8,
            'blame' => $moyGenerale < 8,
        ];

        // Appréciation professeur principal
        $data['appreciationPrincipal'][$trimestre->id] = $this->determinerAppreciation($moyGenerale);
    }

    // ÉTAPE 3: Bilan annuel seulement pour 3ème trimestre
    $trimestre3 = $trimestres->where('nom', 'like', '%3ème%')->first()
                ?? $trimestres->where('nom', 'like', '%3%')->first()
                ?? $trimestres->where('nom', 'like', '%troisième%')->first();

    if ($trimestre3) {
        // Récupérer les moyennes par ID de trimestre
        $trimestre1 = $trimestres->where('nom', 'like', '%1%')->first();
        $trimestre2 = $trimestres->where('nom', 'like', '%2%')->first();

        $moy1 = 0;
        $moy2 = 0;
        $moy3 = 0;

        if ($trimestre1 && isset($data['moyennesGenerales'][$trimestre1->id]) && is_numeric($data['moyennesGenerales'][$trimestre1->id])) {
            $moy1 = (float) $data['moyennesGenerales'][$trimestre1->id];
        }
        if ($trimestre2 && isset($data['moyennesGenerales'][$trimestre2->id]) && is_numeric($data['moyennesGenerales'][$trimestre2->id])) {
            $moy2 = (float) $data['moyennesGenerales'][$trimestre2->id];
        }
        if ($trimestre3 && isset($data['moyennesGenerales'][$trimestre3->id]) && is_numeric($data['moyennesGenerales'][$trimestre3->id])) {
            $moy3 = (float) $data['moyennesGenerales'][$trimestre3->id];
        }

        $nbTrimestres = 0;
        $sommeMoyennes = 0;

        if ($moy1 > 0) { $sommeMoyennes += $moy1; $nbTrimestres++; }
        if ($moy2 > 0) { $sommeMoyennes += $moy2; $nbTrimestres++; }
        if ($moy3 > 0) { $sommeMoyennes += $moy3; $nbTrimestres++; }

        if ($nbTrimestres > 0) {
            $data['bilanAnnuel'] = number_format($sommeMoyennes / $nbTrimestres, 2);
            $data['moyenneGeneraleAnnuelle'] = $data['bilanAnnuel'];
            $data['decisionConseil'] = (float) $data['moyenneGeneraleAnnuelle'] >= 10 ? 'Admis(e)' : 'Redouble';
        } else {
            $data['bilanAnnuel'] = 'N/A';
            $data['moyenneGeneraleAnnuelle'] = 'N/A';
            $data['decisionConseil'] = 'En attente';
        }

        $data['rangAnnuel'] = $this->calculerRangAnnuel($elevesClasse, $anneeAcademiqueId, $coefficients)[$eleveId] ?? 'N/A';
    }

    return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'coefficients') + $data);
}

protected function determinerAppreciation($moyenne)
{
    if ($moyenne >= 16) return 'Excellent travail, félicitations ! Élève exemplaire.';
    if ($moyenne >= 14) return 'Très bon travail, continuez sur cette voie.';
    if ($moyenne >= 12) return 'Bon travail dans l\'ensemble, des efforts appréciés.';
    if ($moyenne >= 10) return 'Travail passable, des progrès sont possibles.';
    if ($moyenne >= 8) return 'Travail insuffisant, plus d\'efforts nécessaires.';
    return 'Résultats préoccupants, un soutien pédagogique est recommandé.';
}

protected function calculerMoyenneGeneralePourTrimestre($eleveId, $trimestreId, $coefficients = null)
{
    $notes = Note::where('eleve_id', $eleveId)
        ->where('trimestre_id', $trimestreId)
        ->with('matiere')
        ->get();

    if ($notes->isEmpty()) {
        return null;
    }

    $totalPoints = 0;
    $totalCoefficients = 0;

    if ($coefficients === null) {
        // Récupérer les coefficients depuis l'inscription
        $inscription = Inscription::where('eleve_id', $eleveId)->first();
        if (!$inscription) return null;

        $coefficients = Coefficient::where('classe_id', $inscription->classe_id)
            ->with('matiere')
            ->get();
    }

    foreach ($coefficients as $coeff) {
        $notesMatiere = $notes->where('matiere_id', $coeff->matiere_id);

        if ($notesMatiere->isEmpty()) continue;

        $moyenneInterrogations = $notesMatiere
            ->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3'])
            ->avg('valeur_note');

        $moyenneDevoirs = $notesMatiere
            ->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2'])
            ->avg('valeur_note');

        $moyenneFinale = null;
        if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
            $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
        } elseif ($moyenneInterrogations !== null) {
            $moyenneFinale = $moyenneInterrogations;
        } elseif ($moyenneDevoirs !== null) {
            $moyenneFinale = $moyenneDevoirs;
        }

        if ($moyenneFinale !== null && $moyenneFinale > 0) {
            $totalPoints += $moyenneFinale * $coeff->valeur_coefficient;
            $totalCoefficients += $coeff->valeur_coefficient;
        }
    }

    return $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
}








    // protected function calculerMoyennesData($eleveId, $anneeAcademiqueId)
    // {
    //     $inscription = Inscription::with(['classe', 'eleve'])->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
    //     $classeId = $inscription->classe->id;
    //     $anneeAcademique = AnneeAcademique::findOrFail($anneeAcademiqueId);
    //     $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
    //     $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();
    //     $etablissement = Etablissement::first() ?? (object) ['republique' => 'République du Bénin', 'nom' => 'Nom de l\'École', 'contact' => '+123 456 789', 'devise' => 'Amour - Discipline - Travail'];

    //     $data = [
    //         // Même structure que calculerMoyennes
    //     ];

    //     $elevesClasse = Inscription::where('classe_id', $classeId)->pluck('eleve_id');
    //     $rangsTrimestrielsParTrimestre = [];
    //     foreach ($trimestres as $trimestre) {
    //         $rangsTrimestrielsParTrimestre[$trimestre->id] = $this->calculerRangsTrimestrielsClasse($elevesClasse, $trimestre->id, $coefficients);
    //         $data['rangsTrimestriels'][$trimestre->id] = $rangsTrimestrielsParTrimestre[$trimestre->id][$eleveId] ?? 'N/A';

    //         $moyennesClasse = [];
    //         foreach ($elevesClasse as $eleveClasseId) {
    //             $moyClasse = $this->calculerMoyenneGeneralePourTrimestre($eleveClasseId, $trimestre->id, $coefficients);
    //             if ($moyClasse !== null) $moyennesClasse[] = $moyClasse;
    //         }
    //         $data['moyenneFaible'][$trimestre->id] = !empty($moyennesClasse) ? number_format(min($moyennesClasse), 2) : 'N/A';
    //         $data['moyenneForte'][$trimestre->id] = !empty($moyennesClasse) ? number_format(max($moyennesClasse), 2) : 'N/A';
    //         $data['moyenneClasse'][$trimestre->id] = !empty($moyennesClasse) ? number_format(array_sum($moyennesClasse) / count($moyennesClasse), 2) : 'N/A';

    //         $moyGenerale = $data['moyennesGenerales'][$trimestre->id] ?? 0;
    //         $data['recompenses'][$trimestre->id] = [
    //             'felicitations' => $moyGenerale >= 16,
    //             'encouragements' => $moyGenerale >= 14 && $moyGenerale < 16,
    //             'tableau_honneur' => $moyGenerale >= 12 && $moyGenerale < 14,
    //             'avertissement' => $moyGenerale < 10 && $moyGenerale >= 8,
    //             'blame' => $moyGenerale < 8,
    //         ];

    //         $data['appreciationPrincipal'][$trimestre->id] = $this->determinerAppreciation($moyGenerale);
    //     }

    //     $trimestre3 = $trimestres->where('nom', 'like', '%3ème%')->first();
    //     if ($trimestre3) {
    //         $moy1 = $data['moyennesGenerales'][1] ?? 0;
    //         $moy2 = $data['moyennesGenerales'][2] ?? 0;
    //         $moy3 = $data['moyennesGenerales'][3] ?? 0;
    //         $data['bilanAnnuel'] = number_format(($moy1 + $moy2 + $moy3) / 3, 2);
    //         $data['moyenneGeneraleAnnuelle'] = $data['bilanAnnuel'];
    //         $data['rangAnnuel'] = $this->calculerRangAnnuel($elevesClasse, $anneeAcademiqueId, $coefficients)[$eleveId] ?? 'N/A';
    //         $data['decisionConseil'] = $data['moyenneGeneraleAnnuelle'] >= 10 ? 'Passe' : 'Redouble';
    //     }

    //     foreach ($trimestres as $trimestre) {
    //         $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();
    //         $sommeMoyennes = 0;
    //         $sommeMoyennesCoefficientees = 0;
    //         $totalCoefficients = 0;
    //         $hasAllNotes = true;

    //         foreach ($coefficients as $coefficient) {
    //             $matiere = $coefficient->matiere;
    //             $notesMatiere = $notes->where('matiere_id', $matiere->id);

    //             $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);
    //             $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->avg('valeur_note') : null;

    //             $notesDevoirs = $notesMatiere->whereIn('type_evaluation', ['Devoir 1', 'Devoir 2']);
    //             $moyenneDevoirs = $notesDevoirs->isNotEmpty() ? $notesDevoirs->avg('valeur_note') : null;

    //             $moyenneFinale = null;
    //             if ($moyenneInterrogations !== null && $moyenneDevoirs !== null) {
    //                 $moyenneFinale = ($moyenneInterrogations + $moyenneDevoirs) / 2;
    //             } else {
    //                 $hasAllNotes = false;
    //             }

    //             $data['notesDetaillees'][$trimestre->id][$matiere->nom] = $notesMatiere->pluck('valeur_note', 'type_evaluation')->toArray();
    //             $data['moyennesInterrogations'][$trimestre->id][$matiere->nom] = $moyenneInterrogations ? number_format($moyenneInterrogations, 2) : 'N/A';
    //             $data['moyennesDevoirs'][$trimestre->id][$matiere->nom] = $moyenneDevoirs ? number_format($moyenneDevoirs, 2) : 'N/A';
    //             $data['moyennesParTrimestre'][$trimestre->id][$matiere->nom] = $moyenneFinale ? number_format($moyenneFinale, 2) : 'N/A';

    //             if ($moyenneFinale !== null) {
    //                 $sommeMoyennes += $moyenneFinale;
    //                 $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;
    //                 $totalCoefficients += $coefficient->valeur_coefficient;

    //                 $data['rangsMatiere'][$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id)[$eleveId] ?? 'N/A';
    //                 $data['mentions'][$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
    //             }
    //         }

    //         $data['moyennesGenerales'][$trimestre->id] = $totalCoefficients > 0 ? number_format($sommeMoyennesCoefficientees / $totalCoefficients, 2) : 'N/A';
    //         $data['totaux'][$trimestre->id] = [
    //             'somme_coefficients' => $totalCoefficients,
    //             'somme_moyennes' => number_format($sommeMoyennes, 2),
    //             'somme_moyennes_coefficientees' => number_format($sommeMoyennesCoefficientees, 2),
    //         ];
    //     }

    //     return $data;
    // }



}
