<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotesEleveMail;
use App\Models\Eleve;
use App\Models\Note;
use App\Models\classe;
use App\Models\Inscription;
use App\Models\Enseignant;
use App\Models\Annee_academique;
use Illuminate\Support\Facades\Storage;

class EleveController extends Controller
{
    // public function envoyerNotes($eleveId)
    // {
    //     $eleve = Eleve::with('notes')->findOrFail($eleveId);

    //     if (!$eleve->email_parent) {
    //         return back()->with('error_message', "L'élève n'a pas d'email parent enregistré.");
    //     }

    //     // Génération du PDF
    //     $pdf = Pdf::loadView('pdf.bulletin_notes', compact('eleve'));
    //     $pdfPath = storage_path('app/public/Bulletin_' . $eleve->nom  . '.pdf');
    //     $pdf->save($pdfPath);

    //     // Envoi de l'email
    //     Mail::to($eleve->email_parent)->send(new NotesEleveMail($eleve, $pdfPath));

    //     return back()->with('success_message', "Les notes de {$eleve->nom}  ont été envoyées aux parents.");
    // }

    public function create()
    {
        $user = auth()->user();

        // Comparaison insensible à la casse des rôles
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            $classes = classe::all();
            $annees = Annee_academique::all();
            return view('eleve.create', compact('classes', 'annees'));
        } else {
            return redirect()->back()->with('error_message', 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }
    }
    public function store(Request $request)
    {
        $annee = Annee_academique::find($request->annee_academique_id);
        $dateActuelle = now();
        if ($dateActuelle < $annee->date_debut || $dateActuelle > $annee->date_fin) {
            return redirect()
                ->back()
                ->with(['error_message' => "L'enregistrement d'un nouveau élève pour cette année académique est clôturé."]);
        }

        // Validation des données
        $validated = $request->validate([
            'matricule_educ_master' => 'required|string|max:13|unique:eleves,matricule_educ_master',
            // 'matricule_eleve' => 'nullable|string|max:4',
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'lieu_de_naissance' => 'required|string|max:255',
            'sexe' => 'required|string',
            'statut' => 'required|string|max:15',
            'type' => 'required|string|max:15',
            'aptitude_sport' => 'required|string|max:3',
            'nationalite' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'email_parent' => 'required|string|max:255',
            'contact_parent' => 'required|string|max:255',
            'classe_id' => 'required|exists:classes,id',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
        ]);

        // if ($request->hasFile('photo')) {
        //     $file = $request->file('photo');
        //     $photoPath = $file->store('photos_identite', 'public'); // Sauvegarde dans le dossier 'storage/app/public/photos_identite'
        // }

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $file->store('photos_identite', 'public');
        } else {
            $photoPath = 'assets/images/faces/default-avatar.jpg';
        }

        $ancienneInscription = Inscription::where('eleve_id', $request->eleve_id)->where('classe_id', $request->classe_id)->first();

        // Si l'élève a déjà été inscrit dans cette classe, on force le statut à doublant
        if ($ancienneInscription) {
            $statut = 'Doublant(e)';
        } else {
            // Si c'est une nouvelle classe, utiliser le statut fourni (passant ou doublant)
            $statut = $request->statut;
        }

        // Génération du matricule de l'élève
        // $lastEleveId = Eleve::max('id');
        // $matricule_eleve = str_pad($lastEleveId + 1000, 4, '0', STR_PAD_LEFT);

        // Création de l'élève
        $eleve = Eleve::create([
            'matricule_educ_master' => $validated['matricule_educ_master'],
            // 'matricule_eleve' => 0 ,
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'date_naissance' => $validated['date_naissance'],
            'lieu_de_naissance' => $validated['lieu_de_naissance'],
            'aptitude_sport' => $validated['aptitude_sport'],
            'nationalite' => $validated['nationalite'],
            'photo' => $photoPath,
            'sexe' => $validated['sexe'],
            'email_parent' => $validated['email_parent'],
            'contact_parent' => $validated['contact_parent'],
        ]);

        // $eleve->matricule_eleve = $matricule_eleve;
        // $eleve->matricule_educ_master = $validatedData['matricule_educ_master'];
        // $eleve->nom = $validatedData['nom'];
        // $eleve->prenom = $validatedData['prenom'];
        // $eleve->aptitude_sport = $validatedData['aptitude_sport'];
        // $eleve->date_naissance = $validatedData['date_naissance'];
        // $eleve->sexe = $validatedData['sexe'];
        // $eleve->nationalite = $validatedData['nationalite'];
        // $eleve->email_parent = $validatedData['email_parent'];
        // $eleve->photo = $validatedData['photo'];
        // $eleve->save();

        // Création de l'inscription
        Inscription::create([
            'eleve_id' => $eleve->id,
            'classe_id' => $validated['classe_id'],
            'annee_academique_id' => $validated['annee_academique_id'],
            'type' => $validated['type'],
            'statut' => $statut,
        ]);

        // $inscription = new Inscription();
        //         $inscription->eleve_id = $eleve->id;
        //         $inscription->classe_id = $validatedData['classe_id'];
        //         $inscription->annee_academique = $validatedData['annee_academique'];
        //         $inscription->statut = $validatedData['statut'];
        //         $inscription->save();

        return redirect()->route('eleve.index')->with('success_message', 'L\'élève et son inscription ont été enregistrés avec succès.');
    }
    public function index()
    {
        // $eleves = Eleve::with([
        //     'inscriptions.classe' => function ($query) {
        //         $query->orderBy('annee_academique', 'desc')->limit(1);
        //     },
        // ])->get();
        $totalEleves = inscription::select(\DB::raw('count(DISTINCT eleve_id) as totalEleves'))->first();
        $eleves = Eleve::with('inscriptions.classe')->paginate(10);
        $effectif_total = $eleves->count();
        $inscriptions = Inscription::with('eleve', 'classe')->where('type', 'inscription')->get();
        $garçon = $inscriptions->where('eleve.sexe', 'M')->count();
        $fille = $inscriptions->where('eleve.sexe', 'F')->count();
        $passant = $inscriptions->where('statut', 'Passant(e)')->count();
        $doublant = $inscriptions->where('statut', 'Doublant(e)')->count();

        return view('eleve.index', compact('eleves', 'effectif_total', 'garçon', 'fille', 'inscriptions', 'passant', 'doublant', 'totalEleves'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        // Comparaison insensible à la casse des rôles
        $role = strtolower($user->role ?? '');

        if ($role === 'admin') {
            // $eleves = Eleve::with('inscriptions.classe')->get();
            // $eleves = Eleve::with('inscriptions.classe')->get();
            $classes = classe::all();
            $annees = Annee_academique::all();
            $eleve = Eleve::findOrfail($id);
            $inscription = Inscription::where('eleve_id', $id)->latest()->first();
            $statuts = ['Passant(e)', 'Doublant(e)'];
            // $annees = $inscription->annee_academique;
            return view('eleve.edit', compact('eleve', 'classes', 'inscription', 'statuts', 'annees'));
        } else {
            return redirect()->back()->with('error_message', 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }
    }
    public function update(Request $request, $id)
    {
        //  Metrre a jour
        try {
            $validatedData = $request->validate([
                'matricule_educ_master' => 'required|string|max:13',
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'date_naissance' => 'required|date',
                'lieu_de_naissance' => 'required|string|max:255',
                'sexe' => 'required|string',
                'statut' => 'required|string|max:15',
                'type' => 'required|string|max:15',
                'aptitude_sport' => 'required|string|max:3',
                'nationalite' => 'required|string|max:255',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'email_parent' => 'required|string|max:255',
                'contact_parent' => 'required|string|max:255',
                'classe_id' => 'required|exists:classes,id',
                'annee_academique_id' => 'required|exists:annee_academiques,id',
            ]);
            // $lastEleveId = Eleve::max('id');
            // $matricule_eleve = str_pad($lastEleveId + 1000, 4, '0', STR_PAD_LEFT);

            $eleve = Eleve::findOrFail($id);
            // $eleve->matricule_eleve = $matricule_eleve;
            $eleve->matricule_educ_master = $request->input('matricule_educ_master');
            $eleve->nom = $request->input('nom');
            $eleve->prenom = $request->input('prenom');
            $eleve->aptitude_sport = $request->input('aptitude_sport');
            $eleve->date_naissance = $request->input('date_naissance');
            $eleve->sexe = $request->input('sexe');
            $eleve->lieu_de_naissance = $request->input('lieu_de_naissance');
            $eleve->nationalite = $request->input('nationalite');
            $eleve->email_parent = $request->input('email_parent');
            $eleve->contact_parent = $request->input('contact_parent');

            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($eleve->photo) {
                    Storage::delete('public/' . $eleve->photo);
                }

                // Stocker la nouvelle photo
                $photoPath = $request->file('photo')->store('photos', 'public');
                $eleve->photo = $photoPath;
            }
            $eleve->save();

            // $inscriptionEx = Inscription::findOrFail($id);

            $inscription = Inscription::where('eleve_id', $eleve->id)->first();

            $annee_academique_id = $request->input('annee_academique_id');
            // $inscription = Inscription::where('eleve_id', $eleve->id)
            //     ->where('annee_academique_id', $annee_academique_id)
            //     ->first();

            // if ($inscription && $inscription->id !== $inscriptionEx->id) {
            //     return redirect()->back()->with('error_message', 'Cet élève est déjà inscrit pour cette année académique.');
            // }

            // if (!$inscription) {
            //     // Création d'une nouvelle inscription si elle n'existe pas
            //     $inscription = new Inscription();
            //     $inscription->eleve_id = $eleve->id;
            //     $inscription->annee_academique_id = $request->input('annee_academique_id');
            // }

            $ancienneInscription = Inscription::where('eleve_id', $request->eleve_id)->where('classe_id', $request->classe_id)->first();

            // Si l'élève a déjà été inscrit dans cette classe, on force le statut à doublant
            if ($ancienneInscription) {
                $statut = 'Doublant(e)';
            } else {
                // Si c'est une nouvelle classe, utiliser le statut fourni (passant ou doublant)
                $statut = $request->statut;
            }

            // Mise à jour des champs de l'inscription
            $inscription->classe_id = $request->input('classe_id');
            $inscription->statut = $statut;
            $inscription->type = $request->input('type');
            $inscription->annee_academique_id = $annee_academique_id;
            // $inscription->eleve_id = $request->input('eleve_id');

            // Sauvegarde de l'inscription
            $inscription->save();
            return redirect()->route('eleve.index')->with('success_message', 'Elève mise à jour.');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function reinscription($eleve_id)
    {
        $eleve = Eleve::findOrFail($eleve_id);
        $classes = classe::all();
        $annees = Annee_academique::all();
        return view('eleve.reinscription', compact('eleve', 'classes', 'annees'));
    }

    public function reinscrireEleve(Request $request, $eleve_id)
    {
        $validatedData = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'statut' => 'required|string|max:15',
            'type' => 'required|string|max:15',
            'classe_id' => 'required|exists:classes,id',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
        ]);

        // Vérifier si l'élève a déjà été inscrit dans cette classe
        $ancienneInscription = Inscription::where('eleve_id', $request->eleve_id)->where('classe_id', $request->classe_id)->first();

        // Si l'élève a déjà été inscrit dans cette classe, on force le statut à doublant
        if ($ancienneInscription) {
            $statut = 'Doublant(e)';
        } else {
            // Si c'est une nouvelle classe, utiliser le statut fourni (passant ou doublant)
            $statut = $request->statut;
        }

        // Récupération de l'élève
        $eleve = Eleve::findOrFail($eleve_id);

        $existingInscription = Inscription::where('eleve_id', $request->eleve_id)->where('annee_academique_id', $request->annee_academique_id)->first();

        if ($existingInscription) {
            return redirect()->back()->with('error_message', 'Cet élève est déjà inscrit pour cette année académique.');
        }

        Inscription::create([
            'eleve_id' => $validatedData['eleve_id'],
            'classe_id' => $validatedData['classe_id'],
            'annee_academique_id' => $validatedData['annee_academique_id'],
            'statut' => $statut,
            'type' => $validatedData['type'],
        ]);

        // Récupération de la dernière inscription de l'élève
        // $derniereInscription = Inscription::where('eleve_id', $eleve->id)
        //     ->orderBy('annee_academique', 'desc')
        //     ->first();

        // // Vérification si l'élève reste dans la même classe
        // if ($derniereInscription && $derniereInscription->classe) {
        //     // Si l'élève est dans la même classe, forcer le statut à "doublant"
        //     // $statut = 'doublant';
        //     $derniereClasse = $derniereInscription->classe;
        //     if ($request->input('classe_id') == $derniereInscription->classe->id) {
        //         $statut = 'doublant';
        //     }
        // } else {
        //     // Sinon, utiliser le statut du formulaire
        //     $statut = $request->input('statut');
        // }

        // Création de la nouvelle inscription pour l'année académique
        // $inscription = new Inscription();
        // $inscription->eleve_id = $eleve->id;
        // $inscription->annee_academique = $request->input('annee_academique');
        // $inscription->classe_id = $request->input('classe_id');
        // $inscription->statut = $request->input('statut');

        // // Sauvegarde de la nouvelle inscription
        // $inscription->save();

        return redirect()->route('eleve.listeReinscription')->with('success_message', 'Réinscription réussie pour l\'élève.');
    }

    public function listeReinscription()
    {
        $reinscriptions = Inscription::with('eleve', 'classe')->where('type', 'reinscription')->get();
        return view('eleve.listeReinscription', compact('reinscriptions'));
    }

    public function afficherFormulaiRerecherce()
    {
        $classes = classe::all();
        $annees = Annee_academique::all();
        return view('classe.liste', compact('classes', 'annees'));
    }

    public function afficherEleve(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
        ]);

        $user = auth()->user(); // Récupérer l'utilisateur connecté
        $classe_id = $request->classe_id;
        $annee_academique_id = $request->annee_academique_id;

        // Vérifier si l'utilisateur est un administrateur
        if ($user->role === 'Admin') {
            // L'admin voit tout
            $inscriptions = Inscription::with('eleve', 'classe')->where('classe_id', $classe_id)->where('annee_academique_id', $annee_academique_id)->get();
        } elseif ($user->role === 'enseignant') {
            // Vérifier si l'enseignant est assigné à cette classe et cette année
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            if (!$enseignant) {
                return redirect()->back()->with('error_message', 'Vous n’êtes pas autorisé à voir ces élèves.');
            }

            // Vérifier si l'enseignant est assigné à la classe et à l'année académique demandée
            $estAssigne = $enseignant->classes()->wherePivot('annee_academique_id', $annee_academique_id)->where('classes.id', $classe_id)->exists();

            if (!$estAssigne) {
                return redirect()->back()->with('error_message', 'Vous ne pouvez voir que les élèves de vos classes assignées.');
            }

            // Si l'enseignant est bien assigné, récupérer les élèves de cette classe
            $inscriptions = Inscription::with('eleve', 'classe')->where('classe_id', $classe_id)->where('annee_academique_id', $annee_academique_id)->get();
        } else {
            return redirect()->back()->with('error_message', 'Accès refusé.');
        }

        // Récupérer toutes les classes et années académiques pour le formulaire
        $classes = Classe::all();
        $annees = Annee_academique::all();

        return view('classe.liste', compact('inscriptions', 'classes', 'annees'));
    }

    // public function afficherEleve(Request $request)
    // {
    //     $request->validate([
    //         'classe_id' => 'required|exists:classes,id',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //     ]);

    //     $classe_id = $request->classe_id;
    //     $annee_academique_id = $request->annee_academique_id;

    //     // $inscription = Inscription::where('classe_id', $classe_id)->with('eleve')->get();
    //     $inscriptions = inscription::with('eleve', 'classe')->where('classe_id', $classe_id)->where('annee_academique_id', $annee_academique_id)->get();
    //     $classes = classe::all();
    //     $annees = Annee_academique::all();

    //     // $totalEleves=inscription::select(\DB::raw('count(DISTINCT eleve_id) as total_eleves'))->first;

    //     // $statistiques = Inscription::where('classe_id',$classe_id)->select('annee_academique_id',\DB::raw('count(DISTINCT eleve_id)as total_eleves'))->groupBy('annee_academique_id')->with('annee_academique')->get();
    //     // $elevesParClasse = Inscription::where('classe_id', 'annee_academique_id', \DB::raw('count(DISTINCT eleve_id)as total_eleves'))->groupBy('classe_id', 'annee_academique_id')->with('classe', 'annee_academique')->get();
    //     // $classe = classe::findOrFail($classe_id);
    //     return view('classe.liste', compact('inscriptions', 'classes', 'annees'));
    // }
}
