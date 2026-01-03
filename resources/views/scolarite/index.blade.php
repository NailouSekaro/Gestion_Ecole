@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Liste des paiements</h3>
        </div>

        @if (session('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="agentsQuotaTable" class="table table-dark table-hover" style="width:100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Élève</th>
                                        <th>Classe</th>
                                        <th>Année académique</th>
                                        <th>Montant total</th>
                                        <th>Montant payé</th>
                                        <th>Reste à payer</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paiements as $index => $paiement)
                                        @php
                                            $resteAPayer = max(0, $paiement->frais_scolarite - $paiement->montant_paye);
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $paiement->inscription->eleve->nom }}
                                                {{ $paiement->inscription->eleve->prenom }}</td>
                                            <td>{{ $paiement->inscription->classe->nom_classe }}</td>
                                            <td>{{ $paiement->anneeAcademique->annee }}</td>
                                            <td>{{ number_format($paiement->total_scolarite, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</td>
                                            <td>
                                                <strong
                                                    class="{{ $paiement->total_scolarite - $paiement->montant_paye == 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($paiement->total_scolarite - $paiement->montant_paye, 0, ',', ' ') }}
                                                    FCFA
                                                </strong>
                                            </td>

                                            <td>
                                                <a href="{{ route('paiements.details', ['inscription_id' => $paiement->inscription_id, 'annee_academique_id' => $paiement->annee_academique_id]) }}"
                                                    class="btn btn-warning btn-sm">
                                                    Voir détails
                                                </a>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Aucun paiement enregistré</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav class="app-pagination col-lg-1">
                        {{-- {{ $paiements->links() }} --}}
                    </nav>
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
