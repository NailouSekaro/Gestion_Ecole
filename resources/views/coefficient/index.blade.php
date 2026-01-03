@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Listes des coefficients</h3>
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Tables</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Basic tables</li>
                </ol> --}}
            </nav>
        </div>
        @if (Session::get('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-bs-dismiss="alert"
                aria-label="Close">
                {{ Session::get('success_message') }}

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
                        {{-- <h4 class="card-title"> Classes disponibles </h4> --}}
                        <p class="card-description">
                        </p>
                        <div class="table-responsive">
                            <table id="agentsQuotaTable" class="table table-dark table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Classe </th>
                                        <th> Matiere </th>
                                        <th> Coefficient </th>
                                        {{-- <th> Liste des élèves </th> --}}
                                        {{-- <th> Liste </th> --}}
                                        <th> Actions </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($coefficients as $coefficient)
                                        <tr>
                                            <td> {{ $loop->iteration }} </td>
                                            <td> {{ $coefficient->classe->nom_classe }} </td>
                                            <td> {{ $coefficient->matiere->nom }} </td>
                                            <td> {{ $coefficient->valeur_coefficient }} </td>
                                            {{-- <td> 0 </td> --}}
                                            {{-- <td> <a href="{{ route('eleve.recherche') }}" type="button"
                                                    class="btn btn-inverse-warning btn-fw">Voir liste</a> </td> --}}
                                            <td><a href="{{ route('coefficient.edit', ['coefficient' => $coefficient->id]) }}"
                                                    type="button" class="btn btn-inverse-primary btn-fw">Edit</a>
                                                <button type="button" class="btn btn-inverse-danger btn-fw"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimé cette coefficient ?')">Delete</button>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td> Aucun coefficient ajouté </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- <nav class="app-pagination col-lg-1">
                        {{ $coefficients->links() }}
                    </nav> --}}
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
