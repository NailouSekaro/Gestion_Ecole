<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Annee_academique;
use App\models\trimestre;
use App\Exceptions\InvalidTrimestreDatesException;

class TrimestreController extends Controller
{
    public function create()
    {
        $annees = Annee_academique::all();
        $dateDebutDefault = now()->startOfMonth()->toDateString();
        $dateFinDefault = now()->endOfMonth()->toDateString();
        return view('trimestre.create', compact('annees', 'dateDebutDefault', 'dateFinDefault'));
    }

    // public function creerTrimestres(AnneeAcademique $annee)
    // {
    //     $trimestres = [
    //         ['nom' => 'Trimestre 1', 'date_debut' => $annee->date_debut, 'date_fin' => $annee->date_debut->addMonths(3)],
    //         ['nom' => 'Trimestre 2', 'date_debut' => $annee->date_debut, 'date_fin' => $annee->date_debut->addMonths(3)],
    //         ['nom' => 'Trimestre 3', 'date_debut' => $annee->date_debut, 'date_fin' => $annee->date_debut->addMonths(3)],
    //         // Ajouter les autres trimestres...
    //     ];

    //     foreach ($trimestres as $trimestre) {
    //         Trimestre::create(array_merge($trimestre, ['annee_academique_id' => $annee->id]));
    //     }
    // }

    public function index()
    {
        $trimestres = trimestre::paginate(5);
        return view('trimestre.index', compact('trimestres'));
    }

    public function edit(trimestre $trimestre)
    {
        $annees = Annee_academique::all();
        return view('trimestre.edit', compact('trimestre', 'annees'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'nom' => 'required',
                    'annee_academique_id' => 'required|exists:annee_academiques,id',
                    'date_debut' => 'required|date|unique:trimestres,date_debut',
                    'date_fin' => 'required|date|unique:trimestres,date_fin|after_or_equal:date_debut',
                ],
                [
                    'date_debut.required' => 'La date de début est obligatoire.',
                    'date_fin.required' => 'La date de fin est obligatoire.',
                    'date_fin.after_or_equal' => 'La date de début doit être une date inférieure ou égale à la date de fin.',
                    'date_debut.unique' => 'Cette date appartient déjà à un trimestre',
                    'date_fin.unique' => 'Cette date appartient déjà à un trimestre',
                ],
            );

            // Vérifier si le trimestre précédent a une date de fin
            $dernierTrimestre = trimestre::orderBy('date_fin', 'desc')->first();

            if ($dernierTrimestre && $dernierTrimestre->date_fin->isFuture()) {
                return redirect()->back()->with('error_message', 'Vous ne pouvez pas ajouter un nouveau trimestre tant que la date de fin du trimestre précédent n\'est pas atteinte.');
            }
            trimestre::create($request->all());

            return redirect()->route('trimestre.index')->with('success_message', 'Trimestre créé avec success.');
        } catch (InvalidTrimestreDatesException $e) {
            return redirect()
                ->back()
                ->withErrors(['error_message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error_message' => 'Une erreur inattendue est survenue ']);
        }
    }

    public function update(Request $request, $id)
    {
        //  Metrre a jour
        try {
            $request->validate(
                [
                    'nom' => 'required',
                    'annee_academique_id' => 'required|exists:annee_academiques,id',
                    'date_debut' => 'required|date',
                    'date_fin' => 'required|date|after_or_equal:date_debut',
                ],
                [
                    'date_debut.required' => 'La date de début est obligatoire.',
                    'date_fin.required' => 'La date de fin est obligatoire.',
                    'date_fin.after_or_equal' => 'La date de début doit être une date inférieure ou égale à la date de fin.',
                ],
                // 'cycle' => 'required',
                // 'frais_scolarite' => 'required|numeric|min:100',
            );

            $trimestre = trimestre::findOrFail($id);
            $trimestre->update($request->only(['nom', 'annee_academique_id', 'date_debut', 'date_fin']));
            return redirect()->route('trimestre.index')->with('success_message', 'Trimestre mise à jour.');
        } catch (InvalidTrimestreDatesException $e) {
            return redirect()
                ->back()
                ->withErrors(['error_message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error_message' => 'Une erreur inattendue est survenue.']);
        }
    }
}
