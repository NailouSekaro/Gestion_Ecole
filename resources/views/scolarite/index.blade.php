@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Liste des paiements</h3>
        </div>

        @if (session('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                        @php
                                            $resteAPayer = max(0, $paiement->frais_scolarite - $paiement->montant_paye);
                                        @endphp
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
                                                    class="{{ $paiement->total_scolarite - $paiement->montant_paye == 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($paiement->total_scolarite - $paiement->montant_paye, 0, ',', ' ') }}
                                                    FCFA
                                                </strong>
                                            </td>

                                            <td>
                                                <a href="{{ route('paiements.details', ['inscription_id' => $paiement->inscription_id, 'annee_academique_id' => $paiement->annee_academique_id]) }}"
                                                    class="btn btn-warning btn-sm">
                                                    Voir détails
                                                </a>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Aucun paiement enregistré</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav class="app-pagination col-lg-1">
                        {{-- {{ $paiements->links() }} --}}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
