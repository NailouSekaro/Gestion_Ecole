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
                            <table class="table table-dark">
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
                                                <img src="{{ asset('storage/' . $enseignant->user->photo) }}"
                                                    alt="Photo de l'enseignant" width="50" height="50">
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
                        <nav class="mt-3">
                            {{ $enseignants->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
