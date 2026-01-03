@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Emplois du Temps</h3>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Liste des Classes et Emplois du Temps</h4>
                        <table id="agentsQuotaTable" class="table table-dark table-hover" style="width:100%" >
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Année Académique</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $emploisGroupes = $emplois->groupBy(function ($item) {
                                        return $item->classe_id . '-' . $item->annee_academique_id;
                                    });

                                    $classesUniques = [];
                                    foreach ($emploisGroupes as $key => $group) {
                                        $keys = explode('-', $key);
                                        $classeId = $keys[0];
                                        $anneeId = $keys[1];
                                        $classe = \App\Models\Classe::find($classeId);
                                        $annee = \App\Models\Annee_Academique::find($anneeId); // Corrige vers AnneeAcademique
                                        if ($classe && $annee && !isset($classesUniques[$classeId . '-' . $anneeId])) {
                                            $classesUniques[$classeId . '-' . $anneeId] = ['classe' => $classe, 'annee' => $annee];
                                        }
                                    }
                                @endphp
                                @foreach ($classesUniques as $item)
                                    <tr>
                                        <td>{{ $item['classe']->nom_classe }}</td>
                                        <td>{{ $item['annee']->annee }}</td>
                                        <td>
                                            <a href="{{ route('emploi_temps.hebdomadaire', ['classeId' => $item['classe']->id, 'anneeAcademiqueId' => $item['annee']->id]) }}" class="btn btn-info btn-sm me-2">Voir Emploi du Temps</a>
                                            <a href="{{ route('emploi_temps.edit', ['classeId' => $item['classe']->id, 'anneeAcademiqueId' => $item['annee']->id]) }}" class="btn btn-warning btn-sm">Modifier</a>
                                        </td>
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

@section('scripts')
        <style>
            .table-dark.table-hover tbody tr:hover {
                background-color: rgba(75, 84, 92, 0.7) !important;
                /* Gris foncé légèrement transparent */
                transition: background-color 0.3s ease;
            }
        </style>

        <!-- CSS DataTables -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

        <!-- JS DataTables -->
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#agentsQuotaTable').DataTable({
                    // Configuration française
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                    },
                    // Options d'affichage
                    dom: '<"top"<"row"<"col-md-6"l><"col-md-6"f>><"row"<"col-md-12"B>>>rt<"bottom"ip>',
                    buttons: [{
                            extend: 'excel',
                            className: 'btn btn-inverse-info btn-fw',
                            text: '<i class="mdi mdi-file-excel"></i> Excel'
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-inverse-danger btn-fw',
                            text: '<i class="mdi mdi-file-pdf"></i> PDF'
                        }
                    ],
                    // Personnalisation du style
                    initComplete: function() {
                        $('.dataTables_filter input').addClass('form-control');
                        $('.dataTables_length select').addClass('form-control');
                    }

                });
            });
        </script>
    @endsection

