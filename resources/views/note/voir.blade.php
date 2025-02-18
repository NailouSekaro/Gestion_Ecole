@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            {{-- <h3 class="page-title">Notes de {{ $eleve->nom }} {{ $eleve->prenom }} pour l'année académique
                {{ $annee_academique->annee }} </h3> --}}
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Tables</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Basic tables</li>
                </ol> --}}
            </nav>
        </div>

        <form class="forms-sample" method="GET" action="{{ route('note.rechercher') }}">
            <input type="hidden" name="eleve_id" value="{{ $eleve->id }}"> <!-- Champ caché pour l'élève -->

            <div class="form-group">
                <label for="exampleSelectGender">Année académique</label>
                <select class="form-control" name="annee_academique_id" id="exampleSelectGender">
                    <option value="">-- Sélectionnez une année académique (optionnel) --</option>
                    @foreach ($annees as $annee)
                        <option value="{{ $annee->id }}"
                            {{ isset($anneeAcademique) && $anneeAcademique->id == $annee->id ? 'selected' : '' }}>
                            {{ $annee->annee }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('annee_academique_id')
                <div style="color: rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary mr-2">Rechercher</button>
        </form>

        <!-- Section pour afficher les notes -->
        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <p class="card-description">
                        </p>
                        <div class="table-responsive">

                            @if (isset($notes) && $notes->isNotEmpty())
                                <div class="table-responsive">
                                    <h4>Notes de {{ $eleve->nom }} {{ $eleve->prenom }} pour
                                        {{ $annee_academique->annee }}</h4>
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
                                            @forelse($notes as $matiereNom => $notesMatiere)
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
                                                    <td colspan="5">Aucune note disponible pour {{ $eleve->nom }}
                                                        {{ $eleve->prenom }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @elseif (isset($message))
                                <tr>
                                    <td colspan="5">{{ $message }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="5">Aucun résultat disponible. Veuillez effectuer une recherche pour
                                        afficher les notes.</td>
                                </tr>
                            @endif


                        </div>
                    </div>
                    <nav class="app-pagination">
                        {{-- {{ $notes->links() }} --}}
                    </nav>
                </div>
            </div>

        </div>
    @endsection
