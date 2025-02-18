@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Notes de {{ $eleve->nom }} {{ $eleve->prenom }} pour l'année académique
                {{ $annee_academique->annee }} </h3>
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Tables</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Basic tables</li>
                </ol> --}}
            </nav>
        </div>

        <!-- Section pour afficher les notes -->
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @if (Session::get('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                data-bs-dismiss="alert" aria-label="Close">
                                {{ Session::get('success_message') }}

                            </div>
                        @endif
                        <p class="card-description">
                        </p>
                        <div class="table-responsive">

                            <div class="table-responsive">

                                <table class="table table-dark">
                                    <thead>
                                        <tr>
                                            <th>Matières</th>
                                            <th>Types d'évaluations</th>
                                            <th>Notes</th>
                                            <th>Trimestre</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($notesGroupées as $matiereNom => $notesMatiere)
                                            @foreach ($notesMatiere as $note)
                                                <tr>
                                                    <td>{{ $matiereNom }}</td>
                                                    <td>{{ $note->type_evaluation }}</td>
                                                    <td>{{ $note->valeur_note }}</td>
                                                    <td>{{ $note->trimestre->nom ?? 'Non défini' }}</td>
                                                    <td>
                                                        <a href="{{ route('note.create', ['eleve_id' => $eleve->id, 'annee_academique_id' => $annee_academique->id]) }}"
                                                            class="btn btn-primary btn-sm">
                                                            Modifier
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="4">Aucune note disponible pour {{ $eleve->nom }}
                                                    {{ $eleve->prenom }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <nav class="app-pagination">
                        {{-- {{ $notes->links() }} --}}
                    </nav>
                </div>
            </div>

    </div>

    @endsection
