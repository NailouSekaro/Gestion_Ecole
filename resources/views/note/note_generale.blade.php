@extends('layaouts.template')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- ... autres balises meta ... -->
</head>
@section('content')
    <div class="content-wrapper">
        <!-- En-tête avec retour -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div>
                    <h3 class="page-title">📝 Saisie collective des notes - {{ $classe->nom_classe }}</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Année : {{ $annee_academique->annee }}</li>
                            <li class="breadcrumb-item active">{{ $eleves->count() }} élève(s)</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('eleve.afficher', ['classe_id' => $classe->id, 'annee_academique_id' => $annee_academique->id]) }}"
                    class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>

        <!-- Messages de succès/erreur -->
        @if (session('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-circle"></i> {{ session('error_message') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if (session('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle"></i> {{ session('success_message') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <!-- Formulaire principal -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">🎯 Configuration de la saisie</h4>
                        <p class="card-description">
                            Sélectionnez le trimestre, la matière et le type d'évaluation pour commencer la saisie des notes
                        </p>

                        <form class="forms-sample" method="POST" action="{{ route('note.store_collective') }}"
                            id="notesForm">
                            @csrf

                            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
                            <input type="hidden" name="annee_academique_id" value="{{ $annee_academique->id }}">

                            <!-- Sélecteurs principaux -->
                            <div class="row">
                                <!-- Trimestre -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="trimestre_id"><strong>📅 Trimestre *</strong></label>
                                        <select name="trimestre_id" id="trimestre_id" class="form-control form-control-lg"
                                            required>
                                            <option value="">-- Sélectionner --</option>
                                            @foreach ($trimestres as $trimestre)
                                                <option value="{{ $trimestre->id }}">
                                                    {{ $trimestre->nom }} ({{ $trimestre->date_debut->format('d/m/Y') }} -
                                                    {{ $trimestre->date_fin->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Matière -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="matiere_id"><strong>📚 Matière *</strong></label>
                                        <select name="matiere_id" id="matiere_id" class="form-control form-control-lg"
                                            required>
                                            <option value="">-- Sélectionner --</option>
                                            @foreach ($matieres as $matiere)
                                                <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">
                                            @if (auth()->user()->role === 'enseignant')
                                                ⚠️ Vous ne voyez que vos matières assignées
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                <!-- Type d'évaluation -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type_evaluation"><strong>📋 Type d'évaluation *</strong></label>
                                        <select name="type_evaluation" id="type_evaluation"
                                            class="form-control form-control-lg" required>
                                            <option value="">-- Sélectionner --</option>
                                            <option value="interrogation 1">📝 Interrogation 1</option>
                                            <option value="interrogation 2">📝 Interrogation 2</option>
                                            <option value="interrogation 3">📝 Interrogation 3</option>
                                            <option value="Devoir 1">📄 Devoir 1</option>
                                            <option value="Devoir 2">📄 Devoir 2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Barre d'outils -->
                            <div id="toolbar" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <button type="button" class="btn btn-info" id="chargerNotes">
                                            <i class="mdi mdi-reload"></i> Charger notes existantes
                                        </button>
                                        <button type="button" class="btn btn-warning" id="remplirAuto">
                                            <i class="mdi mdi-flash"></i> Remplissage rapide
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="effacerTout">
                                            <i class="mdi mdi-eraser"></i> Effacer tout
                                        </button>
                                    </div>
                                    <div>
                                        <span class="badge badge-pill badge-primary"
                                            id="compteur">0/{{ $eleves->count() }} saisies</span>
                                    </div>
                                </div>

                                <!-- Statistiques en temps réel -->
                                <div class="alert alert-light" id="stats" style="display: none;">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <strong>Moyenne classe :</strong> <span id="moyenneClasse">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Note min :</strong> <span id="noteMin">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Note max :</strong> <span id="noteMax">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Taux de réussite :</strong> <span id="tauxReussite">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tableau des élèves -->
                            <div class="table-responsive" id="tableNotes" style="display: none;">
                                <table class="table table-hover table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="10%">Photo</th>
                                            <th width="25%">Nom & Prénom</th>
                                            <th width="15%">Matricule</th>
                                            <th width="20%">Note (/20)</th>
                                            <th width="15%">Statut</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($eleves as $index => $eleve)
                                            <tr id="row-{{ $eleve->id }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <img src="{{ $eleve->photo ? asset('storage/' . $eleve->photo) : asset('images/default-avatar.jpg') }}"
                                                        alt="{{ $eleve->nom }}" class="rounded-circle" width="40"
                                                        height="40">
                                                </td>
                                                <td>
                                                    <strong>{{ strtoupper($eleve->nom) }}</strong><br>
                                                    <small class="text-muted">{{ ucfirst($eleve->prenom) }}</small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-secondary">{{ $eleve->matricule_educ_master ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control note-input"
                                                            name="notes[{{ $eleve->id }}]"
                                                            data-eleve-id="{{ $eleve->id }}"
                                                            data-eleve-nom="{{ $eleve->nom }} {{ $eleve->prenom }}"
                                                            placeholder="0.00" min="0" max="20"
                                                            step="0.01" autocomplete="off">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">/20</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-badge" data-eleve-id="{{ $eleve->id }}">
                                                        <span class="badge badge-secondary">Non saisi</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-effacer"
                                                        data-eleve-id="{{ $eleve->id }}" title="Effacer cette note">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Boutons de soumission -->
                            <div class="mt-4" id="btnSubmit" style="display: none;">
                                <button type="submit" class="btn btn-success btn-lg mr-2">
                                    <i class="mdi mdi-check-all"></i> Enregistrer toutes les notes
                                </button>
                                <button type="button" class="btn btn-secondary btn-lg"
                                    onclick="window.location.reload()">
                                    <i class="mdi mdi-refresh"></i> Réinitialiser
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <h5><i class="mdi mdi-information"></i> Instructions d'utilisation :</h5>
                        <ol class="mb-0">
                            <li>Sélectionnez le <strong>trimestre</strong>, la <strong>matière</strong> et le <strong>type
                                    d'évaluation</strong></li>
                            <li>Le tableau des élèves s'affichera automatiquement</li>
                            <li>Cliquez sur <strong>"Charger notes existantes"</strong> pour modifier des notes déjà
                                enregistrées</li>
                            <li>Utilisez le <strong>"Remplissage rapide"</strong> pour attribuer la même note à tous les
                                élèves</li>
                            <li>Les notes vides ne seront pas enregistrées (utile pour saisie partielle)</li>
                            <li>Les statistiques se mettent à jour en temps réel</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .note-input {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .note-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
            transform: scale(1.05);
        }

        .note-input.note-valide {
            border-color: #28a745;
            background-color: #f0fff4;
        }

        .note-input.note-faible {
            border-color: #ffc107;
            background-color: #fffbf0;
        }

        .note-input.note-invalide {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #343a40;
            z-index: 10;
        }

        .btn-effacer {
            opacity: 0;
            transition: opacity 0.2s;
        }

        tr:hover .btn-effacer {
            opacity: 1;
        }

        #stats {
            font-size: 1.1rem;
        }

        .rounded-circle {
            object-fit: cover;
            border: 2px solid #dee2e6;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trimestre = document.getElementById('trimestre_id');
            const matiere = document.getElementById('matiere_id');
            const typeEval = document.getElementById('type_evaluation');
            const table = document.getElementById('tableNotes');
            const btnSubmit = document.getElementById('btnSubmit');
            const toolbar = document.getElementById('toolbar');
            const statsDiv = document.getElementById('stats');
            const compteur = document.getElementById('compteur');

            let notesData = {};

            // Afficher le tableau quand tous les champs sont sélectionnés
            function checkSelections() {
                if (trimestre.value && matiere.value && typeEval.value) {
                    table.style.display = 'block';
                    btnSubmit.style.display = 'block';
                    toolbar.style.display = 'block';
                    statsDiv.style.display = 'block';
                } else {
                    table.style.display = 'none';
                    btnSubmit.style.display = 'none';
                    toolbar.style.display = 'none';
                    statsDiv.style.display = 'none';
                }
            }

            trimestre.addEventListener('change', checkSelections);
            matiere.addEventListener('change', checkSelections);
            typeEval.addEventListener('change', checkSelections);

            // Gestion des inputs de notes
            document.querySelectorAll('.note-input').forEach(input => {
                input.addEventListener('input', function() {
                    updateNote(this);
                    updateStats();
                });

                // Navigation avec Enter
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const nextInput = this.closest('tr').nextElementSibling?.querySelector(
                            '.note-input');
                        if (nextInput) nextInput.focus();
                    }
                });
            });

            // Mise à jour d'une note individuelle
            function updateNote(input) {
                const eleveId = input.dataset.eleveId;
                const valeur = parseFloat(input.value);
                const statusBadge = document.querySelector(`.status-badge[data-eleve-id="${eleveId}"]`);

                // Supprimer toutes les classes
                input.classList.remove('note-valide', 'note-faible', 'note-invalide');

                if (!input.value) {
                    statusBadge.innerHTML = '<span class="badge badge-secondary">Non saisi</span>';
                    delete notesData[eleveId];
                } else if (isNaN(valeur) || valeur < 0 || valeur > 20) {
                    input.classList.add('note-invalide');
                    statusBadge.innerHTML = '<span class="badge badge-danger">❌ Invalide</span>';
                    delete notesData[eleveId];
                } else {
                    notesData[eleveId] = valeur;

                    if (valeur >= 10) {
                        input.classList.add('note-valide');
                        statusBadge.innerHTML = '<span class="badge badge-success">✅ Admis</span>';
                    } else {
                        input.classList.add('note-faible');
                        statusBadge.innerHTML = '<span class="badge badge-warning">⚠️ Échec</span>';
                    }
                }
            }

            // Mise à jour des statistiques
            function updateStats() {
                const notes = Object.values(notesData);
                const count = notes.length;

                compteur.textContent = `${count}/{{ $eleves->count() }} saisies`;

                if (count === 0) {
                    document.getElementById('moyenneClasse').textContent = '-';
                    document.getElementById('noteMin').textContent = '-';
                    document.getElementById('noteMax').textContent = '-';
                    document.getElementById('tauxReussite').textContent = '-';
                    return;
                }

                const moyenne = (notes.reduce((a, b) => a + b, 0) / count).toFixed(2);
                const min = Math.min(...notes).toFixed(2);
                const max = Math.max(...notes).toFixed(2);
                const reussite = ((notes.filter(n => n >= 10).length / count) * 100).toFixed(1);

                document.getElementById('moyenneClasse').textContent = moyenne + '/20';
                document.getElementById('noteMin').textContent = min;
                document.getElementById('noteMax').textContent = max;
                document.getElementById('tauxReussite').textContent = reussite + '%';
            }

            // Remplissage automatique
            document.getElementById('remplirAuto').addEventListener('click', function() {
                const note = prompt('Entrez la note à attribuer à tous les élèves (0-20):');
                if (note !== null && !isNaN(note)) {
                    const valeur = parseFloat(note);
                    if (valeur >= 0 && valeur <= 20) {
                        document.querySelectorAll('.note-input').forEach(input => {
                            input.value = valeur.toFixed(2);
                            updateNote(input);
                        });
                        updateStats();
                    } else {
                        alert('❌ La note doit être entre 0 et 20');
                    }
                }
            });

            // Effacer tout
            document.getElementById('effacerTout').addEventListener('click', function() {
                if (confirm('⚠️ Voulez-vous vraiment effacer toutes les notes saisies ?')) {
                    document.querySelectorAll('.note-input').forEach(input => {
                        input.value = '';
                        updateNote(input);
                    });
                    notesData = {};
                    updateStats();
                }
            });

            // Effacer une note individuelle
            document.querySelectorAll('.btn-effacer').forEach(btn => {
                btn.addEventListener('click', function() {
                    const eleveId = this.dataset.eleveId;
                    const input = document.querySelector(`.note-input[data-eleve-id="${eleveId}"]`);
                    input.value = '';
                    updateNote(input);
                    updateStats();
                });
            });

            // Charger les notes existantes
            document.getElementById('chargerNotes').addEventListener('click', function() {
                const trimestre_id = trimestre.value;
                const matiere_id = matiere.value;
                const type_evaluation = typeEval.value;

                if (!trimestre_id || !matiere_id || !type_evaluation) {
                    alert('⚠️ Veuillez sélectionner le trimestre, la matière et le type d\'évaluation.');
                    return;
                }

                this.disabled = true;
                this.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Chargement...';

                fetch(
                        `/notes/get-notes?trimestre_id=${trimestre_id}&matiere_id=${matiere_id}&type_evaluation=${type_evaluation}&classe_id={{ $classe->id }}&annee_academique_id={{ $annee_academique->id }}`
                        )
                    .then(response => response.json())
                    .then(data => {
                        if (data.notes && Object.keys(data.notes).length > 0) {
                            Object.keys(data.notes).forEach(eleve_id => {
                                const input = document.querySelector(
                                    `.note-input[data-eleve-id="${eleve_id}"]`);
                                if (input) {
                                    input.value = data.notes[eleve_id];
                                    updateNote(input);
                                }
                            });
                            updateStats();
                            alert(
                                `✅ ${Object.keys(data.notes).length} note(s) chargée(s) avec succès !`
                                );
                        } else {
                            alert('ℹ️ Aucune note existante pour cette configuration.');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('❌ Erreur lors du chargement des notes.');
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = '<i class="mdi mdi-reload"></i> Charger notes existantes';
                    });
            });

            // Validation avant soumission
            document.getElementById('notesForm').addEventListener('submit', function(e) {
                const count = Object.keys(notesData).length;

                if (count === 0) {
                    e.preventDefault();
                    alert('⚠️ Veuillez saisir au moins une note avant d\'enregistrer.');
                    return false;
                }

                if (!confirm(`Voulez-vous enregistrer ${count} note(s) ?`)) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>

    {{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const trimestre = document.getElementById('trimestre_id');
        const matiere = document.getElementById('matiere_id');
        const typeEval = document.getElementById('type_evaluation');
        const table = document.getElementById('tableNotes');
        const btnSubmit = document.getElementById('btnSubmit');
        const toolbar = document.getElementById('toolbar');
        const statsDiv = document.getElementById('stats');
        const compteur = document.getElementById('compteur');

        let notesData = {};

        // Afficher le tableau quand tous les champs sont sélectionnés
        function checkSelections() {
            if (trimestre.value && matiere.value && typeEval.value) {
                table.style.display = 'block';
                btnSubmit.style.display = 'block';
                toolbar.style.display = 'block';
                statsDiv.style.display = 'block';
            } else {
                table.style.display = 'none';
                btnSubmit.style.display = 'none';
                toolbar.style.display = 'none';
                statsDiv.style.display = 'none';
            }
        }

        trimestre.addEventListener('change', checkSelections);
        matiere.addEventListener('change', checkSelections);
        typeEval.addEventListener('change', checkSelections);

        // Gestion des inputs de notes
        document.querySelectorAll('.note-input').forEach(input => {
            input.addEventListener('input', function() {
                updateNote(this);
                updateStats();
            });

            // Navigation avec Enter
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const nextInput = this.closest('tr').nextElementSibling?.querySelector('.note-input');
                    if (nextInput) nextInput.focus();
                }
            });
        });

        // Mise à jour d'une note individuelle
        function updateNote(input) {
            const eleveId = input.dataset.eleveId;
            const valeur = parseFloat(input.value);
            const statusBadge = document.querySelector(`.status-badge[data-eleve-id="${eleveId}"]`);

            // Supprimer toutes les classes
            input.classList.remove('note-valide', 'note-faible', 'note-invalide');

            if (!input.value) {
                statusBadge.innerHTML = '<span class="badge badge-secondary">Non saisi</span>';
                delete notesData[eleveId];
            } else if (isNaN(valeur) || valeur < 0 || valeur > 20) {
                input.classList.add('note-invalide');
                statusBadge.innerHTML = '<span class="badge badge-danger">❌ Invalide</span>';
                delete notesData[eleveId];
            } else {
                notesData[eleveId] = valeur;

                if (valeur >= 10) {
                    input.classList.add('note-valide');
                    statusBadge.innerHTML = '<span class="badge badge-success">✅ Admis</span>';
                } else {
                    input.classList.add('note-faible');
                    statusBadge.innerHTML = '<span class="badge badge-warning">⚠️ Échec</span>';
                }
            }
        }

        // Mise à jour des statistiques
        function updateStats() {
            const notes = Object.values(notesData);
            const count = notes.length;

            compteur.textContent = `${count}/{{ $eleves->count() }} saisies`;

            if (count === 0) {
                document.getElementById('moyenneClasse').textContent = '-';
                document.getElementById('noteMin').textContent = '-';
                document.getElementById('noteMax').textContent = '-';
                document.getElementById('tauxReussite').textContent = '-';
                return;
            }

            const moyenne = (notes.reduce((a, b) => a + b, 0) / count).toFixed(2);
            const min = Math.min(...notes).toFixed(2);
            const max = Math.max(...notes).toFixed(2);
            const reussite = ((notes.filter(n => n >= 10).length / count) * 100).toFixed(1);

            document.getElementById('moyenneClasse').textContent = moyenne + '/20';
            document.getElementById('noteMin').textContent = min;
            document.getElementById('noteMax').textContent = max;
            document.getElementById('tauxReussite').textContent = reussite + '%';
        }

        // Remplissage automatique
        document.getElementById('remplirAuto').addEventListener('click', function() {
            const note = prompt('Entrez la note à attribuer à tous les élèves (0-20):');
            if (note !== null && !isNaN(note)) {
                const valeur = parseFloat(note);
                if (valeur >= 0 && valeur <= 20) {
                    document.querySelectorAll('.note-input').forEach(input => {
                        input.value = valeur.toFixed(2);
                        updateNote(input);
                    });
                    updateStats();
                } else {
                    alert('❌ La note doit être entre 0 et 20');
                }
            }
        });

        // Effacer tout
        document.getElementById('effacerTout').addEventListener('click', function() {
            if (confirm('⚠️ Voulez-vous vraiment effacer toutes les notes saisies ?')) {
                document.querySelectorAll('.note-input').forEach(input => {
                    input.value = '';
                    updateNote(input);
                });
                notesData = {};
                updateStats();
            }
        });

        // Effacer une note individuelle
        document.querySelectorAll('.btn-effacer').forEach(btn => {
            btn.addEventListener('click', function() {
                const eleveId = this.dataset.eleveId;
                const input = document.querySelector(`.note-input[data-eleve-id="${eleveId}"]`);
                input.value = '';
                updateNote(input);
                updateStats();
            });
        });

        // Charger les notes existantes
        document.getElementById('chargerNotes').addEventListener('click', function() {
            const trimestre_id = trimestre.value;
            const matiere_id = matiere.value;
            const type_evaluation = typeEval.value;

            if (!trimestre_id || !matiere_id || !type_evaluation) {
                alert('⚠️ Veuillez sélectionner le trimestre, la matière et le type d\'évaluation.');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Chargement...';

            // Construction de l'URL avec les paramètres (encodeURIComponent pour les espaces)
            const url = `/notes/get-notes?trimestre_id=${trimestre_id}&matiere_id=${matiere_id}&type_evaluation=${encodeURIComponent(type_evaluation)}&classe_id={{ $classe->id }}&annee_academique_id={{ $annee_academique->id }}`;

            console.log('📡 Requête vers:', url);

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('📥 Statut réponse:', response.status);

                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('❌ Réponse brute:', text);
                            throw new Error(
                                `Erreur HTTP ${response.status}: ${response.statusText}`);
                        });
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('📊 Données reçues:', data);

                    if (data.success === false) {
                        alert('❌ ' + (data.message || 'Erreur lors du chargement'));
                        return;
                    }

                    if (data.notes && Object.keys(data.notes).length > 0) {
                        let compteur = 0;

                        Object.keys(data.notes).forEach(eleve_id => {
                            const input = document.querySelector(
                                `.note-input[data-eleve-id="${eleve_id}"]`);
                            if (input) {
                                input.value = data.notes[eleve_id];
                                updateNote(input);
                                compteur++;
                            } else {
                                console.warn(`⚠️ Input non trouvé pour élève ID: ${eleve_id}`);
                            }
                        });

                        updateStats();
                        alert(`✅ ${compteur} note(s) chargée(s) avec succès !`);
                    } else {
                        alert('ℹ️ Aucune note existante pour cette configuration.');
                    }
                })
                .catch(error => {
                    console.error('❌ Erreur détaillée:', error);
                    alert(
                        '❌ Erreur lors du chargement des notes.\n\nDétails: ' + error.message +
                        '\n\nVérifiez la console (F12) pour plus d\'informations.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="mdi mdi-reload"></i> Charger notes existantes';
                });
        });

        // Validation avant soumission
        document.getElementById('notesForm').addEventListener('submit', function(e) {
            const count = Object.keys(notesData).length;

            if (count === 0) {
                e.preventDefault();
                alert('⚠️ Veuillez saisir au moins une note avant d\'enregistrer.');
                return false;
            }

            if (!confirm(`Voulez-vous enregistrer ${count} note(s) ?`)) {
                e.preventDefault();
                return false;
            }
        });
    });
</script> --}}
@endsection
