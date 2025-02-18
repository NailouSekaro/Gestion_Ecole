@component('mail::message')
# Correction de paiement

Bonjour cher(e) parent,

Une correction a été effectuée sur le paiement de votre enfant.
Ancien montant : **{{ $paiement->ancien_montant }}**
Nouveau montant : **{{ $paiement->montant }}**

Veuillez trouver en pièce jointe le reçu corrigé.

Merci de votre compréhension.

Cordialement,
**L'administration de l'école**
@endcomponent
