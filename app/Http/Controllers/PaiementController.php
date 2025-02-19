<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Annee_academique;
use App\Models\Inscription;
use PDF;
use Mail;
use App\Mail\RecuPaiement;
use App\Models\Paiement;
use App\Models\Classe;
use App\Models\User;
// use FedaPay\Transaction;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Exception;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    // public function enregistrerPaiement(Request $request)
    // {
    //     // Validation des données
    //     $validated = $request->validate([
    //         'inscription_id' => 'required|exists:inscriptions,id',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //         'montant' => 'required|numeric|min:0',
    //         'date_paiement' => 'required|date',
    //     ]);

    //     // Enregistrement du paiement
    //     Paiement::create([
    //         'inscription_id' => $validated['inscription_id'],
    //         'annee_academique_id' => $validated['annee_academique_id'],
    //         'montant' => $validated['montant'],
    //         'date_paiement' => $validated['date_paiement'],
    //     ]);

    //     return redirect()->back()->with('success_messages', 'Paiement enregistré avec succès.');
    // }

    // public function suiviPaiement($eleve_id, $annee_academique_id)
    // {
    //     // 🔹 Récupérer l'inscription de l'élève pour l'année académique donnée
    //     $inscription = Inscription::where('eleve_id', $eleve_id)->where('annee_academique_id', $annee_academique_id)->firstOrFail();

    //     // 🔹 Récupérer les frais de scolarité de sa classe
    //     $fraisScolarite = $inscription->classe->frais_scolarite;

    //     // 🔹 Calculer le montant total déjà payé par l'élève
    //     $montantPaye = Paiement::where('inscription_id', $inscription->id)->where('annee_academique_id', $annee_academique_id)->sum('montant');

    //     // 🔹 Calculer le montant restant à payer
    //     $resteAPayer = max(0, $fraisScolarite - $montantPaye);

    //     // 🔹 Déterminer si l'élève a soldé sa scolarité
    //     $scolariteSoldee = $resteAPayer == 0;
    //     $statut = $scolariteSoldee ? '✅ Scolarisé' : "❌ En attente de paiement ({$resteAPayer} FCFA restants)";

    //     // 🔹 Message de succès si l'élève vient de solder
    //     if (session()->has('paiement_effectue') && $scolariteSoldee) {
    //         session()->flash('success', "Félicitations ! L'élève a entièrement soldé sa scolarité pour cette année académique. 🎉");
    //     }

    //     return view('scolarite.suivi', compact('inscription', 'fraisScolarite', 'montantPaye', 'resteAPayer', 'statut', 'scolariteSoldee'));
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'inscription_id' => 'required|exists:inscriptions,id',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //         'montant' => 'required|numeric|min:1',
    //         'moyen_paiement' => 'required|in:espece,mobile_money',
    //         'transaction_id' => '|string', // ID pour les paiements Kkiapay
    //     ]);

    //     // Si c'est un paiement mobile, vérifier que l'ID de transaction est bien fourni
    //     if ($request->moyen_paiement === 'mobile_money') {
    //         if (empty($request->transaction_id)) {
    //             return redirect()->back()->with('error_message', 'Le paiement mobile doit avoir un ID de transaction.');
    //         }
    //     } else {
    //         // Si c'est un paiement en espèces, on met transaction_id à null
    //         $request->merge(['transaction_id' => null]);
    //     }

    //     $inscription = Inscription::findOrFail($request->inscription_id);
    //     $fraisScolarite = $inscription->classe->frais_scolarite;

    //     $montantPaye = Paiement::where('inscription_id', $inscription->id)->where('annee_academique_id', $request->annee_academique_id)->sum('montant');

    //     $resteAPayer = max(0, $fraisScolarite - $montantPaye);

    //     if ($resteAPayer == 0) {
    //         return redirect()->back()->with('error_message', 'Cet élève a déjà soldé sa scolarité !');
    //     }

    //     // Si c'est un paiement mobile, vérifier l'ID de transaction
    //     // if ($request->moyen_paiement === 'mobile_money' && empty($request->transaction_id)) {
    //     //     return redirect()->back()->with('error_message', 'Le paiement mobile doit avoir un ID de transaction.');
    //     // }

    //     // Enregistrer le paiement
    //     $paiement = new Paiement();
    //     $paiement->inscription_id = $request->inscription_id;
    //     $paiement->annee_academique_id = $request->annee_academique_id;
    //     $paiement->montant = $request->montant;
    //     $paiement->moyen_paiement = $request->moyen_paiement;
    //     $paiement->transaction_id = $request->transaction_id;
    //     $paiement->date_paiement = now();
    //     $paiement->save();

    //     $montantPaye += $request->montant;
    //     if ($montantPaye >= $fraisScolarite) {
    //         return redirect()
    //             ->route('scolarite.suivi', [$inscription->eleve_id, $request->annee_academique_id])
    //             ->with('paiement_effectue', true);
    //     }

    //     return redirect()
    //         ->route('scolarite.suivi', [$inscription->eleve_id, $request->annee_academique_id])
    //         ->with('success', 'Paiement enregistré avec succès !');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'inscription_id' => 'required|exists:inscriptions,id',
    //         'annee_academique_id' => 'required|exists:annee_academiques,id',
    //         'montant' => 'required|numeric|min:1',
    //         'moyen_paiement' => 'required|in:espece,mobile_money',
    //         'transaction_id' => 'nullable|string', // ID pour les paiements mobile_money
    //     ]);

    //     // Si c'est un paiement mobile, vérifier l'ID de transaction
    //     // if ($request->moyen_paiement === 'mobile_money' && empty($request->transaction_id)) {
    //     //     return redirect()->back()->with('error_message', 'Le paiement mobile doit avoir un ID de transaction.');
    //     // }

    //     // Si c'est un paiement en espèces, forcer transaction_id à null
    //     if ($request->moyen_paiement === 'espece') {
    //         $request->merge(['transaction_id' => null]);
    //     }

    //     $inscription = Inscription::findOrFail($request->inscription_id);
    //     $fraisScolarite = $inscription->classe->frais_scolarite;

    //     $montantPaye = Paiement::where('inscription_id', $inscription->id)->where('annee_academique_id', $request->annee_academique_id)->sum('montant');

    //     $resteAPayer = max(0, $fraisScolarite - $montantPaye);

    //     if ($resteAPayer == 0) {
    //         return redirect()->back()->with('error_message', 'Cet élève a déjà soldé sa scolarité !');
    //     }

    //     // Enregistrer le paiement
    //     $paiement = new Paiement();
    //     $paiement->inscription_id = $request->inscription_id;
    //     $paiement->annee_academique_id = $request->annee_academique_id;
    //     $paiement->montant = $request->montant;
    //     $paiement->moyen_paiement = $request->moyen_paiement;
    //     $paiement->transaction_id = $request->transaction; // Sera null pour un paiement en espèces
    //     $paiement->user_id = auth()->id();
    //     $paiement->date_paiement = now();
    //     $paiement->save();

    //     $montantPaye += $request->montant;
    //     if ($montantPaye >= $fraisScolarite) {
    //         return redirect()
    //             ->route('paiement.index', [$inscription->eleve_id, $request->annee_academique_id])
    //             ->with('paiement_effectue', true);
    //     }

    //     return redirect()
    //         ->route('paiement.index', [$inscription->eleve_id, $request->annee_academique_id])
    //         ->with('success_message', 'Paiement enregistré avec succès !');
    // }

    public function modifierPaiement(Request $request, $id)
    {
        $paiement = Paiement::findOrFail($id);

        // Sauvegarde l'ancien montant
        $paiement->ancien_montant = $paiement->montant;

        // Mettre à jour avec le montant corrigé
        $paiement->update([
            'montant' => $request->montant,
            'corrige' => true,
        ]);

        // Génération du nouveau reçu PDF
        $pdf = Pdf::loadView('pdf.recu_paiement', compact('paiement'));
        $pdfPath = storage_path('app/public/Recu_' . $paiement->eleve->nom . '.pdf');
        $pdf->save($pdfPath);

        // Envoi du mail aux parents
        Mail::to($paiement->eleve->email_parent)->send(new PaiementCorrigeMail($paiement, $pdfPath));

        return back()->with('success', 'Paiement corrigé et reçu mis à jour envoyé.');
    }

    public function show($paiement_id)
    {
        // Récupérer le paiement avec les relations nécessaires
        $paiement = Paiement::with(['inscription.eleve', 'inscription.classe', 'inscription.Annee_academique'])
            ->where('id', $paiement_id)
            ->firstOrFail();

        // Vérifier si l'inscription et la classe existent
        if (!$paiement->inscription || !$paiement->inscription->classe) {
            abort(404, "Informations de classe ou d'inscription introuvables.");
        }

        // Recalculer le montant total payé par l'élève pour l'année académique
        $paiement_stat = Paiement::select('paiements.inscription_id', 'paiements.annee_academique_id', \DB::raw('SUM(paiements.montant) as montant_paye'), \DB::raw('MAX(classes.frais_scolarite) as total_scolarite'))->join('inscriptions', 'paiements.inscription_id', '=', 'inscriptions.id')->join('classes', 'inscriptions.classe_id', '=', 'classes.id')->where('paiements.inscription_id', $paiement->inscription_id)->where('paiements.annee_academique_id', $paiement->inscription->annee_academique_id)->groupBy('paiements.inscription_id', 'paiements.annee_academique_id')->first();

        // Vérifier si les données existent
        $total_scolarite = $paiement_stat->total_scolarite ?? 0;
        $montant_paye = $paiement_stat->montant_paye ?? 0;

        // Calculer le reste à payer
        $reste_a_payer = max(0, $total_scolarite - $montant_paye);

        // dd($total_scolarite, $montant_paye, $reste_a_payer);
        // Passer les variables à la vue
        return view('scolarite.recu', [
            'paiement' => $paiement,
            'reste_a_payer' => $paiement->reste_apres_paiement, // Utilisation de la valeur enregistrée
        ]);
    }

    // public function generatePdf($paiement)
    // {
    //     // Charger la vue et générer le PDF
    //     $pdf = PDF::loadView('scolarite.recu', compact('paiement'));

    //     // Sauvegarder temporairement le fichier
    //     $pdfPath = storage_path('app/public/recu_paiement_' . $paiement->id . '.pdf');
    //     $pdf->save($pdfPath);

    //     return $pdfPath;
    // }

    public function generatePdf($paiement)
    {
        // Charger la vue du reçu et lui passer les données
        $pdf = Pdf::loadView('scolarite.recu', [
            'paiement' => $paiement,
            'reste_a_payer' => $paiement->reste_apres_paiement, // On garde l'historique du reste à payer
        ]);

        // Définir le nom du fichier PDF
        $fileName = 'recu_paiement_' . $paiement->id . '.pdf';

        // Enregistrer temporairement le fichier PDF
        $filePath = storage_path('app/public/' . $fileName);
        $pdf->save($filePath);

        return $filePath;
    }

    public function downloadReceipt($paiementId)
    {
        $paiement = Paiement::findOrFail($paiementId);
        $pdfPath = $this->generatePdf($paiement);
        return response()->download($pdfPath);
    }

    public function store(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
            'montant' => 'required|numeric|min:1',
            'moyen_paiement' => 'required|in:espece,mobile_money',
        ]);

        $inscription = Inscription::findOrFail($request->inscription_id);
        $fraisScolarite = $inscription->classe->frais_scolarite;
        $montantPaye = Paiement::where('inscription_id', $inscription->id)->where('annee_academique_id', $request->annee_academique_id)->sum('montant');
        $reste_apres_paiement = max(0, $fraisScolarite - ($montantPaye + $request->montant));

        $resteAPayer = max(0, $fraisScolarite - $montantPaye);
        if ($resteAPayer == 0) {
            return redirect()->back()->with('error_message', 'Cet élève a déjà soldé sa scolarité !');
        }

        if ($request->moyen_paiement === 'mobile_money') {
            \FedaPay\FedaPay::setApiKey(config('services.fedapay.secret_key'));
            \FedaPay\FedaPay::setEnvironment(config('services.fedapay.mode'));

            $transaction = Transaction::create([
                'description' => 'Paiement des frais de scolarité',
                'amount' => $request->montant,
                'currency' => ['iso' => 'XOF'],
            ]);

            // dd($e->getMessage());
            return redirect($transaction->generatePaymentUrl());
        }

        // Si paiement en espèce, on enregistre directement
        $paiement = new Paiement();
        $paiement->inscription_id = $request->inscription_id;
        $paiement->annee_academique_id = $request->annee_academique_id;
        $paiement->montant = $request->montant;
        $paiement->moyen_paiement = 'espece';
        $paiement->transaction_id = null;
        $paiement->user_id = auth()->id();
        $paiement->date_paiement = now();
        $paiement->reste_apres_paiement = $reste_apres_paiement;
        $paiement->save();

        $montantPaye += $request->montant;
        if ($montantPaye >= $fraisScolarite) {
            return redirect()
                ->route('paiement.index', [$inscription->eleve_id, $request->annee_academique_id])
                ->with('paiement_effectue', true);
        }

        $pdfPath = $this->generatePdf($paiement);
        Mail::to($paiement->inscription->eleve->email_parent)->send(new RecuPaiement($paiement, $pdfPath));
        return redirect()
            ->route('paiement.index', [$inscription->eleve_id, $request->annee_academique_id])
            ->with('success_message', 'Paiement enregistré avec succès et reçu envoyé !');
    }

    public function fedapayCallback(Request $request)
    {
        \FedaPay\FedaPay::setApiKey(config('services.fedapay.secret_key'));
        \FedaPay\FedaPay::setEnvironment(config('services.fedapay.mode'));

        // $transaction = \FedaPay\Transaction::retrieve($request->transaction_id);
        if (!$request->has('transaction_id')) {
            return redirect()->route('paiement.index')->with('error_message', 'ID de transaction introuvable.');
        }

        $transaction_id = $request->transaction_id;
        $transaction = \FedaPay\Transaction::retrieve($transaction_id);
        $status = $transaction->status;

        if ($status === 'approved') {
            // Sauvegarder le paiement
            $paiement = new Paiement();
            $paiement->inscription_id = $request->inscription_id;
            $paiement->annee_academique_id = $request->annee_academique_id;
            $paiement->montant = $transaction->amount;
            $paiement->moyen_paiement = 'mobile_money';
            $paiement->transaction_id = $transaction->id;
            $paiement->user_id = auth()->id();
            $paiement->date_paiement = now();
            $paiement->save();

            $pdfPath = $this->generatePdf($paiement);
            Mail::to($paiement->inscription->eleve->email_parent)->send(new RecuPaiement($paiement, $pdfPath));

            return redirect()->route('paiement.index')->with('success_message', 'Paiement réussi via FedaPay et reçu envoyé !');
        }

        return redirect()->route('paiement.index')->with('error_message', 'Échec du paiement.');
    }

    public function initiateFedapay(Request $request)
    {
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.mode')); // 'sandbox' ou 'live'

        try {
            $transaction = Transaction::create([
                'description' => 'Paiement pour inscription ID ' . $request->inscription_id,
                'amount' => $request->montant,
                'currency' => ['iso' => 'XOF'],
                'callback_url' => route('paiement.fedapay.callback'),
            ]);

            $token = $transaction->generateToken();
            return redirect($token->url);
        } catch (\Exception $e) {
            return back()->with('error_message', 'Erreur FedaPay : ' . $e->getMessage());
        }
    }

    public function edit(Paiement $paiement)
    {
        return view('scolarite.edit', compact('paiement'));
    }

    public function update(Request $request, Paiement $paiement)
    {
        // Valider les données entrantes
        $request->validate([
            'montant' => 'required|numeric|min:1',
            'moyen_paiement' => 'required|in:espece,mobile_money',
        ]);

        // Vérifier si le paiement est mobile et déjà validé
        if ($paiement->moyen_paiement === 'mobile_money' && $paiement->transaction_id) {
            return redirect()->back()->with('error_message', 'Impossible de modifier un paiement mobile déjà validé.');
        }

        // $paiement = Paiement::with('inscription.classe')->find($paiement->id);

        // Vérifier si l'inscription existe
        if (!$paiement->inscription) {
            return redirect()->back()->with('error_message', "L'inscription associée à ce paiement est introuvable.");
        }

        // Vérifier si la classe existe
        if (!$paiement->inscription->classe) {
            return redirect()->back()->with('error_message', 'La classe associée à cette inscription est introuvable.');
        }

        // Enregistrer l'ancien montant
        $paiement->ancien_montant = $paiement->montant;

        // Récupérer les frais de scolarité
        $fraisScolarite = $paiement->inscription->classe->frais_scolarite;

        // Calcul du montant payé avant modification
        $montantPaye = Paiement::where('inscription_id', $paiement->inscription_id)
            ->where('annee_academique_id', $paiement->annee_academique_id)
            ->where('id', '!=', $paiement->id) // Exclure le paiement en cours de modification
            ->sum('montant');

        // Calcul du reste après modification
        $reste_apres_paiement = max(0, $fraisScolarite - ($montantPaye + $request->montant));

        // Mise à jour des informations du paiement
        $paiement->montant = $request->montant;
        $paiement->moyen_paiement = $request->moyen_paiement;
        $paiement->reste_apres_paiement = $reste_apres_paiement;
        $paiement->save();

        // Vérifier si le paiement est complété
        $montantPaye += $request->montant;
        if ($montantPaye >= $fraisScolarite) {
            return redirect()
                ->route('paiement.index', [$paiement->inscription->eleve_id, $paiement->annee_academique_id])
                ->with('paiement_effectue', true);
        }

        return redirect()
            ->route('paiement.index', [$paiement->inscription->eleve_id, $paiement->annee_academique_id])
            ->with('success_message', 'Paiement modifié avec succès !');
    }

    // public function initiateFedapay(Request $request)
    // {
    //     // Vérification des clés utilisées
    //     dd(config('services.fedapay'));

    //     FedaPay::setApiKey(config('services.fedapay.secret_key'));
    //     FedaPay::setEnvironment(config('services.fedapay.mode')); // 'sandbox' ou 'live'

    //     try {
    //         $transaction = Transaction::create([
    //             'description' => 'Paiement des frais de scolarité',
    //             'amount' => $request->montant,
    //             'currency' => ['iso' => 'XOF'],
    //         ]);

    //         return redirect($transaction->generatePaymentUrl());
    //     } catch (\Exception $e) {
    //         return back()->with('error_message', 'Erreur FedaPay : ' . $e->getMessage());
    //     }
    // }

    // public function verifierPaiement(Request $request)
    // {
    //     $id_transaction = $request->get('transaction_id');
    //     $inscription_id = $request->get('inscription_id');
    //     $annee_academique_id = $request->get('annee_academique_id');
    //     $montant = $request->get('montant');

    //     if (!$id_transaction) {
    //         return redirect()->back()->with('error_message', 'Transaction ID manquant.');
    //     }

    //     // Vérifier si la transaction existe déjà
    //     if (Paiement::where('transaction_id', $id_transaction)->exists()) {
    //         return redirect()
    //             ->route('paiement.index', [$inscription_id, $annee_academique_id])
    //             ->with('error_message', 'Cette transaction a déjà été enregistrée.');
    //     }

    //     // Vérification via API Kkiapay
    //     $response = Http::withHeaders([
    //         'x-api-key' => config('services.kkiapay.private_key'),
    //         'Content-Type' => 'application/json',
    //     ])->post('https://api.kkiapay.me/api/v1/transactions/status', [
    //         'transactionId' => $id_transaction,
    //     ]);

    //     $data = $response->json();

    //     if ($data['status'] !== 'SUCCESS') {
    //         return redirect()
    //             ->route('paiement.index', [$inscription_id, $annee_academique_id])
    //             ->with('error_message', 'Le paiement n\'a pas été validé.');
    //     }

    //     // Enregistrer le paiement
    //     Paiement::create([
    //         'inscription_id' => $inscription_id,
    //         'annee_academique_id' => $annee_academique_id,
    //         'montant' => $montant,
    //         'moyen_paiement' => 'mobile_money',
    //         'transaction_id' => $id_transaction,
    //         'user_id' => auth()->id(),
    //         'date_paiement' => now(),
    //     ]);

    //     return redirect()
    //         ->route('paiement.index', [$inscription_id, $annee_academique_id])
    //         ->with('success', 'Paiement validé et enregistré avec succès !');
    // }

    public function create(Request $request)
    {
        $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
        ]);

        $eleve = Eleve::findOrFail($request->eleve_id);
        $annee_academique = Annee_academique::findOrFail($request->annee_academique_id);

        // Récupérer l'inscription de l'élève pour l'année académique en cours
        $inscription = Inscription::where('eleve_id', $eleve->id)->where('annee_academique_id', $annee_academique->id)->firstOrFail();

        // Récupérer les frais de scolarité de la classe de l'élève
        $fraisScolarite = $inscription->classe->frais_scolarite;

        // Calculer le montant déjà payé par l'élève
        $montantPaye = Paiement::where('inscription_id', $inscription->id)->where('annee_academique_id', $annee_academique->id)->sum('montant');

        // Calculer le reste à payer
        $resteAPayer = max(0, $fraisScolarite - $montantPaye);

        // Vérifier si l'élève a déjà tout payé
        if ($resteAPayer == 0) {
            return redirect()->back()->with('error_message', 'Cet élève a déjà soldé sa scolarité !');
        }

        return view('scolarite.create', compact('eleve', 'annee_academique', 'inscription', 'resteAPayer'));
    }

    // public function index()
    // {
    //     // Récupérer tous les paiements avec les relations nécessaires
    //     $paiements = Paiement::with('inscription.eleve', 'inscription.classe', 'anneeAcademique')->latest()->get();

    //     // Parcourir chaque paiement pour calculer les montants restants
    //     foreach ($paiements as $paiement) {
    //         $inscription = $paiement->inscription;
    //         $fraisScolarite = $inscription->classe->frais_scolarite ?? 0; // Vérifier si la classe existe
    //         $montantPaye = Paiement::where('inscription_id', $inscription->id)->where('annee_academique_id', $paiement->annee_academique_id)->sum('montant');

    //         $resteAPayer = max(0, $fraisScolarite - $montantPaye); // Éviter les valeurs négatives

    //         // Ajouter les valeurs calculées dans l'objet paiement
    //         $paiement->classe = $inscription->classe->nom ?? 'N/A';
    //         $paiement->total_scolarite = $fraisScolarite;
    //         $paiement->montant_paye = $montantPaye;
    //         $paiement->reste_a_payer = $resteAPayer;
    //     }

    //     return view('scolarite.index', compact('paiements'));
    // }

    // public function index()
    // {
    //     // Récupérer les paiements en les regroupant par élève et année académique
    //     $paiements = Paiement::with(['inscription.eleve', 'inscription.classe', 'anneeAcademique'])
    //         ->select('paiements.inscription_id', 'paiements.annee_academique_id', \DB::raw('SUM(paiements.montant) as montant_paye'), \DB::raw('MAX(classes.frais_scolarite) as total_scolarite'))
    //         ->join('inscriptions', 'paiements.inscription_id', '=', 'inscriptions.id')
    //         ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
    //         ->groupBy('paiements.inscription_id', 'paiements.annee_academique_id')
    //         ->get();

    //     return view('scolarite.index', compact('paiements'));
    // }

    public function index()
    {
        // Récupérer les paiements en les regroupant par élève et année académique
        $paiements = Paiement::with(['inscription.eleve', 'inscription.classe', 'anneeAcademique'])
            ->select('paiements.inscription_id', 'paiements.annee_academique_id', \DB::raw('SUM(paiements.montant) as montant_paye'), \DB::raw('MAX(classes.frais_scolarite) as total_scolarite'))
            ->join('inscriptions', 'paiements.inscription_id', '=', 'inscriptions.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->groupBy('paiements.inscription_id', 'paiements.annee_academique_id')
            ->get();

        // Calculer le reste à payer pour chaque paiement et déterminer si la scolarité est soldée
        $paiements->each(function ($paiement) {
            $paiement->reste_a_payer = max(0, $paiement->total_scolarite - $paiement->montant_paye);
            $paiement->scolarite_soldee = $paiement->reste_a_payer == 0;
            $paiement->statut = $paiement->scolarite_soldee ? '✅ Scolarisé' : "❌ En attente de paiement ({$paiement->reste_a_payer} FCFA restants)";
        });

        // Message flash si un paiement a été effectué récemment
        if (session()->has('paiement_effectue')) {
            session()->flash('success_message', 'Félicitations! Scolarité soldée. 🎉');
        }

        return view('scolarite.index', compact('paiements'));
    }

    public function details($inscription_id, $annee_academique_id)
    {
        // Récupérer l'inscription avec l'élève et la classe
        $inscription = Inscription::with('eleve', 'classe')->where('id', $inscription_id)->firstOrFail();

        // Récupérer le montant total de la scolarité depuis Inscription
        $total_scolarite = $inscription->classe->frais_scolarite;

        // Récupérer tous les paiements de l'élève pour l'année donnée
        $paiements = Paiement::where('inscription_id', $inscription_id)->where('annee_academique_id', $annee_academique_id)->orderBy('date_paiement', 'asc')->get();

        // Calculer le montant total déjà payé
        $montant_paye = $paiements->sum('montant');

        // Calculer le reste à payer (en s'assurant qu'il ne soit jamais négatif)
        $reste_a_payer = max(0, $total_scolarite - $montant_paye);

        return view('scolarite.details', compact('inscription', 'paiements', 'total_scolarite', 'montant_paye', 'reste_a_payer'));
    }

    public function afficherFormulaireRecherche()
    {
        $classes = Classe::all();
        $annees = Annee_academique::all();
        return view('scolarite.recherche', compact('classes', 'annees'));
    }

    public function afficherPaiements(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'annee_academique_id' => 'required|exists:annee_academiques,id',
        ]);

        $classe_id = $request->classe_id;
        $annee_academique_id = $request->annee_academique_id;

        $paiements = Paiement::with(['inscription.eleve', 'inscription.classe', 'anneeAcademique'])
            ->select('paiements.inscription_id', 'paiements.annee_academique_id', \DB::raw('SUM(paiements.montant) as montant_paye'), \DB::raw('MAX(classes.frais_scolarite) as total_scolarite'))
            ->join('inscriptions', 'paiements.inscription_id', '=', 'inscriptions.id')
            ->join('classes', 'inscriptions.classe_id', '=', 'classes.id')
            ->where('inscriptions.classe_id', $classe_id)
            ->where('paiements.annee_academique_id', $annee_academique_id)
            ->groupBy('paiements.inscription_id', 'paiements.annee_academique_id')
            ->get();

        $paiements->each(function ($paiement) {
            $paiement->reste_a_payer = max(0, $paiement->total_scolarite - $paiement->montant_paye);
            $paiement->scolarite_soldee = $paiement->reste_a_payer == 0;
            $paiement->statut = $paiement->scolarite_soldee ? '✅ Scolarisé' : "❌ En attente de paiement ({$paiement->reste_a_payer} FCFA restants)";
        });

        $classes = Classe::all();
        $annees = Annee_academique::all();

        return view('scolarite.recherche', compact('paiements', 'classes', 'annees'));
    }

    public function getAccountInfo(Request $request)
    {
        $userId = auth()->user()->id;
        $paymentInfo = PaymentGateway::where('user_id', $userId)->first();
        return view('dashboard.users.manage.payment', compact('paymentInfo'));
    }

    public function handleUpdateInfo(Request $request)
    {
        DB::beginTransaction();

        $request->validate(
            [
                'site_id' => 'required',
                'api_key' => 'required',
                'secret_key' => 'required',
            ],
            [
                'site_id.required' => 'L\'id du site est requis',
                'api_key.required' => 'La clé API est requise',
                'secret_key.required' => 'La clé sécrète est requise',
            ],
        );

        try {
            // dd($e);
            $userId = auth()->user()->id;
            $existingAccount = PaymentGateway::where('user_id', $userId)->first();
            if ($existingAccount) {
                $existingAccount->site_id = $request->site_id;
                $existingAccount->api_key = $request->api_key;
                $existingAccount->secret_key = $request->secret_key;
                $existingAccount->update();
            } else {
                PaymentGateway::create([
                    'user_id' => $userId,
                    'site_id' => $request->site_id,
                    'api_key' => $request->api_key,
                    'secret_key' => $request->secret_key,
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success_message', 'Données enregistrées');
        } catch (Exception $e) {
            DB::rollback();
        }
    }
}
