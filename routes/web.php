<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\anneeAcademiqueController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TrimestreController;
use App\Http\Controllers\CoefficientController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\UserPaymentController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/test-mail', function () {
//     Mail::raw('Test d\'envoi de mail', function ($message) {
//         $message
//             ->to('nailousekaro@gmail.com') // Remplace par ton e-mail
//             ->subject('Test Laravel Mail');
//     });

//     return 'E-mail envoyé avec succès !';
// });

Route::get('login', [UserController::class, 'login'])->name('login');
Route::post('handelogin', [UserController::class, 'handelogin'])->name('handelogin');

Route::get('/validate-account/{email}', [EnseignantController::class, 'validateAccount'])->name('validate.account');
Route::post('/validate-account-submit', [EnseignantController::class, 'validateAccountSubmit'])->name('validate.account.submit');

Route::get('payment-configuration', [PaiementController::class, 'getAccountInfo'])->name('payment.configuration');
Route::post('handle-payment-configuration', [PaiementController::class, 'handleUpdateInfo'])->name('payments.Updateconfiguration');

Route::middleware('auth')->group(function () {
    Route::post('/cinetpay/notify', [PaymentController::class, 'handleNotification'])->name('cinetpay.notify');
    Route::get('/cinetpay/return', [PaymentController::class, 'handleReturn'])->name('cinetpay.return');

    Route::get('/paiement/eleve/{eleve_id}/{annee_academique_id}', [UserPaymentController::class, 'initPayment'])->name('eleve.paiement');
    Route::get('/paiement/fedapay/callback', [PaiementController::class, 'fedapayCallback'])->name('paiement.fedapay.callback');
    Route::get('/paiement/fedapay/initiate', [PaiementController::class, 'initiateFedapay'])->name('paiement.fedapay.initiate');

    Route::get('dashbord', [AppController::class, 'index'])->name('dashboard');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');

    Route::post('/updatePassword', [AppController::class, 'updatePassword'])->name('updatePassword');
    Route::get('/change-password', [AppController::class, 'showChangePasswordForm'])->name('change-password');

    Route::get('/envoyer-notes/{id}', [EleveController::class, 'envoyerNotes'])->name('envoyer.notes');

    Route::prefix('eleve')->group(function () {
        Route::get('/', [EleveController::class, 'index'])->name('eleve.index');
        Route::get('/create', [EleveController::class, 'create'])->name('eleve.create');
        Route::get('/reinscription/{eleve}', [EleveController::class, 'reinscription'])->name('eleve.reinscription');
        Route::post('/reinscrireEleve/{id}', [EleveController::class, 'reinscrireEleve'])->name('eleve.reinscrireEleve');
        Route::get('listeReinscription', [EleveController::class, 'listeReinscription'])->name('eleve.listeReinscription');
        Route::get('/edit/{eleve}', [EleveController::class, 'edit'])->name('eleve.edit');
        Route::post('/store', [EleveController::class, 'store'])->name('eleve.store');
        Route::get('/recherche-eleve', [EleveController::class, 'afficherFormulaiRerecherce'])->name('eleve.recherche');
        Route::get('/eleve/classe-annee', [EleveController::class, 'afficherEleve'])->name('eleve.afficher');
        // route d'action pour la modification
        Route::get('/eleve/{id}/envoyer-notes', [EleveController::class, 'envoyerNotes'])->name('eleve.envoyer_notes');

        Route::put('/update/{id}', [EleveController::class, 'update'])->name('eleve.update');
        Route::get('/delete/{eleve}', [EleveController::class, 'delete'])->name('eleve.delete');
    });

    Route::prefix('classe')->group(function () {
        Route::get('/', [ClasseController::class, 'index'])->name('classe.index');
        Route::get('/create', [ClasseController::class, 'create'])->name('classe.create');
        Route::get('/edit/{classe}', [ClasseController::class, 'edit'])->name('classe.edit');
        Route::post('/store', [ClasseController::class, 'store'])->name('classe.store');
        Route::get('/liste/{classe}', [ClasseController::class, 'liste'])->name('classe.liste');
        // route d'action pour la modification
        Route::put('/update/{id}', [ClasseController::class, 'update'])->name('classe.update');
        Route::get('/delete/{classe}', [ClasseController::class, 'delete'])->name('classe.delete');
    });

    Route::prefix('paiement')->group(function () {
        Route::get('/', [PaiementController::class, 'index'])->name('paiement.index');
        Route::get('/create', [PaiementController::class, 'create'])->name('paiement.create');
        Route::get('/edit/{paiement}', [PaiementController::class, 'edit'])->name('paiement.edit');
        Route::post('/store', [PaiementController::class, 'store'])->name('paiement.store');
        Route::get('/liste/{paiement}', [PaiementController::class, 'liste'])->name('paiement.liste');
        // route d'action pour la modification
        // Route::get('/verifier-paiement', [PaiementController::class, 'verifierPaiement']);
        Route::get('/paiement/kkiapay/success', [PaiementController::class, 'verifierPaiement']);
        Route::get('/paiements/details/{inscription_id}/{annee_academique_id}', [PaiementController::class, 'details'])->name('paiements.details');
        Route::get('/paiements/recherche', [PaiementController::class, 'afficherFormulaireRecherche'])->name('paiements.recherche');
        Route::get('/paiements/afficher', [PaiementController::class, 'afficherPaiements'])->name('paiements.afficher');

        Route::put('/update/{id}', [PaiementController::class, 'update'])->name('paiement.update');
        Route::get('/paiement/{id}/recu', [PaiementController::class, 'show'])->name('paiement.recu');
        Route::get('/paiement/{id}/download', [PaiementController::class, 'downloadReceipt'])->name('paiement.download');

        Route::get('/delete/{paiement}', [PaiementController::class, 'delete'])->name('paiement.delete');
    });

    Route::prefix('enseignant')->group(function () {
        Route::get('/', [EnseignantController::class, 'index'])->name('enseignant.index');
        Route::get('/create', [EnseignantController::class, 'create'])->name('enseignant.create');
        Route::get('/edit/{enseignant}', [EnseignantController::class, 'edit'])->name('enseignant.edit');
        Route::post('/store', [EnseignantController::class, 'store'])->name('enseignant.store');
        Route::get('/liste/{enseignant}', [EnseignantController::class, 'liste'])->name('enseignant.liste');
        // route d'action pour la modification
        Route::put('/update/{id}', [EnseignantController::class, 'update'])->name('enseignant.update');
        Route::get('/delete/{enseignant}', [EnseignantController::class, 'delete'])->name('enseignant.delete');
    });

    // Route::get('/validate-account/{email}', [EnseignantController::class, 'validateAccount'])->name('validate.account');
    // Route::post('/validate-account-submit', [EnseignantController::class, 'validateAccountSubmit'])->name('validate.account.submit');
    // Route::get('/validate-account/{email}', [EnseignantController::class, 'defineAccess']);

    Route::prefix('annee')->group(function () {
        Route::get('/', [anneeAcademiqueController::class, 'index'])->name('annee.index');
        Route::get('/create', [anneeAcademiqueController::class, 'create'])->name('annee.create');
        Route::get('/edit/{annee_academique}', [anneeAcademiqueController::class, 'edit'])->name('annee.edit');
        Route::post('/store', [anneeAcademiqueController::class, 'store'])->name('annee.store');
        // route d'action pour la modification
        Route::put('/update/{id}', [anneeAcademiqueController::class, 'update'])->name('annee.update');
        Route::get('/delete/{annee}', [anneeAcademiqueController::class, 'delete'])->name('annee.delete');
    });

    Route::prefix('matiere')->group(function () {
        Route::get('/', [MatiereController::class, 'index'])->name('matiere.index');
        Route::get('/create', [MatiereController::class, 'create'])->name('matiere.create');
        Route::get('/edit/{matiere}', [MatiereController::class, 'edit'])->name('matiere.edit');
        Route::post('/store', [MatiereController::class, 'store'])->name('matiere.store');
        // route d'action pour la modification
        Route::put('/update/{id}', [MatiereController::class, 'update'])->name('matiere.update');
        Route::get('/delete/{matiere}', [MatiereController::class, 'delete'])->name('matiere.delete');
    });

    Route::prefix('note')->group(function () {
        // Route::get('/', [NoteController::class, 'index'])->name('note.index');
        Route::get('/note/inserer/{eleve_id}/{annee_academique_id}', [NoteController::class, 'create'])->name('note.create');
        // Route::get('/note/voir/{eleve_id}/{annee_academique_id}', [NoteController::class, 'voir'])->name('note.voir');
        Route::get('/note/index/{eleve_id}/{annee_academique_id}', [NoteController::class, 'index'])->name('note.index');
        Route::get('notes/{eleve_id}/matiere/{matiere_id}', [NoteController::class, 'edit'])->name('note.edit');
        Route::post('/store/{eleve_id}/{annee_academique_id}', [NoteController::class, 'store'])->name('note.store');
        Route::get('/liste/{note}', [NoteController::class, 'liste'])->name('note.liste');
        // Route::get('/note/rechercher', [NoteController::class, 'rechercher'])->name('note.rechercher');
        // Route::get('/note/voir', [NoteController::class, 'voir'])->name('note.voir');

        Route::get('/notes/{eleve_id}/voir', [NoteController::class, 'voir'])->name('note.voir');

        Route::get('/notes/{eleve_id}/{annee_academique_id}/moyenne', [NoteController::class, 'calculerMoyennes'])->name('note.moyenne');

        Route::get('/note/rechercher', [NoteController::class, 'rechercher'])->name('note.rechercher');

        // route d'action pour la modification
        Route::put('/update/{id}', [NoteController::class, 'update'])->name('note.update');
        Route::get('/delete/{note}', [NoteController::class, 'delete'])->name('note.delete');
    });

    Route::prefix('trimestre')->group(function () {
        Route::get('/', [TrimestreController::class, 'index'])->name('trimestre.index');
        Route::get('/create', [TrimestreController::class, 'create'])->name('trimestre.create');
        Route::get('/edit/{trimestre}', [TrimestreController::class, 'edit'])->name('trimestre.edit');
        Route::post('/store', [TrimestreController::class, 'store'])->name('trimestre.store');
        // route d'action pour la modification
        Route::put('/update/{id}', [TrimestreController::class, 'update'])->name('trimestre.update');
        Route::get('/delete/{trimestre}', [TrimestreController::class, 'delete'])->name('trimestre.delete');
    });

    Route::prefix('coefficient')->group(function () {
        Route::get('/', [CoefficientController::class, 'index'])->name('coefficient.index');
        Route::get('/create', [CoefficientController::class, 'create'])->name('coefficient.create');
        Route::get('/edit/{coefficient}', [CoefficientController::class, 'edit'])->name('coefficient.edit');
        Route::post('/store', [CoefficientController::class, 'store'])->name('coefficient.store');
        // route d'action pour la modification
        Route::put('/update/{id}', [CoefficientController::class, 'update'])->name('coefficient.update');
        Route::get('/delete/{coefficient}', [CoefficientController::class, 'delete'])->name('coefficient.delete');
    });
});
