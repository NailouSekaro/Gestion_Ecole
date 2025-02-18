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

        return redirect()
            ->route('note.index', [$eleve_id, $annee_academique_id])
            ->with('success_message', 'Notes enregistrées avec succès. Un mail a été envoyé aux parents');
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

    public function determinerMention($moyenne)
    {
        if ($moyenne >= 16) {
            return 'Très Bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }

    public function calculerMoyennes($eleveId, $anneeAcademiqueId)
    {
        $inscription = Inscription::with('classe', 'eleve')->where('eleve_id', $eleveId)->where('annee_academique_id', $anneeAcademiqueId)->firstOrFail();
        $classeId = $inscription->classe->id;
        $anneeAcademique = Annee_academique::findOrFail($anneeAcademiqueId);
        $trimestres = Trimestre::where('annee_academique_id', $anneeAcademiqueId)->get();
        $coefficients = Coefficient::where('classe_id', $classeId)->with('matiere')->get();

        $moyennesParTrimestre = [];
        $moyennesGenerales = [];
        $moyennesInterrogations = [];
        $rangsMatiere = [];
        $rangsTrimestriels = [];
        $mentions = [];
        $totaux = [];

        foreach ($trimestres as $trimestre) {
            $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestre->id)->with('matiere')->get();

            $totalPoints = 0;
            $totalCoefficients = 0;
            $sommeMoyennes = 0;
            $sommeMoyennesCoefficientees = 0;
            $hasAllNotes = true;

            foreach ($coefficients as $coefficient) {
                $matiere = $coefficient->matiere;
                $notesMatiere = $notes->where('matiere_id', $matiere->id);
                $notesInterrogations = $notesMatiere->whereIn('type_evaluation', ['interrogation 1', 'interrogation 2', 'interrogation 3']);

                $moyenneInterrogations = $notesInterrogations->isNotEmpty() ? $notesInterrogations->sum('valeur_note') / $notesInterrogations->count() : null;
                $devoir1 = $notesMatiere->where('type_evaluation', 'Devoir 1')->first();
                $devoir2 = $notesMatiere->where('type_evaluation', 'Devoir 2')->first();

                $moyenneFinale = null;
                if ($moyenneInterrogations !== null && $devoir1 !== null && $devoir2 !== null) {
                    $moyenneFinale = ($moyenneInterrogations + $devoir1->valeur_note + $devoir2->valeur_note) / 3;
                } else {
                    $hasAllNotes = false;
                }

                $moyennesParTrimestre[$trimestre->id][$matiere->nom] = $moyenneFinale;
                $moyennesInterrogations[$trimestre->id][$matiere->nom] = $moyenneInterrogations;

                if ($moyenneFinale !== null) {
                    $totalPoints += $moyenneFinale * $coefficient->valeur_coefficient;
                    $totalCoefficients += $coefficient->valeur_coefficient;
                    $sommeMoyennes += $moyenneFinale;
                    $sommeMoyennesCoefficientees += $moyenneFinale * $coefficient->valeur_coefficient;

                    // Ajout du rang de l'élève pour chaque matière dans sa classe
                    $rangsMatiere[$trimestre->id][$matiere->nom] = $this->calculerRangMatiereClasse($classeId, $matiere->id, $trimestre->id);

                    // Ajout de la mention
                    $mentions[$trimestre->id][$matiere->nom] = $this->determinerMention($moyenneFinale);
                }
            }

            if ($hasAllNotes) {
                $moyennesGenerales[$trimestre->id] = $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
                $totaux[$trimestre->id] = [
                    'somme_coefficients' => $totalCoefficients,
                    'somme_moyennes' => $sommeMoyennes,
                    'somme_moyennes_coefficientees' => $sommeMoyennesCoefficientees,
                ];

                // Calcul du rang trimestriel pour chaque élève
                $rangsTrimestriels[$trimestre->id] = $this->calculerRangTrimestriel($classeId, $eleveId, $trimestre->id);
            } else {
                $moyennesGenerales[$trimestre->id] = null;
                $totaux[$trimestre->id] = [
                    'somme_coefficients' => 0,
                    'somme_moyennes' => 0,
                    'somme_moyennes_coefficientees' => 0,
                ];
                $rangsTrimestriels[$trimestre->id] = 'N/A';
            }
        }

        return view('note.moyenne', compact('inscription', 'anneeAcademique', 'trimestres', 'moyennesParTrimestre', 'moyennesGenerales', 'moyennesInterrogations', 'coefficients', 'totaux', 'rangsMatiere', 'mentions', 'rangsTrimestriels'));
    }

    public function calculerRangMatiereClasse($classeId, $matiereId, $trimestreId)
    {
        $notesMatiereClasse = Note::where('classe_id', $classeId)->where('matiere_id', $matiereId)->where('trimestre_id', $trimestreId)->with('eleve')->get();

        // Calculer les moyennes des élèves pour la matière spécifiée
        $moyennes = [];
        foreach ($notesMatiereClasse as $note) {
            if (!isset($moyennes[$note->eleve->id])) {
                $moyennes[$note->eleve->id] = 0;
            }
            $moyennes[$note->eleve->id] += $note->valeur_note;
        }

        // Trier les moyennes par ordre décroissant
        arsort($moyennes);

        // Attribuer les rangs en fonction des moyennes
        $rang = 1;
        $rangs = [];
        foreach ($moyennes as $eleveId => $moyenne) {
            $rangs[$eleveId] = $rang++;
        }

        return $rangs;
    }

    public function calculerRangTrimestriel($classeId, $eleveId, $trimestreId)
    {
        // Récupérer tous les élèves de la classe
        $elevesClasse = Eleve::whereHas('inscriptions', function ($query) use ($classeId) {
            $query->where('classe_id', $classeId);
        })->get();

        // Calculer les moyennes générales pour chaque élève de la classe pour le trimestre spécifié
        $moyennes = [];
        foreach ($elevesClasse as $eleve) {
            $moyenne = $this->calculerMoyenneGeneralePourTrimestre($eleve->id, $trimestreId);
            if ($moyenne !== null) {
                $moyennes[$eleve->id] = $moyenne;
            }
        }

        // Trier les moyennes par ordre décroissant pour déterminer les rangs
        arsort($moyennes);

        // Attribuer les rangs en fonction des moyennes
        $rang = 1;
        $rangs = [];
        foreach ($moyennes as $id => $moyenne) {
            $rangs[$id] = $rang++;
        }

        // Vérification du résultat du rang pour l'élève spécifié
        return isset($rangs[$eleveId]) ? $rangs[$eleveId] : 'N/A';
    }

    public function calculerMoyenneGeneralePourTrimestre($eleveId, $trimestreId)
    {
        // Récupérer toutes les notes de l'élève pour le trimestre spécifié
        $notes = Note::where('eleve_id', $eleveId)->where('trimestre_id', $trimestreId)->with('matiere')->get();

        if ($notes->isEmpty()) {
            return null;
        }

        $totalPoints = 0;
        $totalCoefficients = 0;

        foreach ($notes as $note) {
            $coefficient = $note->matiere->coefficient;
            $totalPoints += $note->valeur_note * $coefficient;
            $totalCoefficients += $coefficient;
        }

        return $totalCoefficients > 0 ? $totalPoints / $totalCoefficients : null;
    }

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
}
