<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Etablissement;
use Exception;

class EtablissementController extends Controller
{
        public function getEtablissementInfo(Request $request)
    {
        $userId = auth()->user()->id;
        $etablissement = Etablissement::where('user_id', $userId)->first();

        return view('etablissement.create', compact('etablissement'));
    }

    public function handleUpdateEtablissement(Request $request)
    {
        DB::beginTransaction();

        $request->validate(
            [
                'nom' => 'required|string|max:255',
                'republique' => 'required|string|max:255',
                'contact' => 'required|string|max:255',
                'devise' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            ],
            [
                'nom.required' => 'Le nom de l\'établissement est requis',
                'republique.required' => 'La république est requise',
                'contact.required' => 'Le contact est requis',
                'devise.required' => 'La devise est requise',
                'logo.image' => 'Le logo doit être une image valide',
            ]
        );

        try {
            $userId = auth()->user()->id;
            $etablissement = Etablissement::where('user_id', $userId)->first();

            // Gestion du logo
            $logoPath = $etablissement ? $etablissement->logo : null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            if ($etablissement) {
                $etablissement->update([
                    'nom' => $request->nom,
                    'republique' => $request->republique,
                    'contact' => $request->contact,
                    'devise' => $request->devise,
                    'logo' => $logoPath,
                ]);
            } else {
                Etablissement::create([
                    'user_id' => $userId,
                    'nom' => $request->nom,
                    'republique' => $request->republique,
                    'contact' => $request->contact,
                    'devise' => $request->devise,
                    'logo' => $logoPath,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success_message', 'Etablissement enregistré avec succès');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error_message', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

}
