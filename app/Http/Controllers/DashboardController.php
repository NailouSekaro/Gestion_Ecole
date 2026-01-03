<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\Annee_academique;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {
    public function index() {

        $user = auth()->user();

        // Comparaison insensible à la casse des rôles
        $role = strtolower( $user->role ?? '' );

        if ( $role === 'admin' ) {

            $currentDate = now()->format( 'd/m/Y' );
            // Date actuelle

            // Statistiques générales
            $nombreEleves = Eleve::count();
            $nombreEnseignants = Enseignant::count();
            $nombreClasses = Classe::count();
            $moyenneNotes = Note::avg( 'valeur_note' );
            $nombreAbsences = Absence::count();
            $totalPaiements = Paiement::sum( 'montant' );

            // Données pour graphs
            $absencesParTrimestre = Absence::groupBy( 'trimestre_id' )->select( 'trimestre_id', DB::raw( 'count(*) as total' ) )->get();
            $paiementsMensuels = Paiement::select( DB::raw( 'MONTH(date_paiement) as mois' ), DB::raw( 'sum(montant) as total' ) )
            ->groupBy( 'mois' )
            ->get();

            // Tableaux récapitulatifs
            $elevesRecents = Eleve::latest()->take( 5 )->get();
            $enseignants = Enseignant::with( 'matiere' )->get();
            $classes = Classe::withCount( 'inscriptions' )->get();
            $anneeCourante = Annee_academique::latest()->first();

            return view( 'dashboard.index', compact( 'currentDate', 'nombreEleves', 'nombreEnseignants', 'nombreClasses', 'moyenneNotes', 'nombreAbsences', 'totalPaiements', 'absencesParTrimestre', 'paiementsMensuels', 'elevesRecents', 'enseignants', 'classes', 'anneeCourante' ) );

        } else {
            return redirect()->back()->with( 'error_message', 'Vous n\'êtes pas autorisé à effectuer cette action.' );
        }
    }
}
