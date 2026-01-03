@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Modifier Emploi du Temps - Classe : {{ $classe->nom_classe }} - Année : {{ $anneeAcademique->annee }}</h3>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Modifier les Créneaux</h4>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('emploi_temps.update', ['classeId' => $classe->id, 'anneeAcademiqueId' => $anneeAcademique->id]) }}" method="POST" id="edit-form">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Jour</th>
                                            <th>Heure de Début</th>
                                            <th>Heure de Fin</th>
                                            <th>Matière</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emplois-tbody">
                                        @php $index = 0; @endphp
                                        @foreach ($emplois as $jour => $jourEmplois)
                                            @foreach ($jourEmplois as $emploi)
                                                <tr data-emploi-id="{{ $emploi->id }}">
                                                    <td>
                                                        <input type="hidden" name="emplois[{{ $index }}][id]" value="{{ $emploi->id }}">
                                                        <select name="emplois[{{ $index }}][jour]" class="form-control form-select" required>
                                                            <option value="Lundi" {{ $emploi->jour === 'Lundi' ? 'selected' : '' }}>Lundi</option>
                                                            <option value="Mardi" {{ $emploi->jour === 'Mardi' ? 'selected' : '' }}>Mardi</option>
                                                            <option value="Mercredi" {{ $emploi->jour === 'Mercredi' ? 'selected' : '' }}>Mercredi</option>
                                                            <option value="Jeudi" {{ $emploi->jour === 'Jeudi' ? 'selected' : '' }}>Jeudi</option>
                                                            <option value="Vendredi" {{ $emploi->jour === 'Vendredi' ? 'selected' : '' }}>Vendredi</option>
                                                            <option value="Samedi" {{ $emploi->jour === 'Samedi' ? 'selected' : '' }}>Samedi</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="time" name="emplois[{{ $index }}][heure_debut]" class="form-control" value="{{ \Carbon\Carbon::parse($emploi->heure_debut)->format('H:i') }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="time" name="emplois[{{ $index }}][heure_fin]" class="form-control" value="{{ \Carbon\Carbon::parse($emploi->heure_fin)->format('H:i') }}" required>
                                                    </td>
                                                    <td>
                                                        <select name="emplois[{{ $index }}][matiere_id]" class="form-control form-select" required>
                                                            @foreach (\App\Models\Matiere::all() as $matiere)
                                                                <option value="{{ $matiere->id }}" {{ $emploi->matiere_id == $matiere->id ? 'selected' : '' }}>{{ $matiere->nom }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm edit-row" data-emploi-id="{{ $emploi->id }}">
                                                            <i class="bi bi-pencil"></i> Modifier
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm delete-row" data-emploi-id="{{ $emploi->id }}">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </td>
                                                </tr>
                                                @php $index++; @endphp
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                {{-- <button type="button" id="add-row" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Ajouter un Créneau
                                </button> --}}
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Enregistrer Toutes les Modifications
                                </button>
                                <a href="{{ route('emploi_temps.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modification individuelle -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier un Créneau</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-form-modal">
                        <input type="hidden" id="emploi-id">

                        <div class="mb-3">
                            <label for="edit-jour" class="form-label">Jour</label>
                            <select id="edit-jour" class="form-control form-select" required>
                                <option value="Lundi">Lundi</option>
                                <option value="Mardi">Mardi</option>
                                <option value="Mercredi">Mercredi</option>
                                <option value="Jeudi">Jeudi</option>
                                <option value="Vendredi">Vendredi</option>
                                <option value="Samedi">Samedi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit-heure_debut" class="form-label">Heure de Début</label>
                            <input type="time" id="edit-heure_debut" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit-heure_fin" class="form-label">Heure de Fin</label>
                            <input type="time" id="edit-heure_fin" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit-matiere_id" class="form-label">Matière</label>
                            <select id="edit-matiere_id" class="form-control form-select" required>
                                @foreach (\App\Models\Matiere::all() as $matiere)
                                    <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let newRowIndex = {{ $index }};

        // Fonction pour afficher les messages
        function showMessage(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            setTimeout(() => alertDiv.remove(), 5000);
        }

        // Modification d'un créneau individuel via modal
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-row')) {
                const button = e.target.closest('.edit-row');
                const emploiId = button.getAttribute('data-emploi-id');
                const row = document.querySelector(`tr[data-emploi-id="${emploiId}"]`);

                // Récupération des valeurs
                const jour = row.querySelector('select[name*="[jour]"]').value;
                const heureDebut = row.querySelector('input[name*="[heure_debut]"]').value;
                const heureFin = row.querySelector('input[name*="[heure_fin]"]').value;
                const matiereId = row.querySelector('select[name*="[matiere_id]"]').value;

                // Remplissage du modal
                document.getElementById('emploi-id').value = emploiId;
                document.getElementById('edit-jour').value = jour;
                document.getElementById('edit-heure_debut').value = heureDebut;
                document.getElementById('edit-heure_fin').value = heureFin;
                document.getElementById('edit-matiere_id').value = matiereId;

                // Affichage du modal
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            }
        });

        // Soumission du formulaire modal
        document.getElementById('edit-form-modal').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('emploi_id', document.getElementById('emploi-id').value);
            formData.append('jour', document.getElementById('edit-jour').value);
            formData.append('heure_debut', document.getElementById('edit-heure_debut').value);
            formData.append('heure_fin', document.getElementById('edit-heure_fin').value);
            formData.append('matiere_id', document.getElementById('edit-matiere_id').value);

            fetch('{{ route("emploi_temps.updateCreneau") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    showMessage(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(data.message || 'Erreur lors de la modification', 'danger');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('Erreur serveur', 'danger');
            });
        });

        // Suppression d'un créneau
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-row')) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')) return;

                const button = e.target.closest('.delete-row');
                const emploiId = button.getAttribute('data-emploi-id');

                fetch(`{{ url('emploi_du_temps/delete-creneau') }}/${emploiId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        document.querySelector(`tr[data-emploi-id="${emploiId}"]`).remove();
                    } else {
                        showMessage(data.message || 'Erreur lors de la suppression', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showMessage('Erreur serveur', 'danger');
                });
            }
        });

        // Ajout d'un nouveau créneau
        document.getElementById('add-row').addEventListener('click', function() {
            const tbody = document.getElementById('emplois-tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="emplois[new][jour][]" class="form-control form-select" required>
                        <option value="Lundi">Lundi</option>
                        <option value="Mardi">Mardi</option>
                        <option value="Mercredi">Mercredi</option>
                        <option value="Jeudi">Jeudi</option>
                        <option value="Vendredi">Vendredi</option>
                        <option value="Samedi">Samedi</option>
                    </select>
                </td>
                <td><input type="time" name="emplois[new][heure_debut][]" class="form-control" required></td>
                <td><input type="time" name="emplois[new][heure_fin][]" class="form-control" required></td>
                <td>
                    <select name="emplois[new][matiere_id][]" class="form-control form-select" required>
                        @foreach (\App\Models\Matiere::all() as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-new-row">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
        });

        // Suppression d'une nouvelle ligne (non encore enregistrée)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-new-row')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
@endsection
