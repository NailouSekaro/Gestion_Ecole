@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Insertion des notes de {{ $eleve->nom }} {{ $eleve->prenom }} </h3>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Remplir le formulaire</h4>

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                            </div>
                        @endif

                        <p class="card-description">Veuillez saisir les notes</p>

                        <form class="forms-sample" method="POST"
                            action="{{ route('note.store', ['eleve_id' => $eleve->id, 'annee_academique_id' => $annee_academique->id]) }}">
                            @csrf

                            <!-- Sélection du trimestre -->
                            <div class="form-group">
                                <h3>Trimestre</h3>
                                <select name="trimestre_id" class="form-control" required>
                                    <option value="">Sélectionner un trimestre</option>
                                    @foreach ($trimestres as $trimestre)
                                        <option value="{{ $trimestre->id }}">{{ $trimestre->nom }} -
                                            {{ $annee_academique->annee }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="classe_id" value="{{ $classe->id }}">

                            <!-- Note de conduite -->
                            {{-- <h3>Conduite</h3>
                            <div class="form-group">
                                <label>Note de conduite</label>
                                <input type="number" class="form-control" placeholder="Entrer la note de conduite"
                                    name="conduite" value="{{ $conduiteExistante->valeur_note ?? '' }}" min="0" max="20"
                                    step="0.01">
                            </div> --}}

                            <!-- Affichage des matières -->
                            @foreach ($matieres as $matiere)
                                <h3>{{ $matiere->nom }}</h3>

                                @foreach (['interrogation 1', 'interrogation 2', 'interrogation 3', 'Devoir 1', 'Devoir 2'] as $type_evaluation)
                                    @php
                                        $note = $notesExistantes[$matiere->id][$type_evaluation][0] ?? null;
                                    @endphp
                                    <div class="form-group">
                                        <label>{{ ucfirst(str_replace('_', ' ', $type_evaluation)) }}</label>
                                        <input type="number" class="form-control" placeholder="Entrer la note"
                                            name="notes[{{ $matiere->id }}][{{ $type_evaluation }}]"
                                            value="{{ $note ? $note->valeur_note : '' }}" min="0" max="20"
                                            step="0.01">
                                    </div>
                                    @error('notes')
                                        <div style="color:red;"> {{ $message }}</div>
                                    @enderror
                                @endforeach
                            @endforeach

                            <button type="submit" class="btn btn-primary mr-2">Envoyer</button>
                            <button class="btn btn-dark" type="reset">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
