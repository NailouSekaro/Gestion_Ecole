<?php

namespace App\Http\Controllers;
use App\Models\matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = matiere::paginate(5);
        return view('matiere.index', compact('matieres'));
    }

    public function create()
    {
        return view('matiere.create');
    }

    public function edit(matiere $matiere)
    {
        return view('matiere.edit', compact('matiere'));
    }

    // Interraction BD
    public function store(Request $request)
    {
        // Enregistrer une classe
        $request->validate([
            'nom' => 'required|unique:matieres,nom',
        ]);

        matiere::create($request->all());

        return redirect()->route('matiere.index')->with('success_message', 'Matiere créée avec success.');
    }

    public function update(Request $request, $id)
    {
        //  Metrre a jour
        try {
            $request->validate([
                'nom' => 'required',
                // 'cycle' => 'required',
                // 'frais_scolarite' => 'required|numeric|min:100',
            ]);

            $matiere = matiere::findOrFail($id);
            $matiere->update($request->only(['nom']));
            return redirect()->route('matiere.index')->with('success_message', 'Matiere mise à jour.');
        } catch (Exception $e) {
            dd($e);
        }
    }
}
