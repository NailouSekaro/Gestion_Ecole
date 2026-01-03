<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\support\Facades\Session;
use App\Models\Absence;
use App\Models\Annee_academique;
use App\Models\EmploiTemps;
use App\Models\Classe;
use Illuminate\Support\Facades\DB;
use App\Models\Enseignant;

class AppController extends Controller {

    // public function index() {
    //     $user = auth()->user();

    //     if ( Auth::user()->password_changed_at === null ) {
    //         return redirect( route( 'change-password' ) );
    //     } else {
    //         return view( 'dashboard' );
    //     }
    // }

    // public function index() {
    //     $user = auth()->user();

    //     if ( $user->password_changed_at === null ) {
    //         return redirect()->route( 'change-password' );
    //     }

    //     $data = [];

    //     $anneeActive = Annee_Academique::where( 'statut', 'active' )->first();

    //     if ( $user->role === 'Admin' ) {
    //         // Stats globales pour admins
    //         $data[ 'total_classes' ] = Classe::count();
    //         $data[ 'total_enseignants' ] = User::where( 'role', 'enseignant' )->count();
    //         $data[ 'total_emplois' ] = EmploiTemps::count();
    //         $data[ 'recent_emplois' ] = EmploiTemps::with( [ 'classe', 'annee_Academique' ] )
    //         ->orderBy( 'created_at', 'desc' )
    //         ->take( 5 )
    //         ->get();
    //     } elseif ( $user->role === 'enseignant' ) {
    //         // Trouver l'enseignant lié à l'utilisateur
    //         $enseignant = Enseignant::where( 'user_id', $user->id )->first();

    //         if ( !$enseignant ) {
    //             $data[ 'emplois' ] = collect( [] );
    //             $data[ 'total_cours' ] = 0;
    //             $data[ 'annee_active' ] = $anneeActive ? $anneeActive->annee : 'Aucune année active';
    //         } else {
    //             // Classes assignées via la relation classes
    //             $classesAssignees = $enseignant->classes()
    //             ->wherePivot( 'annee_academique_id', $anneeActive->id ?? null )
    //             ->pluck( 'classes.id' );

    //             // Emplois du temps pour ces classes
    //             $data[ 'emplois' ] = EmploiTemps::whereIn( 'classe_id', $classesAssignees )
    //             ->where( 'annee_academique_id', $anneeActive->id ?? null )
    //             ->with( [ 'classe', 'matiere', 'annee_Academique' ] )
    //             ->orderBy( 'jour' )
    //             ->orderBy( 'heure_debut' )
    //             ->get()
    //             ->groupBy( 'jour' );

    //             $data[ 'total_cours' ] = $data[ 'emplois' ]->flatten()->count();
    //             $data[ 'annee_active' ] = $anneeActive ? $anneeActive->annee : 'Aucune année active';

    //             // Stats supplémentaires ( ex. : absences dans leurs classes )
    //             $data[ 'total_absences' ] = \App\Models\Absence::whereIn( 'classe_id', $classesAssignees )
    //             ->where( 'annee_academique_id', $anneeActive->id ?? null )
    //             ->count();
    //             $data[ 'absencesParJour' ] = \App\Models\Absence::whereIn( 'classe_id', $classesAssignees )
    //             ->where( 'annee_academique_id', $anneeActive->id ?? null )
    //             ->groupBy( 'date_absence' )
    //             ->select( 'date_absence', DB::raw( 'count(*) as total' ) )
    //             ->get();

    //         }
    //     }

    //     return view( 'dashboard', $data );
    // }


    public function index()
    {
        $user = auth()->user();

        // forcer le changement de mot de passe si nécessaire
        if ($user->password_changed_at === null) {
            return redirect()->route('change-password');
        }

        $data = [];
        $anneeActive = Annee_Academique::where('statut', 'active')->first();

        // Comparaison insensible à la casse des rôles
        $role = strtolower($user->role ?? '');

        if ($role === 'admin' || $role === 'administrateur' || $role === 'Admin') {
            // -------------------------
            // VUE ADMIN — statistiques globales
            // -------------------------
            $data['total_classes'] = Classe::count();
            $data['total_enseignants'] = Enseignant::count();
            $data['total_emplois'] = EmploiTemps::count();

            $data['recent_emplois'] = EmploiTemps::with(['classe', 'annee_Academique'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

        } elseif ($role === 'enseignant' || $role === 'teacher') {
            // -------------------------
            // VUE ENSEIGNANT — limiter aux classes et cours qui lui sont réellement assignés
            // -------------------------
            $enseignant = Enseignant::where('user_id', $user->id)->first();

            $data['annee_active'] = $anneeActive ? $anneeActive->annee : 'Aucune année active';

            if (!$enseignant) {
                // enseignant non lié : pas de données
                $data['emplois'] = collect([]);
                $data['total_cours'] = 0;
                $data['total_absences'] = 0;
                $data['absencesParJour'] = collect([]);
            } else {
                // --- Récupérer les IDs de classes assignées à cet enseignant pour l'année active
                // On suppose une table pivot `enseignant_classe` avec colonnes:
                // enseignant_id, classe_id, annee_academique_id
                $classesAssignees = DB::table('enseignant_classe')
                    ->where('enseignant_id', $enseignant->id)
                    ->when($anneeActive, function ($q) use ($anneeActive) {
                        $q->where('annee_academique_id', $anneeActive->id);
                    })
                    ->pluck('classe_id')
                    ->toArray();

                // Si l'enseignant n'a aucune classe assignée -> retourner vide
                if (empty($classesAssignees)) {
                    $data['emplois'] = collect([]);
                    $data['total_cours'] = 0;
                    $data['total_absences'] = 0;
                    $data['absencesParJour'] = collect([]);
                } else {
                    // --- Filtrer les emplois du temps pour :
                    //     - les classes assignées
                    //     - l'année académique active
                    //     - la/les matières que l'enseignant enseigne (si applicable)
                    // NOTE: si un enseignant peut enseigner plusieurs matières, il faudra adapter.
                    $query = EmploiTemps::whereIn('classe_id', $classesAssignees)
                        ->when($anneeActive, function ($q) use ($anneeActive) {
                            $q->where('annee_academique_id', $anneeActive->id);
                        });

                    // Si la table Enseignant possède matiere_id (enseignant -> matière unique),
                    // on filtre pour que l'enseignant voie seulement ses matières.
                    if (!empty($enseignant->matiere_id)) {
                        $query->where('matiere_id', $enseignant->matiere_id);
                    }
                    // Sinon, si tu gères plusieurs matières pour un enseignant, ce bloc ci-dessus
                    // serait remplacé par un whereIn sur un tableau d'IDs de matières.

                    $emplois = $query->with(['classe', 'matiere', 'annee_Academique'])
                        ->orderBy('jour')
                        ->orderBy('heure_debut')
                        ->get()
                        ->groupBy('jour');

                    $data['emplois'] = $emplois;
                    $data['total_cours'] = $emplois->flatten()->count();

                    // Statistiques d'absences limitées aux classes assignées et année active
                    $data['total_absences'] = \App\Models\Absence::whereIn('classe_id', $classesAssignees)
                        ->when($anneeActive, function ($q) use ($anneeActive) {
                            $q->where('annee_academique_id', $anneeActive->id);
                        })
                        ->count();

                    $data['absencesParJour'] = \App\Models\Absence::whereIn('classe_id', $classesAssignees)
                        ->when($anneeActive, function ($q) use ($anneeActive) {
                            $q->where('annee_academique_id', $anneeActive->id);
                        })
                        ->groupBy('date_absence')
                        ->select('date_absence', DB::raw('count(*) as total'))
                        ->get();
                }
            }
        } else {
            // rôle non prévu — afficher une vue minimale
            $data['emplois'] = collect([]);
            $data['annee_active'] = $anneeActive ? $anneeActive->annee : 'Aucune année active';
        }

        return view('dashboard', $data);
    }



    public function updatePassword( Request $request ) {
        $request->validate( [
            'new-password' => 'required|min:8|confirmed',
        ] );
        $user = User::find( Auth::id() );

        if ( !$user ) {
            return redirect()->route( 'login' )->with( 'error_message', 'Utilisateur non authentifié' );
        }

        $user->password = Hash::make( $request->input( 'new-password' ) );
        $user->password_changed_at = now();
        // Mettez à jour la date du changement de mot de passe
        $user->save();

        return redirect( route( 'dashboard' ) )->with( 'success_message', 'Votre mot de passe a été mis à jour.' );
    }

    public function showChangePasswordForm() {
        return view( 'enseignant.changer_password' );
        // Remplacez par le nom de votre vue
    }
}
