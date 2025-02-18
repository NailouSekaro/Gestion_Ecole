@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Modifier le paiement de {{ $paiement->inscription->eleve->nom }}
                {{ $paiement->inscription->eleve->prenom }}</h3>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Modifier le paiement</h4>

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form class="forms-sample" action="{{ route('paiement.update', $paiement->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="inscription_id" value="{{ $paiement->inscription_id }}">
                            <input type="hidden" name="annee_academique_id" value="{{ $paiement->annee_academique_id }}">

                            <div class="form-group">
                                <label>Élève</label>
                                <input type="text" class="form-control text-dark"
                                    value="{{ $paiement->inscription->eleve->nom }} {{ $paiement->inscription->eleve->prenom }}"
                                    disabled>
                            </div>

                            <div class="form-group">
                                <label>Montant payé (CFA)</label>
                                <input type="number" name="montant" class="form-control" value="{{ $paiement->montant }}"
                                    required min="1">
                            </div>

                            <div class="form-group">
                                <label>Moyen de paiement</label>
                                <select class="form-control" name="moyen_paiement">
                                    <option value="espece" {{ $paiement->moyen_paiement === 'espece' ? 'selected' : '' }}>
                                        Espèces</option>
                                    <option value="mobile_money"
                                        {{ $paiement->moyen_paiement === 'mobile_money' ? 'selected' : '' }}>Mobile Money
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary me-2">Modifier le paiement</button>
                            <a href="{{ route('paiement.index', [$paiement->inscription->eleve_id, $paiement->annee_academique_id]) }}"
                                class="btn btn-light">Annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@endsection
