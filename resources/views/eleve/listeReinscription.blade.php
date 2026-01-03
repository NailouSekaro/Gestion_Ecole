@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Listes des élèves réinscrire.</h3>
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                  <li class="breadcrumb-item">Effectif total : {{ $effectif_total }} </li>
                  <li class="breadcrumb-item">Garçons : {{ $garçon }} </li>
                  <li class="breadcrumb-item">Filles : {{ $fille }} </li>
                  <li class="breadcrumb-item">Passants : {{ $passant}} </li>
                  <li class="breadcrumb-item">Doublants : {{ $doublant }} </li>
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
                                        <th>Photo</th>
                                        <th>Matricule</th>
                                        <th> Nom </th>
                                        <th> Prénom </th>
                                        <th> Sexe </th>
                                        <th> Classe </th>
                                        <th> Date de naissance </th>
                                        <th>Lieu de naissance</th>
                                        <th>Aptitude sport</th>
                                        <th>Email parent</th>
                                        <th>Contact parent</th>
                                        <th>Statut</th>
                                        <th>Année académique</th>
                                        <th> Actions </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reinscriptions as $inscription)
                                        <tr>
                                            <td> {{ $loop->iteration }} </td>
                                            <td><img src="{{ $inscription->eleve->photo
                                                ? asset('storage/' . $inscription->eleve->photo)
                                                : asset('images/default-avatar.jpg') }}"
                                                    alt="Photo de l'élève" width="150" height="150"></td>
                                            <td>{{ $inscription->eleve->matricule_educ_master }}</td>
                                            <td> {{ $inscription->eleve->nom }} </td>
                                            <td> {{ $inscription->eleve->prenom }} </td>
                                            <td> {{ $inscription->eleve->sexe }} </td>
                                            <td>{{ $inscription->classe ? $inscription->classe->nom_classe : 'Non attribué' }}
                                            </td>


                                            <td>{{ $inscription->eleve->date_naissance }}</td>
                                            <td>{{ $inscription->eleve->lieu_de_naissance }}</td>
                                            <td>{{ $inscription->eleve->aptitude_sport }}</td>
                                            <td>{{ $inscription->eleve->email_parent }}</td>
                                            <td>{{ $inscription->eleve->contact_parent }}</td>
                                            <td>
                                                {{ $inscription->statut }}
                                            </td>

                                            <td>{{ $inscription->Annee_academique ? $inscription->Annee_academique->annee : 'Non attribué' }}
                                            </td>

                                            <td>
                                                {{-- <a href="{{ route('eleve.edit', ['eleve' => $eleve->id]) }}" type="button"
                                                    class="btn btn-inverse-primary btn-fw">Edit</a> --}}
                                                <button type="button" class="btn btn-inverse-danger btn-fw"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir continuer ?')">Delete</button>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td> Aucun élève réinscrire. </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav class="app-pagination">
                        {{-- {{ $eleves->links() }} --}}
                    </nav>
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
