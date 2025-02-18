<?php

namespace App\Http\Controllers;

use App\Models\Annee_academique;
use Illuminate\Http\Request;

class anneeAcademiqueController extends Controller
{
    public function index()
    {
        $annees = Annee_academique::paginate(5);
        return view('annee.index', compact('annees'));
    }

    public function create()
    {
        $dateDebutDefault = now()->startOfMonth()->toDateString();
        $dateFinDefault = now()->endOfMonth()->toDateString();

        return view('annee.create', compact('dateDebutDefault', 'dateFinDefault'));
    }

    public function edit(Annee_academique $annee_academique)
    {
        return view('annee.edit', compact('annee_academique'));
    }

    // Interraction BD
    public function store(Request $request)
    {
        // Enregistrer une classe
        $request->validate(
            [
                'annee' => 'required|unique:annee_academiques,annee',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
            ],
            [
                'date_debut.required' => 'La date de début est obligatoire.',
                'date_fin.required' => 'La date de fin est obligatoire.',
                'date_fin.after_or_equal' => 'La date de début doit être une date inférieure ou égale à la date de fin.',
            ],
        );

        // Vérifier si l'année académique précédente a une date de fin
        $derniereAnnee = Annee_academique::orderBy('date_fin', 'desc')->first();

        if ($derniereAnnee && $derniereAnnee->date_fin->isFuture()) {
            return redirect()->back()->with('error_message', 'Vous ne pouvez pas ajouter une nouvelle année académique tant que la date de fin de l\'année précédente n\'est pas atteinte.');
        }

        Annee_academique::create($request->all());

        return redirect()->route('annee.index')->with('success_message', 'Année académique créée avec success.');
    }

    // public function update(Request $request, $id)
    // {
    //     //  Metrre a jour
    //     try {
    //         $request->validate(
    //             [
    //                 'annee' => 'required',
    //                 'date_debut' => 'required|date',
    //                 'date_fin' => 'required|date|after_or_equal:date_debut',
    //             ],
    //             [
    //                 'date_debut.required' => 'La date de début est obligatoire.',
    //                 'date_fin.required' => 'La date de fin est obligatoire.',
    //                 'date_fin.after_or_equal' => 'La date de début doit être une date inférieure ou égale à la date de fin.',

    //                 // 'cycle' => 'required',
    //                 // 'frais_scolarite' => 'required|numeric|min:100',
    //             ],
    //         );

    //         $annee = Annee_academique::findOrFail($id);
    //         $annee->update($request->only(['annee', 'date_debut', 'date_fin']));
    //         return redirect()->route('annee.index')->with('success_message', 'Année académique mise à jour.');
    //     } catch (Exception $e) {
    //         dd($e);
    //     }
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        $annee_academique = Annee_academique::findOrFail($id);
        $annee_academique->update([
            'date_debut' => $request->input('date_debut'),
            'date_fin' => $request->input('date_fin'),
        ]);

        return redirect()->route('annee.index')->with('success_message', 'Année académique mise à jour avec succès.');
    }
}
