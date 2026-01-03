@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <!-- En-tête -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <h3 class="page-title">👁️ Consultation des notes - {{ $classe->nom_classe }}</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Année : {{ $annee_academique->annee }}</li>
                            <li class="breadcrumb-item active">{{ $eleves->count() }} élève(s)</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('eleve.afficher', ['classe_id' => $classe->id, 'annee_academique_id' => $annee_academique->id]) }}"
                   class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <!-- Messages -->
        @if (session('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle"></i> {{ session('success_message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if (session('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle"></i> {{ session('error_message') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <!-- Formulaire de recherche/filtrage -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">🔍 Filtrer les notes</h4>
                        <form method="GET" action="{{ route('note.rechercher_classe') }}">
                            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
                            <input type="hidden" name="annee_academique_id" value="{{ $annee_academique->id }}">

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="trimestre_id">📅 Trimestre (optionnel)</label>
                                        <select name="trimestre_id" id="trimestre_id" class="form-control">
                                            <option value="">-- Tous les trimestres --</option>
                                            @foreach ($trimestres as $trimestre)
                                                <option value="{{ $trimestre->id }}"
                                                    {{ isset($trimestre_id) && $trimestre_id == $trimestre->id ? 'selected' : '' }}>
                                                    {{ $trimestre->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="matiere_id">📚 Matière (optionnel)</label>
                                        <select name="matiere_id" id="matiere_id" class="form-control">
                                            <option value="">-- Toutes les matières --</option>
                                            @if(isset($matieres))
                                                @foreach ($matieres as $matiere)
                                                    <option value="{{ $matiere->id }}"
                                                        {{ isset($matiere_id) && $matiere_id == $matiere->id ? 'selected' : '' }}>
                                                        {{ $matiere->nom }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="mdi mdi-filter"></i> Filtrer
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des notes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">📋 Liste des notes</h4>
                            @if(isset($notes) && $notes->isNotEmpty())
                                <span class="badge badge-info badge-pill">{{ $notes->count() }} note(s) trouvée(s)</span>
                            @endif
                        </div>

                        <div class="table-responsive">
                            @if(isset($notes) && $notes->isNotEmpty())
                                <table class="table table-hover table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="20%">Élève</th>
                                            <th width="20%">Matière</th>
                                            <th width="15%">Type évaluation</th>
                                            <th width="10%">Note</th>
                                            <th width="15%">Trimestre</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notes as $index => $note)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ strtoupper($note->eleve->nom) }}</strong><br>
                                                    <small class="text-muted">{{ ucfirst($note->eleve->prenom) }}</small>
                                                </td>
                                                <td>{{ $note->matiere->nom }}</td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $note->type_evaluation }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $note->valeur_note >= 10 ? 'badge-success' : 'badge-warning' }} badge-lg">
                                                        {{ $note->valeur_note }}/20
                                                    </span>
                                                </td>
                                                <td>{{ $note->trimestre->nom ?? 'Non défini' }}</td>
                                                <td>
                                                    <a href="{{ route('note.edit_note', $note->id) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="mdi mdi-pencil"></i> Modifier
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @elseif(isset($message))
                                <div class="alert alert-warning text-center">
                                    <i class="mdi mdi-alert"></i> {{ $message }}
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="mdi mdi-information"></i> Utilisez les filtres ci-dessus pour afficher les notes.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques (optionnel) -->
        @if(isset($notes) && $notes->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4>{{ $notes->count() }}</h4>
                            <p class="mb-0">Notes totales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ number_format($notes->avg('valeur_note'), 2) }}</h4>
                            <p class="mb-0">Moyenne générale</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>{{ $notes->max('valeur_note') }}</h4>
                            <p class="mb-0">Note maximale</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>{{ $notes->where('valeur_note', '>=', 10)->count() }}</h4>
                            <p class="mb-0">Notes ≥ 10</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }
    </style>
@endsection
