<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Paiement de Scolarité - {{ $eleve->nom }} {{ $eleve->prenom }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://checkout.fedapay.com/js/checkout.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --success: #4cc9f0;
            --success-dark: #3a8fb8;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --card-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Poppins', sans-serif;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .container {
            max-width: 1000px;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            background: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(120deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            text-align: center;
        }

        .card-body {
            padding: 2rem;
        }

        .nav-tabs {
            border-bottom: 2px solid #e2e8f0;
        }

        .nav-link {
            color: var(--gray);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px 8px 0 0;
            transition: var(--transition);
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: var(--primary-dark);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
            border: none;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark);
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .form-label i {
            margin-right: 0.5rem;
            color: var(--primary);
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #f9fafc;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            background: white;
        }

        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(67, 97, 238, 0.3);
        }

        .btn-success {
            background: linear-gradient(to right, var(--success), var(--success-dark));
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(to right, var(--success-dark), var(--success));
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(76, 201, 240, 0.3);
        }

        .accordion-button {
            font-weight: 500;
            background: #f9fafc;
            border-radius: 8px !important;
        }

        .accordion-button:not(.collapsed) {
            background: var(--primary);
            color: white;
        }

        .file-input-container {
            position: relative;
        }

        .file-input-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            background: #f9fafc;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-input-label:hover {
            border-color: var(--primary);
            background: #edf2f7;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .file-preview {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
            background: #edf2f7;
            display: none;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 6px;
        }

        .bank-info {
            padding: 1rem;
            border-radius: 8px;
            background: #f0f9ff;
            border-left: 4px solid var(--primary);
            margin-bottom: 1rem;
        }

        .required::after {
            content: '*';
            color: #e53e3e;
            margin-left: 4px;
        }

        .invalid-feedback {
            color: #e53e3e;
            font-size: 0.85rem;
            margin-top: 0.4rem;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }

            .nav-tabs {
                flex-direction: column;
            }

            .nav-link {
                width: 100%;
                text-align: center;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h4>
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Paiement de Scolarité pour {{ $eleve->nom }} {{ $eleve->prenom }}
                    (Classe: {{ $eleve->inscriptions->first()->classe->nom_classe ?? 'N/A' }})
                </h4>
            </div>
            <div class="card-body">
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

                <!-- Sélecteur année -->
                <div class="form-group mb-4">
                    <label for="annee-select" class="form-label required">
                        <i class="fas fa-calendar"></i> Année académique
                    </label>
                    <select class="form-control" id="annee-select">
                        @foreach ($annees as $annee)
                            <option value="{{ $annee->id }}" data-reste="{{ $impayes[$annee->id]['reste'] }}"
                                    {{ old('annee_academique_id') == $annee->id ? 'selected' : '' }}>
                                {{ $annee->annee }} (Reste: {{ number_format($impayes[$annee->id]['reste']) }} CFA)
                            </option>
                        @endforeach
                    </select>
                    @error('annee_academique_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Historique paiements -->
                <div class="accordion mb-4" id="historiqueAccordion">
                    @foreach ($impayes as $id => $data)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $id }}">
                                    Historique pour {{ $data['annee'] }} (Reste: {{ number_format($data['reste']) }} CFA)
                                </button>
                            </h2>
                            <div id="collapse{{ $id }}" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Moyen</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['historique'] as $paiement)
                                                <tr>
                                                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($paiement->montant) }} CFA</td>
                                                    <td>{{ ucfirst($paiement->moyen_paiement) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $paiement->statut === 'pending' ? 'warning' : ($paiement->statut === 'paye' ? 'success' : 'secondary') }}">
                                                            {{ ucfirst($paiement->statut ?? 'Approuvé') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tabs pour moyens de paiement -->
                <ul class="nav nav-tabs" id="paiementTabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="fedapay-tab" data-bs-toggle="tab" href="#fedapay">
                            <i class="fas fa-mobile-alt me-1"></i> Mobile Money (FedaPay)
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" id="virement-tab" data-bs-toggle="tab" href="#virement">
                            <i class="fas fa-university me-1"></i> Virement Bancaire
                        </a>
                    </li> --}}
                </ul>

                <div class="tab-content mt-3">
                    <!-- Fedapay -->
                    <div class="tab-pane fade show active" id="fedapay">
                        <form id="fedapay-form" action="{{ route('paiement.parent.fedapay.initiate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="annee_academique_id" id="fedapay-annee"
                                   value="{{ $annees->first()->id ?? '' }}">
                            <div class="form-group">
                                <label for="fedapay-montant" class="form-label required">
                                    <i class="fas fa-money-bill"></i> Montant à payer (CFA)
                                </label>
                                <input type="number" name="montant" id="fedapay-montant"
                                       class="form-control @error('montant') is-invalid @enderror"
                                       required min="1" max="{{ $impayes[$annees->first()->id]['reste'] ?? 0 }}"
                                       value="{{ old('montant') }}">
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block mt-3">
                                <i class="fas fa-check-circle me-1"></i> Payer avec FedaPay
                            </button>
                        </form>
                    </div>

                    <!-- Virement -->
                    <div class="tab-pane fade" id="virement">
                        <div class="bank-info">
                            <p><strong>IBAN :</strong> {{ config('services.bank.iban') }}</p>
                            <p><strong>BIC/SWIFT :</strong> {{ config('services.bank.bic') }}</p>
                            <p><strong>Banque :</strong> {{ config('services.bank.name') }}</p>
                            <p><strong>Référence :</strong> Scolarité {{ $eleve->matricule_educ_master }} - {{ now()->format('Y') }}</p>
                            <p>Veuillez effectuer le virement, puis uploader la preuve ci-dessous.</p>
                        </div>
                        <form id="virement-form" action="{{ route('paiement.parent.virement.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="annee_academique_id" id="virement-annee"
                                   value="{{ $annees->first()->id ?? '' }}">
                            <div class="form-group">
                                <label for="virement-montant" class="form-label required">
                                    <i class="fas fa-money-bill"></i> Montant viré (CFA)
                                </label>
                                <input type="number" name="montant" id="virement-montant"
                                       class="form-control @error('montant') is-invalid @enderror"
                                       required min="1" max="{{ $impayes[$annees->first()->id]['reste'] ?? 0 }}"
                                       value="{{ old('montant') }}">
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="preuve" class="form-label required">
                                    <i class="fas fa-file-upload"></i> Preuve de virement (PDF/Image)
                                </label>
                                <div class="file-input-container">
                                    <label class="file-input-label" for="preuve">
                                        <i class="fas fa-upload"></i>
                                        <span>Glisser-déposer ou cliquer pour sélectionner (JPG, PNG, PDF, max 2MB)</span>
                                    </label>
                                    <input type="file" name="preuve" id="preuve"
                                           class="file-input @error('preuve') is-invalid @enderror"
                                           accept=".jpg,.jpeg,.png,.pdf" required>
                                    @error('preuve')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="file-preview" id="filePreview">
                                        <img id="previewImage" alt="Aperçu de la preuve">
                                        <p id="fileName"></p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block mt-3">
                                <i class="fas fa-check-circle me-1"></i> Soumettre la Demande
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="/" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gestion du sélecteur d'année
            const anneeSelect = document.getElementById('annee-select');
            const fedapayAnnee = document.getElementById('fedapay-annee');
            const virementAnnee = document.getElementById('virement-annee');
            const fedapayMontant = document.getElementById('fedapay-montant');
            const virementMontant = document.getElementById('virement-montant');

            anneeSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const reste = selectedOption.dataset.reste;
                fedapayAnnee.value = this.value;
                virementAnnee.value = this.value;
                fedapayMontant.max = reste;
                virementMontant.max = reste;
            });

            // Déclencher le changement au chargement
            if (anneeSelect.value) {
                anneeSelect.dispatchEvent(new Event('change'));
            }

            // Aperçu du fichier pour virement
            const fileInput = document.getElementById('preuve');
            const filePreview = document.getElementById('filePreview');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    fileName.textContent = file.name;
                    filePreview.style.display = 'block';
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewImage.src = e.target.result;
                            previewImage.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewImage.style.display = 'none';
                    }
                } else {
                    filePreview.style.display = 'none';
                    previewImage.style.display = 'none';
                    fileName.textContent = '';
                }
            });

            // Validation côté client
            const forms = [document.getElementById('fedapay-form'), document.getElementById('virement-form')];
            forms.forEach(form => {
                form.addEventListener('submit', function (event) {
                    let isValid = true;
                    const montantInput = form.querySelector('input[name="montant"]');
                    const montant = parseFloat(montantInput.value);

                    if (!montant || montant <= 0) {
                        montantInput.classList.add('is-invalid');
                        montantInput.nextElementSibling.textContent = 'Le montant doit être supérieur à 0.';
                        isValid = false;
                    } else if (montant > parseFloat(montantInput.max)) {
                        montantInput.classList.add('is-invalid');
                        montantInput.nextElementSibling.textContent = 'Le montant dépasse le reste dû.';
                        isValid = false;
                    } else {
                        montantInput.classList.remove('is-invalid');
                    }

                    if (form.id === 'virement-form') {
                        const preuveInput = form.querySelector('input[name="preuve"]');
                        if (!preuveInput.files.length) {
                            preuveInput.classList.add('is-invalid');
                            preuveInput.nextElementSibling.textContent = 'Veuillez uploader une preuve.';
                            isValid = false;
                        } else {
                            preuveInput.classList.remove('is-invalid');
                        }
                    }

                    if (!isValid) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                });
            });

            // Initialisation des tabs Bootstrap
            const tabList = document.querySelectorAll('.nav-link');
            tabList.forEach(tab => {
                tab.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
                    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
                    target.classList.add('show', 'active');
                    this.classList.add('active');
                });
            });

            // Gestion du checkout FedaPay en popup (si token fourni)
            @if(isset($fedapay_token))
                FedaPay.init({
                    public_key: '{{ $fedapay_public_key }}',
                    transaction: {
                        token: '{{ $fedapay_token }}'
                    },
                    onSuccess: function (transaction) {
                        window.location.href = '{{ route('paiement.parent.callback') }}?payment_id={{ $payment_id }}&status=approved';
                    },
                    onCancel: function () {
                        window.location.href = '{{ route('paiement.parent.index') }}?status=canceled';
                    },
                    onError: function (error) {
                        alert('Erreur lors du paiement: ' + error.message);
                        window.location.href = '{{ route('paiement.parent.index') }}?status=error';
                    }
                });
            @endif
        });
    </script>
</body>
</html>
