@extends('layaouts.template')

@section('content')
    <style>
        .dashboard-card {
            background: linear-gradient(135deg, #050708 0%, #e9ecef 100%);
            border: none;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        #absencesTable {
            width: 100%;
            background: #fff;
        }
        .btn-primary {
            background: linear-gradient(90deg, #007bff, #0056b3);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3, #003d80);
        }
        .form-group label {
            font-weight: 600;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-sm {
            font-size: 0.875rem;
        }
    </style>

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title text-primary">Modifier l'absence de {{ $absence->eleve->nom }} {{ $absence->eleve->prenom }}</h3>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4 class="card-title">Modifier le formulaire</h4>

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <p class="card-description">Veuillez modifier les détails de l'absence</p>

                        <form class="forms-sample" method="POST"
                            action="{{ route('absence.update', $absence->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Sélection du trimestre -->
                            <div class="form-group">
                                <label for="trimestre_id">Trimestre</label>
                                <select name="trimestre_id" id="trimestre_id" class="form-control" required>
                                    <option value="">Sélectionner un trimestre</option>
                                    @foreach ($trimestres as $trimestre)
                                        <option value="{{ $trimestre->id }}"
                                            {{ $absence->trimestre_id == $trimestre->id ? 'selected' : '' }}>
                                            {{ $trimestre->nom }} - {{ $absence->anneeAcademique->annee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date de l'absence -->
                            <div class="form-group">
                                <label for="date_absence">Date de l'absence</label>
                                <input type="date" class="form-control" name="date_absence" id="date_absence"
                                    value="{{ $absence->date_absence }}" required>
                            </div>

                            <!-- Type (absence ou retard) -->
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="absence" {{ $absence->type === 'absence' ? 'selected' : '' }}>Absence</option>
                                    <option value="retard" {{ $absence->type === 'retard' ? 'selected' : '' }}>Retard</option>
                                </select>
                            </div>

                            <!-- Matière (visible uniquement pour absence, pré-rempli pour enseignants) -->
                            <div class="form-group" id="matiere_field"
                                style="{{ $absence->type === 'absence' ? 'display: block;' : 'display: none;' }}">
                                <label for="matiere_id">Matière</label>
                                @if ($user->role === 'enseignant' && $enseignant)
                                    <input type="text" class="form-control" value="{{ $enseignant->matiere->nom }}" readonly>
                                    <input type="hidden" name="matiere_id" value="{{ $enseignant->matiere_id }}">
                                @else
                                    <select name="matiere_id" id="matiere_id" class="form-control">
                                        <option value="">Sélectionner une matière</option>
                                        @foreach ($matieres as $matiere)
                                            <option value="{{ $matiere->id }}"
                                                {{ $absence->matiere_id == $matiere->id ? 'selected' : '' }}>
                                                {{ $matiere->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <!-- Justification -->
                            <div class="form-group">
                                <label for="justification">Justification (facultatif)</label>
                                <textarea class="form-control" name="justification" id="justification" rows="3">{{ $absence->justification }}</textarea>
                            </div>

                            <!-- Justifiée -->
                            <div class="form-group">
                                <label for="justifiee">Justifiée</label>
                                <select name="justifiee" id="justifiee" class="form-control" required>
                                    <option value="1" {{ $absence->justifiee ? 'selected' : '' }}>Oui</option>
                                    <option value="0" {{ !$absence->justifiee ? 'selected' : '' }}>Non</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Mettre à jour</button>
                            <a href="{{ route('absence.create', [$absence->eleve_id, $absence->annee_academique_id]) }}"
                                class="btn btn-dark">Annuler</a>
                        </form>

                        <!-- Liste des absences existantes -->
                        <h4 class="card-title mt-4">Autres Absences Enregistrées</h4>
                        @php
                            $absencesExistantes = \App\Models\Absence::where('eleve_id', $absence->eleve_id)
                                ->where('annee_academique_id', $absence->annee_academique_id)
                                ->where('id', '!=', $absence->id)
                                ->with(['matiere', 'user'])
                                ->get();
                        @endphp
                        @if ($absencesExistantes->isEmpty())
                            <p class="text-muted">Aucune autre absence enregistrée.</p>
                        @else
                            <div class="table-responsive">
                                <table id="absencesTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Matière</th>
                                            <th>Justification</th>
                                            <th>Justifiée</th>
                                            <th>Enregistré par</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($absencesExistantes as $otherAbsence)
                                            <tr>
                                                <td>{{ $otherAbsence->date_absence }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $otherAbsence->type === 'absence' ? 'danger' : 'warning' }}">
                                                        {{ ucfirst($otherAbsence->type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $otherAbsence->matiere ? $otherAbsence->matiere->nom : 'N/A' }}</td>
                                                <td>{{ $otherAbsence->justification ?? 'Aucune' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $otherAbsence->justifiee ? 'success' : 'danger' }}">
                                                        {{ $otherAbsence->justifiee ? 'Oui' : 'Non' }}
                                                    </span>
                                                </td>
                                                <td>{{ $otherAbsence->user->name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('absence.edit', $otherAbsence->id) }}"
                                                        class="btn btn-warning btn-sm me-1">Modifier</a>
                                                    <form method="POST" action="{{ route('absence.destroy', $otherAbsence->id) }}"
                                                        style="display: inline;" onsubmit="return confirm('Confirmer la suppression ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#absencesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                dom: '<"top"<"row"<"col-md-6"l><"col-md-6"f>><"row"<"col-md-12"B>>>rt<"bottom"ip>',
                buttons: [
                    {
                        extend: 'excel',
                        className: 'btn btn-inverse-info btn-sm',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-inverse-danger btn-sm',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF'
                    }
                ],
                columnDefs: [
                    { orderable: false, targets: -1 } // Désactiver tri sur Actions
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-control');
                }
            });

            // Afficher/masquer le champ matière selon le type
            $('#type').on('change', function() {
                if ($(this).val() === 'absence') {
                    $('#matiere_field').show();
                    @if ($user->role === 'admin')
                        $('#matiere_id').prop('required', true);
                    @endif
                } else {
                    $('#matiere_field').hide();
                    $('#matiere_id').val('').prop('required', false);
                }
            });

            // Initialiser l'état du champ matière
            if ($('#type').val() === 'absence') {
                $('#matiere_field').show();
                @if ($user->role === 'admin')
                    $('#matiere_id').prop('required', true);
                @endif
            } else {
                $('#matiere_field').hide();
                $('#matiere_id').prop('required', false);
            }
        });
    </script>
@endsection
