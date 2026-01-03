<?php
namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Annee_academique;
use App\Models\Trimestre;
use App\Models\Inscription;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationAbsence;
use Illuminate\Support\Facades\Auth;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Support\Facades\DB;

class AbsenceController extends Controller
{
    // public function create($eleve_id, $annee_academique_id)
    // {
    //     $eleve = Eleve::findOrFail($eleve_id);
    //     $annee_academique = Annee_academique::findOrFail($annee_academique_id);
    //     $trimestres = Trimestre::where('annee_academique_id', $annee_academique_id)->get();
    //     $inscription = Inscription::where('eleve_id', $eleve_id)->where('annee_academique_id', $annee_academique_id)->first();

    //     if (!$inscription) {
    //         return redirect()
    //             ->back()
    //             ->withErrors(['error' => 'Aucune classe trouvée pour cet élève pour cette année académique.']);
    //     }

    //     $classe = Classe::findOrFail($inscription->classe_id);
    //     $absencesExistantes = Absence::where('eleve_id', $eleve_id)
    //         ->where('annee_academique_id', $annee_academique_id)
    //         ->get();

    //     return view('absence.create', compact('eleve', 'annee_academique', 'trimestres', 'classe', 'absencesExistantes'));
    // }

    // public function store(Request $request, $eleve_id, $annee_academique_id)
    // {
    //     $request->validate([
    //         'trimestre_id' => 'required|exists:trimestres,id',
    //         'date_absence' => 'required|date',
    //         'type' => 'required|in:absence,retard',
    //         'justification' => 'nullable|string',
    //     ]);

    //     $trimestre = Trimestre::find($request->trimestre_id);
    //     $dateActuelle = now();
    //     if ($dateActuelle < $trimestre->date_debut || $dateActuelle > $trimestre->date_fin) {
    //         return redirect()
    //             ->back()
    //             ->with(['error_message' => 'La saisie des absences n\'est pas autorisée pour ce trimestre.']);
    //     }

    //     $inscription = Inscription::where('eleve_id', $eleve_id)->where('annee_academique_id', $annee_academique_id)->first();
    //     if (!$inscription) {
    //         return redirect()->back()->with('error_message', 'Inscription non trouvée.');
    //     }

    //     Absence::create([
    //         'eleve_id' => $eleve_id,
    //         'annee_academique_id' => $annee_academique_id,
    //         'trimestre_id' => $request->trimestre_id,
    //         'classe_id' => $inscription->classe_id,
    //         'date_absence' => $request->date_absence,
    //         'type' => $request->type,
    //         'justification' => $request->justification,
    //     ]);

    //     $absencesNonJustifiees = Absence::where('eleve_id', $eleve_id)
    //     ->where('annee_academique_id', $annee_academique_id)
    //     ->where('justifiee', false)
    //     ->count();

    // if ($absencesNonJustifiees >= 2) {
    //     $eleve = Eleve::find($eleve_id);
    //     Mail::to($eleve->email_parent)->send(new NotificationAbsence($eleve, $absencesNonJustifiees));
    // }

    //     return redirect()
    //         ->route('absence.create', [$eleve_id, $annee_academique_id])
    //         ->with('success_message', 'Absence enregistrée avec succès.');
    // }

    public function index(Request $request)
    {
        $user = Auth::user();
        $anneeActive = Annee_Academique::where('statut', 'active')->first();
        $anneeSelectedId = $request->input('annee_academique_id', $anneeActive ? $anneeActive->id : null);
        $anneesAcademiques = Annee_Academique::orderBy('annee', 'desc')->get();

        $absencesQuery = Absence::with(['eleve', 'classe', 'matiere', 'trimestre', 'anneeAcademique', 'user']);

        // Statistiques
        $stats = [];
        if ($user->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            if (!$enseignant) {
                return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
            }

            $classesAssignees = DB::table('enseignant_classe')
                ->where('enseignant_id', $enseignant->id)
                ->where('annee_academique_id', $anneeSelectedId ?? $anneeActive->id ?? null)
                ->pluck('classe_id');

            $absencesQuery->whereIn('classe_id', $classesAssignees)
                          ->where(function ($query) use ($enseignant) {
                              $query->where('matiere_id', $enseignant->matiere_id)
                                    ->orWhereNull('matiere_id');
                          });

            // Statistiques par élève
            $stats['parEleve'] = Absence::whereIn('classe_id', $classesAssignees)
                ->where('annee_academique_id', $anneeSelectedId ?? $anneeActive->id ?? null)
                ->groupBy('eleve_id')
                ->select('eleve_id', DB::raw('count(*) as total'), DB::raw('sum(justifiee) as justifiees'))
                ->with('eleve')
                ->get()
                ->map(function ($item) {
                    return [
                        'eleve' => $item->eleve->nom . ' ' . $item->eleve->prenom,
                        'total' => $item->total,
                        'justifiees' => $item->justifiees,
                        'non_justifiees' => $item->total - $item->justifiees,
                    ];
                });

            // Statistiques par classe
            $stats['parClasse'] = Absence::whereIn('classe_id', $classesAssignees)
                ->where('annee_academique_id', $anneeSelectedId ?? $anneeActive->id ?? null)
                ->groupBy('classe_id')
                ->select('classe_id', DB::raw('count(*) as total'), DB::raw('sum(justifiee) as justifiees'))
                ->with('classe')
                ->get()
                ->map(function ($item) {
                    return [
                        'classe' => $item->classe->nom_classe,
                        'total' => $item->total,
                        'justifiees' => $item->justifiees,
                        'non_justifiees' => $item->total - $item->justifiees,
                    ];
                });
        } else {
            // Admin: Toutes les absences
            if ($anneeSelectedId) {
                $absencesQuery->where('annee_academique_id', $anneeSelectedId);
            }

            $stats['parEleve'] = Absence::where('annee_academique_id', $anneeSelectedId ?? $anneeActive->id ?? null)
                ->groupBy('eleve_id')
                ->select('eleve_id', DB::raw('count(*) as total'), DB::raw('sum(justifiee) as justifiees'))
                ->with('eleve')
                ->get()
                ->map(function ($item) {
                    return [
                        'eleve' => $item->eleve->nom . ' ' . $item->eleve->prenom,
                        'total' => $item->total,
                        'justifiees' => $item->justifiees,
                        'non_justifiees' => $item->total - $item->justifiees,
                    ];
                });

            $stats['parClasse'] = Absence::where('annee_academique_id', $anneeSelectedId ?? $anneeActive->id ?? null)
                ->groupBy('classe_id')
                ->select('classe_id', DB::raw('count(*) as total'), DB::raw('sum(justifiee) as justifiees'))
                ->with('classe')
                ->get()
                ->map(function ($item) {
                    return [
                        'classe' => $item->classe->nom_classe,
                        'total' => $item->total,
                        'justifiees' => $item->justifiees,
                        'non_justifiees' => $item->total - $item->justifiees,
                    ];
                });
        }

        $absences = $absencesQuery->orderBy('date_absence', 'desc')->get();
        $anneeActiveLabel = $anneeSelectedId
            ? Annee_Academique::find($anneeSelectedId)->annee
            : ($anneeActive ? $anneeActive->annee : 'Aucune année active');

        return view('absence.index', compact('absences', 'anneesAcademiques', 'anneeSelectedId', 'anneeActiveLabel', 'stats'));
    }

    public function create($eleve_id, $annee_academique_id)
    {
        $user = Auth::user();
        $eleve = Eleve::findOrFail($eleve_id);
        $annee_academique = Annee_Academique::findOrFail($annee_academique_id);
        $trimestres = Trimestre::where('annee_academique_id', $annee_academique_id)->get();
        $inscription = Inscription::where('eleve_id', $eleve_id)
            ->where('annee_academique_id', $annee_academique_id)
            ->first();

        if (!$inscription) {
            return redirect()->back()->with('error_message', 'Aucune classe trouvée pour cet élève pour cette année académique.');
        }

        $classe = Classe::findOrFail($inscription->classe_id);

        // Vérification pour les enseignants
        $enseignant = null;
        if ($user->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            if (!$enseignant) {
                return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
            }

            // Vérifier si l'élève est dans une classe assignée à l'enseignant
            $isAssigned = DB::table('enseignant_classe')
                ->where('enseignant_id', $enseignant->id)
                ->where('classe_id', $inscription->classe_id)
                ->where('annee_academique_id', $annee_academique_id)
                ->exists();

            if (!$isAssigned) {
                return redirect()->back()->with('error_message', 'Vous n’êtes pas autorisé à enregistrer des absences pour cette classe.');
            }
        }

        $absencesExistantes = Absence::where('eleve_id', $eleve_id)
            ->where('annee_academique_id', $annee_academique_id)
            ->with('matiere')
            ->get();

        return view('absence.create', compact('eleve', 'annee_academique', 'trimestres', 'classe', 'absencesExistantes', 'enseignant'));
    }

    // public function store(Request $request, $eleve_id, $annee_academique_id)
    // {
    //     $user = Auth::user();

    //     $request->validate([
    //         'trimestre_id' => 'required|exists:trimestres,id',
    //         'date_absence' => 'required|date',
    //         'type' => 'required|in:absence,retard',
    //         'justification' => 'nullable|string',
    //         'matiere_id' => 'required_if:type,absence|exists:matieres,id',
    //     ]);

    //     $trimestre = Trimestre::find($request->trimestre_id);
    //     $dateActuelle = now();
    //     if ($dateActuelle < $trimestre->date_debut || $dateActuelle > $trimestre->date_fin) {
    //         return redirect()->back()->with('error_message', 'La saisie des absences n’est pas autorisée pour ce trimestre.');
    //     }

    //     $inscription = Inscription::where('eleve_id', $eleve_id)
    //         ->where('annee_academique_id', $annee_academique_id)
    //         ->first();
    //     if (!$inscription) {
    //         return redirect()->back()->with('error_message', 'Inscription non trouvée.');
    //     }

    //     // Vérification pour les enseignants
    //     $matiere_id = $request->type === 'absence' ? $request->matiere_id : null;
    //     if ($user->role === 'enseignant') {
    //         $enseignant = Enseignant::where('user_id', $user->id)->first();
    //         if (!$enseignant) {
    //             return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
    //         }

    //         // Vérifier si la classe est assignée
    //         $isAssigned = DB::table('enseignant_classe')
    //             ->where('enseignant_id', $enseignant->id)
    //             ->where('classe_id', $inscription->classe_id)
    //             ->where('annee_academique_id', $annee_academique_id)
    //             ->exists();

    //         if (!$isAssigned) {
    //             return redirect()->back()->with('error_message', 'Vous n’êtes pas autorisé à enregistrer des absences pour cette classe.');
    //         }

    //         // Vérifier si la matière correspond à celle de l’enseignant
    //         if ($request->type === 'absence' && $enseignant->matiere_id != $request->matiere_id) {
    //             return redirect()->back()->with('error_message', 'Vous ne pouvez enregistrer des absences que pour votre matière.');
    //         }

    //         $matiere_id = $enseignant->matiere_id; // Forcer la matière de l’enseignant pour les absences
    //     }

    //     Absence::create([
    //         'eleve_id' => $eleve_id,
    //         'annee_academique_id' => $annee_academique_id,
    //         'trimestre_id' => $request->trimestre_id,
    //         'classe_id' => $inscription->classe_id,
    //         'matiere_id' => $matiere_id,
    //         'date_absence' => $request->date_absence,
    //         'type' => $request->type,
    //         'user_id' => $user->id,
    //         'justification' => $request->justification,
    //         'justifiee' => false,
    //     ]);

    //     // Notification par email si 2+ absences non justifiées
    //     $absencesNonJustifiees = Absence::where('eleve_id', $eleve_id)
    //         ->where('annee_academique_id', $annee_academique_id)
    //         ->where('justifiee', false)
    //         ->count();

    //     if ($absencesNonJustifiees >= 2) {
    //         $eleve = Eleve::find($eleve_id);
    //         Mail::to($eleve->email_parent)->send(new NotificationAbsence($eleve, $absencesNonJustifiees));
    //     }

    //     return redirect()
    //         ->route('absence.create', [$eleve_id, $annee_academique_id])
    //         ->with('success_message', 'Absence enregistrée avec succès.');
    // }


    public function store(Request $request, $eleve_id, $annee_academique_id)
    {
        $user = Auth::user();

        $request->validate([
            'trimestre_id' => 'required|exists:trimestres,id',
            'date_absence' => 'required|date',
            'type' => 'required|in:absence,retard',
            'justification' => 'nullable|string',
            'matiere_id' => 'required_if:type,absence|exists:matieres,id',
        ]);

        $trimestre = Trimestre::find($request->trimestre_id);
        $dateActuelle = now();

        if ($dateActuelle < $trimestre->date_debut || $dateActuelle > $trimestre->date_fin) {
            return redirect()->back()->with('error_message', 'La saisie des absences n’est pas autorisée pour ce trimestre.');
        }

        $inscription = Inscription::where('eleve_id', $eleve_id)
            ->where('annee_academique_id', $annee_academique_id)
            ->first();

        if (!$inscription) {
            return redirect()->back()->with('error_message', 'Inscription non trouvée.');
        }

        // ✅ 1. Règle spéciale pour l'administrateur
        if ($user->role === 'admin' && $request->type === 'absence') {
            return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à enregistrer des absences. Seuls les retards sont permis.");
        }

        if ($user->role === 'enseignant' && $request->type === 'retard') {
            return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à enregistrer des retards. Seuls les absences sont permis.");
        }

        // ✅ 2. Vérifications spécifiques pour les enseignants
        $matiere_id = $request->type === 'absence' ? $request->matiere_id : null;

        if ($user->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            if (!$enseignant) {
                return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
            }

            $isAssigned = DB::table('enseignant_classe')
                ->where('enseignant_id', $enseignant->id)
                ->where('classe_id', $inscription->classe_id)
                ->where('annee_academique_id', $annee_academique_id)
                ->exists();

            if (!$isAssigned) {
                return redirect()->back()->with('error_message', 'Vous n’êtes pas autorisé à enregistrer des absences pour cette classe.');
            }

            if ($request->type === 'absence' && $enseignant->matiere_id != $request->matiere_id) {
                return redirect()->back()->with('error_message', 'Vous ne pouvez enregistrer des absences que pour votre matière.');
            }

            $matiere_id = $enseignant->matiere_id;
        }

        Absence::create([
            'eleve_id' => $eleve_id,
            'annee_academique_id' => $annee_academique_id,
            'trimestre_id' => $request->trimestre_id,
            'classe_id' => $inscription->classe_id,
            'matiere_id' => $matiere_id,
            'date_absence' => $request->date_absence,
            'type' => $request->type,
            'user_id' => $user->id,
            'justification' => $request->justification,
            'justifiee' => false,
        ]);

        // Notification si 2+ absences non justifiées
        $absencesNonJustifiees = Absence::where('eleve_id', $eleve_id)
            ->where('annee_academique_id', $annee_academique_id)
            ->where('justifiee', false)
            ->count();

        if ($absencesNonJustifiees >= 2) {
            $eleve = Eleve::find($eleve_id);
            Mail::to($eleve->email_parent)->send(new NotificationAbsence($eleve, $absencesNonJustifiees));
        }

        return redirect()
            ->route('absence.create', [$eleve_id, $annee_academique_id])
            ->with('success_message', 'Absence enregistrée avec succès.');
    }



    public function edit($id)
    {
        $absence = Absence::with(['eleve', 'classe', 'trimestre', 'anneeAcademique', 'matiere'])->findOrFail($id);
        $user = Auth::user();

        // Vérifications d'autorisation
        if ($user->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            if (!$enseignant) {
                return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
            }

            $isAssigned = DB::table('enseignant_classe')
                ->where('enseignant_id', $enseignant->id)
                ->where('classe_id', $absence->classe_id)
                ->where('annee_academique_id', $absence->annee_academique_id)
                ->exists();

            if (!$isAssigned && $absence->type === 'absence') {
                return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à modifier cette absence.");
            }
        }

        $trimestres = Trimestre::where('annee_academique_id', $absence->annee_academique_id)->get();
        $matieres = Matiere::all();

        return view('absence.edit', compact('absence', 'trimestres', 'matieres', 'user'));
    }

    // public function update(Request $request, $id)
    // {
    //     $user = Auth::user();
    //     $absence = Absence::findOrFail($id);

    //     $request->validate([
    //         'trimestre_id' => 'required|exists:trimestres,id',
    //         'date_absence' => 'required|date',
    //         'type' => 'required|in:absence,retard',
    //         'justification' => 'nullable|string',
    //         'justifiee' => 'required|boolean',
    //         'matiere_id' => 'required_if:type,absence|exists:matieres,id',
    //     ]);

    //     // Vérifications pour enseignants
    //     $matiere_id = null;
    //     if ($user->role === 'enseignant') {
    //         $enseignant = Enseignant::where('user_id', $user->id)->first();
    //         if (!$enseignant) {
    //             return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
    //         }

    //         $isAssigned = DB::table('enseignant_classe')
    //             ->where('enseignant_id', $enseignant->id)
    //             ->where('classe_id', $absence->classe_id)
    //             ->where('annee_academique_id', $absence->annee_academique_id)
    //             ->exists();

    //         if (!$isAssigned && $request->type === 'absence') {
    //             return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à modifier cette absence.");
    //         }

    //         if ($request->type === 'absence') {
    //             $matiere_id = $enseignant->matiere_id;
    //         }
    //     } else {
    //         $matiere_id = $request->type === 'absence' ? $request->matiere_id : null;
    //     }

    //     $absence->update([
    //         'trimestre_id' => $request->trimestre_id,
    //         'date_absence' => $request->date_absence,
    //         'type' => $request->type,
    //         'justification' => $request->justification,
    //         'justifiee' => $request->justifiee,
    //         'matiere_id' => $matiere_id,
    //         'user_id' => $user->id,
    //     ]);

    //     return redirect()->route('absence.create', [$absence->eleve_id, $absence->annee_academique_id])
    //         ->with('success_message', 'Absence modifiée avec succès.');
    // }


    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $absence = Absence::findOrFail($id);

        // ✅ Vérifier que c’est bien le créateur qui modifie
        if ($absence->user_id !== $user->id) {
            return redirect()->back()->with('error_message', "Vous ne pouvez pas modifier une absence que vous n'avez pas enregistrée.");
        }

        $request->validate([
            'trimestre_id' => 'required|exists:trimestres,id',
            'date_absence' => 'required|date',
            'type' => 'required|in:absence,retard',
            'justification' => 'nullable|string',
            'justifiee' => 'required|boolean',
            'matiere_id' => 'required_if:type,absence|exists:matieres,id',
        ]);

        // ✅ Règle spéciale pour admin
        if ($user->role === 'Admin' && $request->type === 'absence') {
            return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à modifier une absence de type 'absence'.");
        }

        // Reste du code inchangé
        $matiere_id = null;
        if ($user->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            if (!$enseignant) {
                return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
            }

            $isAssigned = DB::table('enseignant_classe')
                ->where('enseignant_id', $enseignant->id)
                ->where('classe_id', $absence->classe_id)
                ->where('annee_academique_id', $absence->annee_academique_id)
                ->exists();

            if (!$isAssigned && $request->type === 'absence') {
                return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à modifier cette absence.");
            }

            if ($request->type === 'absence') {
                $matiere_id = $enseignant->matiere_id;
            }
        } else {
            $matiere_id = $request->type === 'absence' ? $request->matiere_id : null;
        }

        $absence->update([
            'trimestre_id' => $request->trimestre_id,
            'date_absence' => $request->date_absence,
            'type' => $request->type,
            'justification' => $request->justification,
            'justifiee' => $request->justifiee,
            'matiere_id' => $matiere_id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('absence.create', [$absence->eleve_id, $absence->annee_academique_id])
            ->with('success_message', 'Absence modifiée avec succès.');
    }


    // public function destroy($id)
    // {
    //     $absence = Absence::findOrFail($id);
    //     $user = Auth::user();

    //     // Vérifications d'autorisation
    //     if ($user->role === 'enseignant') {
    //         $enseignant = Enseignant::where('user_id', $user->id)->first();
    //         if (!$enseignant) {
    //             return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
    //         }

    //         $isAssigned = DB::table('enseignant_classe')
    //             ->where('enseignant_id', $enseignant->id)
    //             ->where('classe_id', $absence->classe_id)
    //             ->where('annee_academique_id', $absence->annee_academique_id)
    //             ->exists();

    //         if (!$isAssigned) {
    //             return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à supprimer cette absence.");
    //         }
    //     }

    //     $absence->delete();

    //     return redirect()->route('absence.create', [$absence->eleve_id, $absence->annee_academique_id])
    //         ->with('success_message', 'Absence supprimée avec succès.');
    // }


    public function destroy($id)
    {
        $absence = Absence::findOrFail($id);
        $user = Auth::user();

        // ✅ Vérifier que c’est bien le créateur
        if ($absence->user_id !== $user->id) {
            return redirect()->back()->with('error_message', "Vous ne pouvez pas supprimer une absence que vous n'avez pas enregistrée.");
        }

        if ($user->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            if (!$enseignant) {
                return redirect()->back()->with('error_message', 'Enseignant non trouvé.');
            }

            $isAssigned = DB::table('enseignant_classe')
                ->where('enseignant_id', $enseignant->id)
                ->where('classe_id', $absence->classe_id)
                ->where('annee_academique_id', $absence->annee_academique_id)
                ->exists();

            if (!$isAssigned) {
                return redirect()->back()->with('error_message', "Vous n'êtes pas autorisé à supprimer cette absence.");
            }
        }

        $absence->delete();

        return redirect()->route('absence.create', [$absence->eleve_id, $absence->annee_academique_id])
            ->with('success_message', 'Absence supprimée avec succès.');
    }





}
