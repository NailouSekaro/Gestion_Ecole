<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de paiement</title>
</head>
<body>
    <p>Bonjour,</p>
    <p>Veuillez trouver ci-joint le reçu du paiement de la scolarité de votre enfant, {{ $paiement->inscription->eleve->nom }}.</p>
    <p>Montant : <strong>{{ number_format($paiement->montant, 0, ',', ' ') }} F CFA</strong></p>
    <p>Date : <strong>{{ $paiement->date_paiement->format('d/m/Y H:i') }}</strong></p>
    <p>Merci pour votre confiance.</p>
</body>
</html>
