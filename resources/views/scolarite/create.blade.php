@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Effectuer un paiement pour {{ $eleve->nom }} {{ $eleve->prenom }}</h3>
            <nav aria-label="breadcrumb"></nav>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Remplir le formulaire</h4>
                        <p class="card-description">Avec les éléments correspondants</p>

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

                        <form class="forms-sample" action="{{ route('paiement.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                            <input type="hidden" name="annee_academique_id" value="{{ $annee_academique->id }}">
                            <input type="hidden" id="transaction_id" name="transaction_id">


                            <div class="form-group">
                                <label>Élève</label>
                                <input type="text" class="form-control text-dark" value="{{ $eleve->nom }} {{ $eleve->prenom }}"
                                    disabled>
                            </div>

                            <div class="form-group">
                                <label>Année académique</label>
                                <input type="text" class="form-control" value="{{ $annee_academique->annee }}" disabled>
                            </div>

                            <div class="form-group">
                                <label>Montant à payer (CFA)</label>
                                <input type="number" name="montant" class="form-control" required min="1"
                                    max="{{ $resteAPayer }}">
                                @error('montant')
                                    <div style="color:rgba(255, 0, 0, 0.858)">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Moyen de paiement</label>
                                <select class="form-control" name="moyen_paiement" id="moyen_paiement">
                                    <option value="espece">Espèces</option>
                                    <option value="mobile_money">Mobile Money (FedaPay)</option>
                                </select>
                            </div>

                            <div id="fedapay-button-container" style="display: none;">
                                <button type="button" class="btn btn-warning" id="payWithFedaPay">Payer avec Mobile
                                    Money</button>
                            </div> <br>

                            <button type="submit" class="btn btn-primary me-2">Enregistrer le paiement</button>
                            <button type="reset" class="btn btn-light">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.getElementById('moyen_paiement').addEventListener('change', function() {
            document.getElementById('fedapay-button-container').style.display = (this.value === 'mobile_money') ?
                'block' : 'none';
        });

        document.getElementById('payWithFedaPay').addEventListener('click', function() {
            let montant = document.querySelector('input[name="montant"]').value;
            let inscription_id = document.querySelector('input[name="inscription_id"]').value;
            let annee_academique_id = document.querySelector('input[name="annee_academique_id"]').value;

            if (!montant || montant <= 0) {
                alert("Veuillez saisir un montant valide.");
                return;
            }

            window.location.href =
                `/paiement/fedapay/initiate?montant=${montant}&inscription_id=${inscription_id}&annee_academique_id=${annee_academique_id}`;
        });
    </script>


    <!-- Intégration de Kkiapay -->
    {{-- <script src="https://cdn.kkiapay.me/k.js"></script> --}}

    {{-- <script>
        document.getElementById('moyen_paiement').addEventListener('change', function() {
            document.getElementById('kkiapay-button-container').style.display = (this.value === 'mobile_money') ?
                'block' : 'none';
        });

        document.getElementById('payWithKkiapay').addEventListener('click', function() {
            let montant = document.querySelector('input[name="montant"]').value;

            if (!montant || montant <= 0) {
                alert("Veuillez saisir un montant valide.");
                return;
            }

            KkiapayWidget({
                amount: montant,
                sandbox: true, // Mettre à false en production
                api_key: "{{ config('services.kkiapay.public_key') }}",
                callback: function(response) {
                    if (response.transactionId) {
                        window.location.href = "/paiement/kkiapay/success?transaction_id=" + response
                            .transactionId +
                            "&inscription_id=" + document.querySelector('input[name="inscription_id"]')
                            .value +
                            "&annee_academique_id=" + document.querySelector(
                                'input[name="annee_academique_id"]').value +
                            "&montant=" + montant;
                    } else {
                        alert("Paiement annulé !");
                    }
                }
            });
        });
    </script> --}}
@endsection
