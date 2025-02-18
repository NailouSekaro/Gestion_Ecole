<?php

namespace App\Http\Controllers;
use App\Models\classe;
use App\Models\Eleve;
use App\Models\Annee_academique;
use App\Models\Inscription;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    public function index()
    {
        $classes = classe::paginate(5);
        return view('classe.index', compact('classes'));
    }

    public function create()
    {
        return view('classe.create');
    }

    public function edit(classe $classe)
    {
        return view('classe.edit', compact('classe'));
    }

    // Interraction BD
    public function store(Request $request)
    {
        // Enregistrer une classe
        $request->validate([
            'nom_classe' => 'required|unique:classes,nom_classe',
            'cycle' => 'required',
            'frais_scolarite' => 'required|numeric|min:100',
        ]);

        classe::create($request->all());

        return redirect()->route('classe.index')->with('success_message', 'Classe créée avec success.');
    }

    public function update(Request $request, $id)
    {
        //  Metrre a jour
        try {
            $request->validate([
                'nom_classe' => 'required',
                'cycle' => 'required',
                'frais_scolarite' => 'required|numeric|min:100',
            ]);

            $classe = classe::findOrFail($id);
            $classe->update($request->only(['nom_classe', 'cycle', 'frais_scolarite']));
            return redirect()->route('classe.index')->with('success_message', 'Classe mise à jour.');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function delete(classe $classe)
    {
        // Enregistrer un nouveau departement
        try {
            $classe->delete();
            return redirect()->route('classe.index')->with('success_message', 'Classe supprimée.');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function liste($classe_id)
    {
        // $classes = classe::findorFail($id);
        // $eleves = Eleve::with('inscriptions.classe')->get();

        // Récupérer les inscriptions pour la classe donnée
        $inscriptions = Inscription::where('classe_id', $classe_id)->with('eleve')->get();
        // $eleve = classe::where('classe_id', $classe_id)->with('eleve')->get();
        // $eleves = inscription::where('classe_id', $classe_id)->where('annee_academique_id', $annee_academique_id)->with('eleve')->get();

        // Calculer l'effectif
        $effectif = $inscriptions->count();
        $garçon = $inscriptions->where('eleve.sexe', 'M')->count();
        $fille = $inscriptions->where('eleve.sexe', 'F')->count();
        $passant = $inscriptions->where('statut', 'Passant(e)')->count();
        $doublant = $inscriptions->where('statut', 'Doublant(e)')->count();
        // $eleve=Eleve::all();

        // Récupérer les informations de la classe
        $classe = Classe::findOrFail($classe_id);

        $classes = classe::all();
        $annees = Annee_academique::all();
        return view('classe.liste', compact('inscriptions', 'classe', 'effectif', 'garçon', 'fille', 'passant', 'doublant',  'classes', 'annees'));
    }
}
