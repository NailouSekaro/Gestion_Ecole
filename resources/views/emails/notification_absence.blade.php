<!DOCTYPE html>
<html>
<head>
    <title>Alerte Absences</title>
</head>
<body>
    <h1>Alerte : Absences Non Justifiées</h1>
    <p>Bonjour,</p>
    <p>Votre enfant {{ $eleve->nom }} {{ $eleve->prenom }} a accumulé {{ $absencesCount }} absences non justifiées.</p>
    <p>Veuillez contacter l'école pour régulariser la situation.</p>
    <p>Cordialement,<br>L'Équipe Scolaire</p>
</body>
</html>
