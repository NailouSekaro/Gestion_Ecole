@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Détails des paiements de {{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }}
            </h3>
            <a href="{{ route('paiement.index') }}" class="btn btn-secondary">Retour</a>
        </div>

        <div class="row">
            <!-- Tableau des informations de l'élève -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Informations de l'élève</h4>
                        <div class="table-responsive">
                            <table  class="table table-dark">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Classe</th>
                                        <th>Année académique</th>
                                        <th>Montant total</th>
                                        <th>Montant payé</th>
                                        <th>Reste à payer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }}</td>
                                        <td>{{ $inscription->classe->nom_classe }}</td>
                                        <td>{{ $paiements->first()->anneeAcademique->annee ?? 'N/A' }}</td>
                                        <td>{{ number_format($total_scolarite, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($montant_paye, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            <span class="{{ $reste_a_payer == 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($reste_a_payer, 0, ',', ' ') }} FCFA
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau de l'historique des paiements -->
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Historique des paiements</h4>
                        <div class="table-responsive">
                            <table id="agentsQuotaTable" class="table table-dark table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Montant payé</th>
                                        <th>Moyen de paiement</th>
                                        <th>Transaction ID</th>
                                        <th>Date de paiement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paiements as $paiement)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ ucfirst($paiement->moyen_paiement) }}</td>
                                            <td>{{ $paiement->transaction_id ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y H:i') }}
                                            </td>
                                            <td><a href="{{ route('paiement.recu', $paiement->id) }}"
                                                    class="btn btn-primary btn-sm">Voir le reçu</a>
                                                <a href="{{ route('paiement.download', $paiement->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-download"></i> Télécharger le reçu
                                                </a>

                                                {{-- <a href="{{ route('paiement.edit', $paiement->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    Modifier
                                                </a> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Aucun paiement enregistré</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('paiement.index') }}" class="btn btn-secondary mt-3">Retour à la liste</a>
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
