<?php

namespace App\Http\Controllers;

use App\Helpers\CinetPay;
// use CinetPay\CinetPay;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\PaymentGateway;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserPaymentController extends Controller {

    public function initPayment( $classe_id, $annee_academique_id ) {
        $classeInfo = classe::findOrFail( $classe_id );
        // dd( $classeInfo );
        $notify_url = '';
        $return_url = '';
        $paymentData = array(
            'transaction_id'=> str::random( 40 ),
            'amount'=> $classeInfo->frais_scolarite,
            'currency'=>'XOF',
            'customer_surname'=> 'BOUBACAR',
            'customer_name'=> 'CAMARA',
            'description'=> 'Paiement scolarité'.$classeInfo->nom_classe,
            'notify_url' => $notify_url,
            'return_url' => $return_url,
            'invoice_data' =>[],
            'channels' => 'ALL',
            'metadata' => '', // utiliser cette variable pour recevoir des informations personnalisés.
            'alternative_currency' => '', //Valeur de la transaction dans une devise alternative
            //Fournir ces variables obligatoirement pour le paiements par carte bancaire
            'customer_email' => '', //l'email du client
        "customer_phone_number" => "", //Le numéro de téléphone du client
        "customer_address" => "", //l'adresse du client
            'customer_city' => '', // ville du client
            'customer_country' => '', //Le pays du client, la valeur à envoyer est le code ISO du pays ( code à deux chiffre ) ex : CI, BF, US, CA, FR
            'customer_state' => '', //L’état dans de la quel se trouve le client. Cette valeur est obligatoire si le client se trouve au États Unis d’Amérique ( US ) ou au Canada ( CA )
            'customer_zip_code' => '' //Le code postal du client
        );

        $paymentSourceInfo = PaymentGateway::where( 'user_id', $classeInfo->user_id )->first();
        $Cinetpay = new CinetPay( $paymentSourceInfo->site_id, $paymentSourceInfo->api_key );

        if ( !$paymentSourceInfo ) {
            dd( "Erreur : Informations de paiement introuvables pour l'utilisateur {$classeInfo->user_id}" );
        }

        dd( $paymentSourceInfo->site_id, $paymentSourceInfo->api_key );

        // $result = $Cinetpay->getPayButton( $paymentData );
        // dd( $result );
    }

    // public function initPayment( $classe_id, $annee_academique_id ) {
    //     // Récupérer les informations de la classe
    //     $classeInfo = Classe::findOrFail( $classe_id );

    //     // Générer un ID de transaction unique
    //     $transactionId = Str::random( 40 );

    //     // Configurer les URLs de callback
    //     $notify_url = route( 'cinetpay.notify' );
    //     // URL de notification
    //     $return_url = route( 'cinetpay.return' );
    //     // URL de retour

    //     // Données de paiement
    //     $paymentData = [
    //         'transaction_id' => $transactionId,
    //         'amount' => $classeInfo->frais_scolarite,
    //         'currency' => 'XOF',
    //         'customer_surname' => 'BOUBACAR',
    //         'customer_name' => 'CAMARA',
    //         'description' => 'Paiement scolarité ' . $classeInfo->nom_classe,
    //         'notify_url' => $notify_url,
    //         'return_url' => $return_url,
    //         'channels' => 'ALL',
    //         'metadata' => '', // Informations personnalisées
    //         'customer_email' => '', // Email du client
    //         'customer_phone_number' => '', // Numéro de téléphone du client
    //         'customer_address' => '', // Adresse du client
    //         'customer_city' => '', // Ville du client
    //         'customer_country' => '', // Code ISO du pays ( ex: CI, BF, US )
    //         'customer_state' => '', // État ( obligatoire pour US ou CA )
    //         'customer_zip_code' => '', // Code postal du client
    // ];

    //     // Récupérer les informations de la passerelle de paiement
    //     $paymentSourceInfo = PaymentGateway::where( 'user_id', $classeInfo->user_id )->first();

    //     // Vérifier si la passerelle de paiement existe
    //     if ( !$paymentSourceInfo ) {
    //         return redirect()->back()->with( 'error', 'Passerelle de paiement non trouvée pour cet utilisateur.' );
    //     }

    //     // Initialiser CinetPay
    //     $Cinetpay = new CinetPay( $paymentSourceInfo->site_id, $paymentSourceInfo->api_key );

    //     // Générer le lien de paiement
    //     try {
    //         $result = $Cinetpay->getPayButton( 'form_paiement', 4, 'large', $paymentData );
    //         return redirect()->away( $result );
    //         // Rediriger vers le lien de paiement
    //     } catch ( \Exception $e ) {
    //         return redirect()->back()->with( 'error', 'Erreur lors de la génération du lien de paiement : ' . $e->getMessage() );
    //     }
    // }

}
