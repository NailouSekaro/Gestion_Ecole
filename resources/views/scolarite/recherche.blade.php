@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Effectuez une recherche de paiements</h3>
        </div>

        <form class="forms-sample" method="GET" action="{{ route('paiements.afficher') }}">
            <div class="form-group">
                <label for="exampleSelectGender">Classe</label>
                <select class="form-control" name="classe_id" id="exampleSelectGender">
                    <option></option>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->nom_classe }}</option>
                    @endforeach
                </select>
            </div>

            @error('classe_id')
                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
            @enderror

            <div class="form-group">
                <label for="exampleInputPassword4">Année académique</label>
                <select class="form-control" name="annee_academique_id" id="exampleSelectGender">
                    <option></option>
                    @foreach ($annees as $annee)
                        <option value="{{ $annee->id }}">{{ $annee->annee }}</option>
                    @endforeach
                </select>
            </div>

            @error('annee_academique_id')
                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary mr-2">Rechercher</button>
        </form>

        @if (isset($paiements))
            <div class="row mt-4">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4>Liste des paiements pour la classe et l'année académique sélectionnée</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Élève</th>
                                            <th>Classe</th>
                                            <th>Année académique</th>
                                            <th>Montant total</th>
                                            <th>Montant payé</th>
                                            <th>Reste à payer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($paiements as $index => $paiement)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $paiement->inscription->eleve->nom }}
                                                    {{ $paiement->inscription->eleve->prenom }}</td>
                                                <td>{{ $paiement->inscription->classe->nom_classe }}</td>
                                                <td>{{ $paiement->anneeAcademique->annee }}</td>
                                                <td>{{ number_format($paiement->total_scolarite, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    <strong
                                                        class="{{ $paiement->reste_a_payer == 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($paiement->reste_a_payer, 0, ',', ' ') }} FCFA
                                                    </strong>
                                                </td>
                                                <td><a href="{{ route('paiements.details', ['inscription_id' => $paiement->inscription_id, 'annee_academique_id' => $paiement->annee_academique_id]) }}"
                                                        class="btn btn-warning btn-sm">
                                                        Voir détails
                                                    </a></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Aucun paiement enregistré pour cette
                                                    classe et cette année académique.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    
@endsection
