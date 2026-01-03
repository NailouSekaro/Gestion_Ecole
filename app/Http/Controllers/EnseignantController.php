<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Annee_academique;
use App\Models\ResetCodePassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SendEmailToEnseignantAfterRegistrationNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

class EnseignantController extends Controller
{
    public function create()
    {
        $matieres = matiere::all();
        $classes = classe::all();
        $annees = Annee_academique::all();
        return view('enseignant.create', compact('classes', 'annees', 'matieres'));
    }

    // public function store(Request $request)
    // {
    //     dd( $request->all() );
    //     Validation des données
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'prenom' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:6',
    //         'matricule' => 'required|string|unique:enseignants,matricule',
    //         'telephone' => 'required|string|unique:enseignants,telephone',
    //         'sexe' => 'required|in:M,F',
    //         'diplomes' => 'required|string',
    //         'matiere_id' => 'required|string',
    //         'adresse' => 'required|string',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //         'classe_id' => 'required|array', // Tableau d'IDs de classes
    //         'classe_id.*' => 'exists:classes,id',
    //         'photo' => 'required|image|mimes:jpg, jpeg, png|max:2048',
    //     ]);

    //     // Vérifier si un enseignant est déjà assigné à cette matière, classe et année académique
    //     foreach ($validated['classe_id'] as $classeId) {
    //         $existingAssignment = DB::table('enseignants')->join('enseignant_classe', 'enseignants.id', '=', 'enseignant_classe.enseignant_id')->where('enseignants.matiere_id', $validated['matiere_id'])->where('enseignant_classe.classe_id', $classeId)->where('enseignant_classe.annee_academique_id', $validated['annee_academique_id'])->exists();

    //         if ($existingAssignment) {
    //             return redirect()->back()->with('error_message', 'Pour cette année académique, un enseignant est déjà assigné à cette matière de cette classe. ');
    //         }
    //     }

    //     if ($request->hasFile('photo')) {
    //         $file = $request->file('photo');
    //         $photoPath = $file->store('photos_identite', 'public'); // Sauvegarde dans le dossier 'storage/app/public/photos_identite'
    //     } else {
    //         $photoPath = null; // Si aucune photo n'est téléchargée, définissez $photoPath à null
    //     }

    //     DB::transaction(function () use ($validated, $photoPath) {
    //         // Ajoutez $photoPath ici
    //         // Étape 1 : Création de l'utilisateur
    //         $user = User::create([
    //             'name' => $validated['name'],
    //             'prenom' => $validated['prenom'],
    //             'email' => $validated['email'],
    //             'password' => Hash::make($validated['password']),
    //             // 'password' => Hash::make('default');
    //             'role' => 'enseignant', // Définir le rôle ici
    //             'photo' => $photoPath, // Utilisez $photoPath ici
    //         ]);

    //         // envoyer un code par email pour vérification

    //         if ($user) {
    //             try {
    //                 ResetCodePassword::where('email', $user->email)->delete();
    //                 $code = rand(1000, 4000);

    //                 $data = [
    //                     'code' => $code,
    //                     'email' => $user->email,
    //                 ];

    //                 ResetPassword::create($data);

    //                 Notification::route('email', $user->email)->notify(new SendEmailToEnseignantAfterRegistrationNotification($code ,$user->email));
    //                 // Rediriger l'utilisateur vers une URL
    //                 return redirect()->route('enseignant.index')->whith('success_message', 'Enseignant enregistré avec succès');

    //             } catch (Exception $e) {
    //                 dd($e);
    //                 throw new Exception("Une erreur est survenue lors de l'envoie du mail");
    //             }
    //         }

    //         // Étape 2 : Création de l'enseignant
    //         $enseignant = Enseignant::create([
    //             'user_id' => $user->id,
    //             'matricule' => $validated['matricule'],
    //             'telephone' => $validated['telephone'],
    //             'sexe' => $validated['sexe'],
    //             'matiere_id' => $validated['matiere_id'],
    //             'diplomes' => $validated['diplomes'],
    //             'adresse' => $validated['adresse'],
    //         ]);

    //         // Étape 3 : Assigner les classes à l'enseignant
    //         foreach ($validated['classe_id'] as $classeId) {
    //             DB::table('enseignant_classe')->insert([
    //                 'enseignant_id' => $enseignant->id,
    //                 'classe_id' => $classeId,
    //                 'annee_academique_id' => $validated['annee_academique_id'],
    //             ]);
    //         }
    //     });

    //     return redirect()->route('enseignant.index')->with('success_message', 'Enseignant enregistré avec succès !');
    // }

    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'matricule' => 'required|string|unique:enseignants,matricule',
            'telephone' => 'required|string|unique:enseignants,telephone',
            'sexe' => 'required|in:M,F',
            'diplomes' => 'required|string',
            'matiere_id' => 'required|string',
            'adresse' => 'required|string',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
            'classe_id' => 'required|array', // Tableau d'IDs de classes
            'classe_id.*' => 'exists:classes,id',
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Vérifier si un enseignant est déjà assigné à cette matière, classe et année académique
        foreach ($validated['classe_id'] as $classeId) {
            $existingAssignment = DB::table('enseignants')->join('enseignant_classe', 'enseignants.id', '=', 'enseignant_classe.enseignant_id')->where('enseignants.matiere_id', $validated['matiere_id'])->where('enseignant_classe.classe_id', $classeId)->where('enseignant_classe.annee_academique_id', $validated['annee_academique_id'])->exists();

            if ($existingAssignment) {
                return redirect()->back()->with('error_message', 'Pour cette année académique, un enseignant est déjà assigné à cette matière de cette classe.');
            }
        }

        // Gestion de la photo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoPath = $file->store('photos_identite', 'public'); // Sauvegarde dans le dossier 'storage/app/public/photos_identite'
        } else {
            $photoPath = null; // Si aucune photo n'est téléchargée, définissez $photoPath à null
        }

        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($validated, $photoPath) {
            // Étape 1 : Création de l'utilisateur
            $user = User::create([
                'name' => $validated['name'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'enseignant', // Définir le rôle ici
                'photo' => $photoPath, // Utilisez $photoPath ici
            ]);

            // Étape 2 : Création de l'enseignant
            $enseignant = Enseignant::create([
                'user_id' => $user->id,
                'matricule' => $validated['matricule'],
                'telephone' => $validated['telephone'],
                'sexe' => $validated['sexe'],
                'matiere_id' => $validated['matiere_id'],
                'diplomes' => $validated['diplomes'],
                'adresse' => $validated['adresse'],
            ]);

            // Étape 3 : Assigner les classes à l'enseignant
            foreach ($validated['classe_id'] as $classeId) {
                DB::table('enseignant_classe')->insert([
                    'enseignant_id' => $enseignant->id,
                    'classe_id' => $classeId,
                    'annee_academique_id' => $validated['annee_academique_id'],
                ]);
            }

            // Étape 4 : Générer un code de validation et envoyer un e-mail
            $code = rand(1000, 9999); // Génère un code à 4 chiffres
            ResetCodePassword::updateOrCreate(['email' => $user->email], ['code' => $code]);

            // Envoyer la notification
            $user->notify(new SendEmailToEnseignantAfterRegistrationNotification($code, $user->email));
        });

        // Redirection avec un message de succès
        return redirect()->route('enseignant.index')->with('success_message', 'Enseignant enregistré avec succès ! Un e-mail de validation a été envoyé.');
    }

    public function index()
    {
        // Récupérer les enseignants avec leurs relations
        $enseignants = Enseignant::with(['user', 'classes', 'anneeAcademique', 'matiere'])->paginate(100);

        // Statistiques
        $effectif_total = Enseignant::count();
        $hommes = Enseignant::where('sexe', 'M')->count();
        $femmes = Enseignant::where('sexe', 'F')->count();

        // Retourner les données à la vue
        return view('enseignant.index', compact('enseignants', 'effectif_total', 'hommes', 'femmes'));
    }

    public function edit($id)
    {
        // Récupérer l'enseignant à modifier
        $enseignant = Enseignant::findOrFail($id);

        // Récupérer les données nécessaires pour le formulaire
        $classes = Classe::all();
        $annees = Annee_academique::all();
        $matieres = Matiere::all();
        // Si vous avez une table `matieres`

        // Retourner la vue avec les données
        return view('enseignant.edit', compact('enseignant', 'classes', 'annees', 'matieres'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email', // Ignore l'email actuel de l'enseignant
                'password' => 'nullable|string|min:6', // Mot de passe facultatif
                'matricule' => 'required|string', // Ignore le matricule actuel
                'telephone' => 'required|string', // Ignore le téléphone actuel
                'sexe' => 'required|in:M,F',
                'diplomes' => 'required|string',
                'matiere_id' => 'required|exists:matieres,id',
                'adresse' => 'required|string',
                'annee_academique_id' => 'required|exists:annee_academiques,id',
                'classe_id' => 'required|array', // Tableau d'IDs de classes
                'classe_id.*' => 'exists:classes,id', // Vérifie que chaque ID existe dans la table `classes`
                'photo' => 'nullable|image|mimes:jpg, jpeg, png|max:2048', // Photo facultative
            ]);

            // Récupérer l'enseignant à modifier
            $enseignant = Enseignant::findOrFail($id);

            // Vérifier si un enseignant est déjà assigné à cette matière, classe et année académique
            // foreach ($validatedData['classe_id'] as $classeId) {
            //     $existingAssignment = DB::table('enseignants')->join('enseignant_classe', 'enseignants.id', '=', 'enseignant_classe.enseignant_id')->where('enseignants.matiere_id', $validatedData['matiere_id'])->where('enseignant_classe.classe_id', $classeId)->where('enseignant_classe.annee_academique_id', $validatedData['annee_academique_id'])->exists();

            //     if ($existingAssignment) {
            //         return redirect()->back()->with('error_message', 'Pour cette année académique, un enseignant est déjà assigné à cette matière de cette classe. ');
            //     }
            // }

            // Mettre à jour les données de l'utilisateur associé
            $user = $enseignant->user;
            $user->name = $request->input('name');
            $user->prenom = $request->input('prenom');
            $user->email = $request->input('email');
            if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password')); // Mettre à jour le mot de passe si fourni
            }

            // Gestion de la photo
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($user->photo) {
                    Storage::delete('public/' . $user->photo);
                }

                // Stocker la nouvelle photo
                $photoPath = $request->file('photo')->store('photos_identite', 'public');
                $user->photo = $photoPath;
            }

            $user->save();

            // Mettre à jour les données de l'enseignant
            $enseignant->matricule = $request->input('matricule');
            $enseignant->telephone = $request->input('telephone');
            $enseignant->sexe = $request->input('sexe');
            $enseignant->diplomes = $request->input('diplomes');
            $enseignant->matiere_id = $request->input('matiere_id');
            $enseignant->adresse = $request->input('adresse');
            $enseignant->save();

            // Mettre à jour les classes assignées à l'enseignant
            // $enseignant->classes()->sync( $request->input( 'classe_id' ) );

            // Mettre à jour les classes assignées à l'enseignant avec l'année académique
            DB::transaction(function () use ($enseignant, $validatedData) {
                // Supprimer les anciennes associations pour cet enseignant et cette année académique
                DB::table('enseignant_classe')->where('enseignant_id', $enseignant->id)->where('annee_academique_id', $validatedData['annee_academique_id'])->delete();

                // Ajouter les nouvelles associations
                foreach ($validatedData['classe_id'] as $classeId) {
                    DB::table('enseignant_classe')->insert([
                        'enseignant_id' => $enseignant->id,
                        'classe_id' => $classeId,
                        'annee_academique_id' => $validatedData['annee_academique_id'],
                    ]);
                }
            });

            // Redirection avec un message de succès
            return redirect()->route('enseignant.index')->with('success_message', 'Enseignant mis à jour avec succès !');
        } catch (Exception $e) {
            dd($e);
            // En cas d'erreur, rediriger avec un message d'erreur
            return redirect()->back()->with('error_message', 'Une erreur s\'est produite lors de la mise à jour de l\'enseignant.');
        }
    }

    public function validateAccount($email)
    {
        return view('enseignant.validate-account', ['email' => $email]);
    }

    public function validateAccountSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric',
        ]);

        $codeEntry = ResetCodePassword::where('email', $request->email)->where('code', $request->code)->first();

        if ($codeEntry) {
            // Valider le compte de l'utilisateur
            $user = User::where('email', $request->email)->first();
            $user->email_verified_at = now();
            $user->save();

            // Supprimer le code utilisé
            $codeEntry->delete();

            return redirect()->route('login')->with('success_message', 'Votre compte a été validé avec succès !');
        } else {
            return redirect()->back()->with('error_message', 'Code de validation invalide.');
        }
    }

    // public function defineAccess($email){
    //     $checkUserExist = user::('email',$email)->first();
    //     if ($checkUserExist) {
    //       return view('enseignant.validate-account', compact('email'));
    //     }else{
    //         // return rediret()- route('login');
    //     }

    // }
}
