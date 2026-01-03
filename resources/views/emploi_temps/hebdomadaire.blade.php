@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Emploi du Temps Hebdomadaire - Classe : {{ $classe->nom_classe }} - Année :
                {{ $anneeAcademique->annee }}</h3>
        </div>

        <div class="row">
            <div class="col-12">
                <a href="{{ route('emploi_temps.exportPdf', ['classeId' => $classe->id, 'anneeAcademiqueId' => $anneeAcademique->id]) }}"
                    class="btn btn-success mb-3">
                    <i class="bi bi-file-earmark-pdf"></i> Exporter PDF
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
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
                                    // Récupérer les créneaux uniques enregistrés
                                    $creneaux = $emplois
                                        ->flatMap(function ($jourEmplois) {
                                            return $jourEmplois->map(function ($emploi) {
                                                return $emploi->heure_debut . ' - ' . $emploi->heure_fin;
                                            });
                                        })
                                        ->unique()
                                        ->sort()
                                        ->values();

                                    // Ajouter un créneau par défaut si aucun n'est trouvé
if ($creneaux->isEmpty()) {
    $creneaux = collect(['06:00 - 07:00']);
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
                                                $cours = $emplois[$jour] ?? collect();
                                                // Trouver un cours exact pour ce créneau
                                                $coursDuJour = $cours->first(function ($emploi) use ($debut, $fin) {
                                                    return $emploi->heure_debut === $debut &&
                                                        $emploi->heure_fin === $fin;
                                                });
                                            @endphp
                                            <td>
                                                @if ($coursDuJour)
                                                    <strong> {{ $coursDuJour->matiere->nom }}</strong><br>
                                                    
                                                @else
                                                    Libre
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
