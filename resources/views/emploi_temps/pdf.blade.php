<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi du Temps - {{ $classe->nom_classe }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: A4 landscape;
            @bottom-right {
                content: "Page " counter(page) " / " counter(pages);
                font-size: 8px;
                color: #666;
            }
        }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; font-size: 9px; }
        th { background: #e9ecef; font-weight: bold; }
        .creneau-libre { background: #f0f0f0; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Emploi du Temps Hebdomadaire</h2>
        <p>Classe : {{ $classe->nom_classe }} | Année Académique : {{ $anneeAcademique->annee }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Heure</th>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Créneaux uniques des emplois enregistrés
                $creneaux = $emplois->flatMap(function ($jourEmplois) {
                    return $jourEmplois->map(function ($emploi) {
                        return $emploi->heure_debut . ' - ' . $emploi->heure_fin;
                    });
                })->unique()->sort()->values();

                // Créneaux par défaut si aucun
                if ($creneaux->isEmpty()) {
                    $creneaux = collect(['08:00 - 09:00', '09:00 - 10:00', '10:00 - 11:00', '11:00 - 12:00', '14:00 - 15:00', '15:00 - 16:00', '16:00 - 17:00']);
                }
            @endphp
            @foreach ($creneaux as $creneau)
                @php
                    [$debut, $fin] = explode(' - ', $creneau);
                @endphp
                <tr>
                    <td>{{ $debut }} - {{ $fin }}</td>
                    @foreach (['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $jour)
                        @php
                            $coursDuJour = $emplois[$jour] ?? collect();
                            $emploi = $coursDuJour->first(function ($item) use ($debut, $fin) {
                                return $item->heure_debut === $debut && $item->heure_fin === $fin;
                            });
                        @endphp
                        <td class="{{ $emploi ? '' : 'creneau-libre' }}">
                            @if ($emploi)
                                <strong>{{ $emploi->matiere->nom }}</strong><br>
                                <small>{{ $emploi->enseignant->user->name ?? 'Non assigné' }}</small>
                            @else
                                Libre
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
