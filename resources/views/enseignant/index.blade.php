@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Liste des enseignants</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Effectif total : {{ $effectif_total }}</li>
                    <li class="breadcrumb-item">Hommes : {{ $hommes }}</li>
                    <li class="breadcrumb-item">Femmes : {{ $femmes }}</li>
                </ol>
            </nav>
        </div>

        @if (Session::get('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ Session::get('success_message') }}

            </div>
        @endif

        @if (session('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_message') }}

            </div>
        @endif

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="agentsQuotaTable" class="table table-dark table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Photo</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Sexe</th>
                                        <th>Matricule</th>
                                        <th>Téléphone</th>
                                        <th>Diplomes</th>
                                        <th>Matiere</th>
                                        <th>Email</th>
                                        <th>Adresse</th>
                                        <th>Classes assignées</th>
                                        <th>Année académique</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($enseignants as $enseignant)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ $enseignant->user->photo
                                                        ? asset('storage/' . $enseignant->user->photo)
                                                        : asset('images/default-avatar.jpg') }}"
                                                        alt="Photo de l'enseignant" width="150" height="150">
                                            </td>
                                            <td>{{ $enseignant->user->name }}</td>
                                            <td>{{ $enseignant->user->prenom }}</td>
                                            <td>{{ $enseignant->sexe }}</td>
                                            <td>{{ $enseignant->matricule }}</td>
                                            <td>{{ $enseignant->telephone }}</td>
                                            <td>{{ $enseignant->diplomes }}</td>
                                            <td>{{ $enseignant->matiere ? $enseignant->matiere->nom : 'Non attribué' }}</td>
                                            <td>{{ $enseignant->user->email }}</td>
                                            <td>{{ $enseignant->adresse }}</td>
                                            <td>
                                                @if ($enseignant->classes->isNotEmpty())
                                                    @foreach ($enseignant->classes as $classe)
                                                        {{ $classe->nom_classe }}<br>
                                                    @endforeach
                                                @else
                                                    Non assigné
                                                @endif
                                            </td>
                                            <td>
                                                @if ($enseignant->anneeAcademique->isNotEmpty())
                                                    @foreach ($enseignant->anneeAcademique as $annee)
                                                        {{ $annee->annee }}<br>
                                                    @endforeach
                                                @else
                                                    Non attribuée
                                                @endif
                                            </td>
                                            <td>

                                                <a href="{{ route('enseignant.edit', ['enseignant' => $enseignant->id]) }}"
                                                    type="button" class="btn btn-inverse-primary btn-fw">Edit</a>
                                                <button type="button" class="btn btn-inverse-danger btn-fw"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir continuer ?')">Delete</button>
                                                {{-- <a href="{{ route('enseignant.edit', $enseignant->id) }}"
                                                    class="btn btn-inverse-primary btn-sm">Modifier</a> --}}
                                                {{-- <form action="{{ route('enseignants.destroy', $enseignant->id) }}"
                                                    method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-inverse-danger btn-sm"
                                                        onclick="return confirm('Voulez-vous vraiment supprimer cet enseignant ?')">
                                                        Supprimer
                                                    </button>
                                                </form> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Aucun enseignant trouvé.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- <nav class="mt-3">
                            {{ $enseignants->links() }}
                        </nav> --}}
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
