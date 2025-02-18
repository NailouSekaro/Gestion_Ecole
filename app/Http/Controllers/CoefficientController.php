<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\classe;
use App\Models\matiere;
use App\Models\coefficient;

class CoefficientController extends Controller
{
    public function create()
    {
        $classes = classe::all();
        $matieres = matiere::all();
        return view('coefficient.create', compact('classes', 'matieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'valeur_coefficient' => 'required|numeric|min:1',
            'matiere_id' => 'required|exists:matieres,id',
            'classe_id' => 'required|exists:classes,id',
        ]);

        $exists = coefficient::where('classe_id', $request->classe_id)
            ->where('matiere_id', $request->matiere_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error_message', 'Un coefficient avait été déjà ajouté pour la matière de cette classe.');
        }

        $coefficient = coefficient::create([
            'valeur_coefficient' => $validated['valeur_coefficient'],
            'matiere_id' => $validated['matiere_id'],
            'classe_id' => $validated['classe_id'],
        ]);
        return redirect()->route('coefficient.index')->with('success_message', 'Coefficient enregistré avec succès pour la matiere de cette classe.');
    }

    public function index()
    {
        $coefficients = coefficient::paginate(5);
        return view('coefficient.index', compact('coefficients'));
    }

    public function edit(coefficient $coefficient)
    {
        $classes = classe::all();
        $matieres = matiere::all();
        return view('coefficient\edit', compact('classes', 'matieres', 'coefficient'));
    }

    public function update(Request $request, $id)
    {
        //  Metrre a jour
        try {
            $request->validate([
                'valeur_coefficient' => 'required',
                'matiere_id' => 'required',
                'classe_id' => 'required',
            ]);

            // $exists = coefficient::where('classe_id', $request->classe_id)
            //     ->where('matiere_id', $request->matiere_id)
            //     ->exists();

            // if ($exists) {
            //     return redirect()->back()->with('error_message', 'Un coefficient avait été déjà ajouté pour la matière de cette classe.');
            // }

            $coefficient = coefficient::findOrFail($id);
            $coefficient->update($request->only(['valeur_coefficient', 'matiere_id', 'classe_id']));
            return redirect()->route('coefficient.index')->with('success_message', 'Coefficient mise à jour.');
        } catch (Exception $e) {
            dd($e);
        }
    }
}
