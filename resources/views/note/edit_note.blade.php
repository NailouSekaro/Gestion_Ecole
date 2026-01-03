@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">✏️ Modification de la note</h3>
        </div>

        @if (session('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <!-- Informations contextuelles -->
                        <div class="alert alert-light mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>👤 Élève :</strong> {{ $note->eleve->nom }} {{ $note->eleve->prenom }}<br>
                                    <strong>🏫 Classe :</strong> {{ $note->classe->nom_classe ?? 'N/A' }}<br>
                                </div>
                                <div class="col-md-6">
                                    <strong>📚 Matière :</strong> {{ $note->matiere->nom }}<br>
                                    <strong>📅 Trimestre :</strong> {{ $note->trimestre->nom }}<br>
                                    <strong>📋 Type :</strong> {{ $note->type_evaluation }}
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire de modification -->
                        <form method="POST" action="{{ route('note.update_note', $note->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="valeur_note"><strong>Note actuelle : {{ $note->valeur_note }}/20</strong></label>
                                <div class="input-group input-group-lg">
                                    <input type="number"
                                           class="form-control form-control-lg @error('valeur_note') is-invalid @enderror"
                                           id="valeur_note"
                                           name="valeur_note"
                                           value="{{ old('valeur_note', $note->valeur_note) }}"
                                           min="0"
                                           max="20"
                                           step="0.01"
                                           required
                                           autofocus>
                                    <div class="input-group-append">
                                        <span class="input-group-text">/20</span>
                                    </div>
                                </div>
                                @error('valeur_note')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Saisissez la nouvelle note entre 0 et 20</small>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success btn-lg mr-2">
                                    <i class="mdi mdi-check"></i> Enregistrer la modification
                                </button>
                                <a href="{{ route('note.voir_classe', ['classe_id' => $note->classe_id, 'annee_academique_id' => $note->annee_academique_id]) }}"
                                   class="btn btn-secondary btn-lg">
                                    <i class="mdi mdi-close"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control-lg {
            font-size: 2rem;
            text-align: center;
            font-weight: bold;
        }
    </style>
@endsection
