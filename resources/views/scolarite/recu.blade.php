<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 10px;

        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 120px;
            /* Ajuste la taille du logo */
            height: auto;
        }

        .details {
            border: 1px solid #000;
            padding: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
            font-style: italic;
        }

        .signature img {
            width: 100px;
            /* Ajuste la taille du cachet ou signature */
            height: auto;
        }

        .container {
            border: 2px solid #000;
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="container" style="max-height:auto;">

        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo de l'école">
            <h2>Reçu de Paiement</h2>
            <p>Émis le {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</p>
        </div>

        <div class="details">
            <p><strong>Nom de l'élève :</strong> {{ $paiement->inscription->eleve->nom }}
                {{ $paiement->inscription->eleve->prenom }}</p>
            <p><strong>Classe :</strong> {{ $paiement->inscription->classe->nom_classe }}</p>
            <p><strong>Année académique :</strong> {{ $paiement->inscription->annee_academique->annee }}</p>
            <p><strong>Montant payé :</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
            <p><strong>Reste à payer :</strong> {{ number_format($reste_a_payer, 0, ',', ' ') }} FCFA</p>
            <p><strong>Date de paiement :</strong> {{ $paiement->date_paiement}} </p>
        </div>

        <div class="signature">
            <p>Signature du responsable</p>
            <img src="{{ asset('images/signature.png') }}" alt="Signature">
        </div>

        <div class="footer">
            <p>Merci pour votre paiement !</p>
        </div>
    </div>
</body>

</html>
