<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use \FedaPay\FedaPay;
use \FedaPay\Transaction;
use App\Mail\RecuPaiement; // Assume ton mailable
use App\Mail\ConfirmationVirement; // Import the correct mailable
use App\Mail\NotificationVirement; // Import the NotificationVirement mailable
// use Barryvdh\DomPDF\PDF as DomPDFPDF;
// use PDF; // Si tu utilises DomPDF pour generatePdf
use Barryvdh\DomPDF\Facade\Pdf;

class PaiementParentController extends Controller
{
    // public function verifyMatricule(Request $request)
    // {
    //     $request->validate(['educ_master' => 'required|string|max:13']);

    //     $eleve = Eleve::where('matricule_educ_master', $request->educ_master)->first();

    //     if (!$eleve) {
    //         return response()->json(['error' => 'Matricule éduc master invalide. Veuillez réessayer.'], 404);
    //     }

    //     // Rediriger vers la vue paiement avec ID élève (stocké en session pour sécurité)
    //     session(['parent_eleve_id' => $eleve->id]);
    //     return response()->json(['redirect' => route('paiement.parent.form', $eleve->id)]);
    // }

    public function verifyMatricule(Request $request)
    {
        $request->validate(['educ_master' => 'required|string|max:13']);

        $eleve = Eleve::where('matricule_educ_master', $request->educ_master)->first();

        if (!$eleve) {
            return response()->json(['error' => 'Matricule éduc master invalide. Veuillez réessayer.'], 404);
        }

        // Rediriger vers la vue paiement avec ID élève (stocké en session pour sécurité)
        session(['parent_eleve_id' => $eleve->id]);
        return response()->json(['redirect' => route('paiement.parent.form', $eleve->id)]);
    }

    public function showForm($eleveId)
    {


        if (session('parent_eleve_id') != $eleveId) {
            abort(403); // Sécurité : Vérifie session
        }

        $eleve = Eleve::findOrFail($eleveId);
        $inscriptions = $eleve->inscriptions()->with(['classe', 'Annee_academique'])->get();
        $annees = $inscriptions->pluck('Annee_academique')->unique();

        // Pour chaque année, calculer reste à payer (groupe par année)
        $impayes = [];
        foreach ($annees as $annee) {
            $inscriptionsAnnee = $inscriptions->where('annee_academique_id', $annee->id);
            $totalScolarite = $inscriptionsAnnee->sum(fn($ins) => $ins->classe->frais_scolarite);
            $montantPaye = Paiement::whereIn('inscription_id', $inscriptionsAnnee->pluck('id'))->sum('montant');
            $reste = max(0, $totalScolarite - $montantPaye);
            $impayes[$annee->id] = [
                'annee' => $annee->annee,
                'reste' => $reste,
                'historique' => Paiement::whereIn('inscription_id', $inscriptionsAnnee->pluck('id'))->get(),
            ];
        }

        return view('paiement.parent', compact('eleve', 'annees', 'impayes'));
    }

    public function initiateFedapay(Request $request)
    {
        $request->validate([
            'annee_academique_id' => 'required|exists:annee_academiques,id',
            'montant' => 'required|numeric|min:1',
        ]);

        $eleveId = session('parent_eleve_id');
        $eleve = Eleve::findOrFail($eleveId);
        $inscription = $eleve->inscriptions()->where('annee_academique_id', $request->annee_academique_id)->firstOrFail();

        // Vérifier reste à payer
        $reste = $this->calculReste($inscription, $request->annee_academique_id);
        if ($request->montant > $reste) {
            return back()->with('error_message', 'Montant excède le reste à payer.');
        }

        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.mode'));

        try {
            $transaction = Transaction::create([
                'description' => 'Paiement scolarité pour ' . $eleve->nom . ' ' . $eleve->prenom,
                'amount' => $request->montant,
                'currency' => ['iso' => 'XOF'],
                'callback_url' => route('paiement.parent.fedapay.callback', [
                    'inscription_id' => $inscription->id,
                    'annee_academique_id' => $request->annee_academique_id,
                ]),
            ]);

            $token = $transaction->generateToken();
            return redirect($token->url);
        } catch (\Exception $e) {
            return back()->with('error_message', 'Erreur FedaPay : ' . $e->getMessage());
        }
    }

    public function fedapayCallback(Request $request)
    {
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.mode'));

        if (!$request->has('id')) {
            return redirect('/')->with('error_message', 'ID de transaction introuvable.');
        }

        $transaction = Transaction::retrieve($request->id);
        if ($transaction->status !== 'approved') {
            return redirect('/')->with('error_message', 'Échec du paiement.');
        }

        $inscription = Inscription::findOrFail($request->inscription_id);
        $paiement = Paiement::create([
            'inscription_id' => $request->inscription_id,
            'annee_academique_id' => $request->annee_academique_id,
            'montant' => $transaction->amount,
            'moyen_paiement' => 'mobile_money',
            'transaction_id' => $transaction->id,
            'date_paiement' => now(),
            'reste_apres_paiement' => $this->calculReste($inscription, $request->annee_academique_id, $transaction->amount),
            'statut' => 'approved', // Ajoute un champ statut si besoin
        ]);

        $pdfPath = $this->generatePdf($paiement); // Réutilise ta méthode
        Mail::to($inscription->eleve->email_parent)->send(new RecuPaiement($paiement, $pdfPath));

        session()->forget('parent_eleve_id');
        return redirect('/')->with('success_message', 'Paiement réussi ! Reçu envoyé par email.');
    }

    public function storeVirement(Request $request)
    {
        $request->validate([
            'annee_academique_id' => 'required|exists:annee_academiques,id',
            'montant' => 'required|numeric|min:1',
            'preuve' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $eleveId = session('parent_eleve_id');
        $eleve = Eleve::findOrFail($eleveId);
        $inscription = $eleve->inscriptions()->where('annee_academique_id', $request->annee_academique_id)->firstOrFail();

        $reste = $this->calculReste($inscription, $request->annee_academique_id);
        if ($request->montant > $reste) {
            return back()->with('error_message', 'Montant excède le reste à payer.');
        }

        $preuvePath = $request->file('preuve')->store('preuves_virements', 'public');

        $paiement = Paiement::create([
            'inscription_id' => $inscription->id,
            'annee_academique_id' => $request->annee_academique_id,
            'montant' => $request->montant,
            'moyen_paiement' => 'virement_bancaire',
            'date_paiement' => now(),
            'preuve' => $preuvePath,
            'statut' => 'pending', // Ajoute champ 'statut' à ton model Paiement (migration: add_statut_to_paiements)
            'reste_apres_paiement' => $reste - $request->montant,
        ]);

        // Email au secrétariat pour validation
        Mail::to(config('mail.secretariat_email'))->send(new NotificationVirement($paiement)); // Crée un nouveau mailable

        // Email confirmation au parent
        Mail::to($eleve->email_parent)->send(new ConfirmationVirement($paiement));

        return back()->with('success_message', 'Demande de virement soumise ! En attente de validation.');
    }

    private function calculReste($inscription, $anneeId, $montantAjoute = 0)
    {
        $fraisScolarite = $inscription->classe->frais_scolarite;
        $montantPaye = Paiement::where('inscription_id', $inscription->id)
            ->where('annee_academique_id', $anneeId)
            ->where('statut', 'approved') // Seulement approved
            ->sum('montant');
        return max(0, $fraisScolarite - ($montantPaye + $montantAjoute));
    }

    // Réutilise ta méthode generatePdf du controller original
    // private function generatePdf($paiement)
    // {
    //     // Implémente comme dans ton code original
    //     // Ex: $pdf = PDF::loadView(...); return $pdf->save(...)->getPath();
    // }

    public function generatePdf($paiement)
    {
        // Charger la vue du reçu et lui passer les données
        $pdf = PDF::loadView('scolarite.recu', [
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

}
