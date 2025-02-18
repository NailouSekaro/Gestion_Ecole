<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
        }
    </style>
</head>

<body>
    <h2>Notes - {{ $eleve->nom }} {{ $eleve->prenom }}</h2>

    <p>Classe : {{ $eleve->inscription->classe->nom_classe }}</p>
    <p>Année Académique : {{ $eleve->inscription->annee_academique->annee }}</p>

    <table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Type d'évaluation</th>
                <th>Note</th>
                <th>Trimestre</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($notes as $note)
                <tr>
                    <td>{{ $note->matiere->nom }}</td>
                    <td>{{ $note->type_evaluation }}</td>
                    <td>{{ $note->valeur_note }}</td>
                    <td>{{ $note->trimestre->nom }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>



    <p style="margin-top: 20px;">Cordialement, L'administration</p>
</body>

</html>
