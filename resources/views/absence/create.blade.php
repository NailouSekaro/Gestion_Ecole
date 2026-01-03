@extends('layaouts.template')

@section('content')
    <style>
        .dashboard-card {
            background: linear-gradient(135deg, #01060bce 0%, #011427 100%);
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
            background: #0b0808;
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
    </style>

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title text-primary">Enregistrement des absences de {{ $eleve->nom }} {{ $eleve->prenom }}</h3>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4 class="card-title">Remplir le formulaire</h4>

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

                        <p class="card-description">Veuillez saisir les détails de l'absence</p>

                        <form class="forms-sample" method="POST"
                            action="{{ route('absence.store', ['eleve_id' => $eleve->id, 'annee_academique_id' => $annee_academique->id]) }}">
                            @csrf

                            <!-- Sélection du trimestre -->
                            <div class="form-group">
                                <label for="trimestre_id">Trimestre</label>
                                <select name="trimestre_id" id="trimestre_id" class="form-control" required>
                                    <option value="">Sélectionner un trimestre</option>
                                    @foreach ($trimestres as $trimestre)
                                        <option value="{{ $trimestre->id }}">{{ $trimestre->nom }} - {{ $annee_academique->annee }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date de l'absence -->
                            <div class="form-group">
                                <label for="date_absence">Date de l'absence</label>
                                <input type="date" class="form-control" name="date_absence" id="date_absence"
                                    value="{{ now()->format('Y-m-d') }}" required>
                            </div>

                            <!-- Type (absence ou retard) -->
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value=""></option>
                                    <option value="absence">Absence</option>
                                    <option value="retard">Retard</option>
                                </select>
                            </div>

                            <!-- Matière (visible uniquement pour absence, pré-rempli pour enseignants) -->
                            @if (auth()->user()->role === 'enseignant' && $enseignant)
                                <div class="form-group" id="matiere_field" style="display: none;">
                                    <label for="matiere_id">Matière</label>
                                    <input type="text" class="form-control" value="{{ $enseignant->matiere->nom }}" readonly>
                                    <input type="hidden" name="matiere_id" value="{{ $enseignant->matiere_id }}">
                                </div>
                            @elseif (auth()->user()->role === 'admin')
                                <div class="form-group" id="matiere_field" style="display: none;">
                                    <label for="matiere_id">Matière</label>
                                    <select name="matiere_id" id="matiere_id" class="form-control">
                                        <option value="">Sélectionner une matière</option>
                                        @foreach (\App\Models\Matiere::all() as $matiere)
                                            <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Justification -->
                            <div class="form-group">
                                <label for="justification">Justification (facultatif)</label>
                                <textarea class="form-control" name="justification" id="justification" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Enregistrer</button>
                            <button class="btn btn-dark" type="reset">Annuler</button>
                        </form>

                        <!-- Liste des absences existantes -->
                        <h4 class="card-title mt-4">Absences enregistrées</h4>
                        @if ($absencesExistantes->isEmpty())
                            <p class="text-muted">Aucune absence enregistrée.</p>
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
                                        @foreach ($absencesExistantes as $absence)
                                            <tr>
                                                <td>{{ $absence->date_absence }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $absence->type === 'absence' ? 'danger' : 'warning' }}">
                                                        {{ ucfirst($absence->type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $absence->matiere ? $absence->matiere->nom : 'N/A' }}</td>
                                                <td>{{ $absence->justification ?? 'Aucune' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $absence->justifiee ? 'success' : 'danger' }}">
                                                        {{ $absence->justifiee ? 'Oui' : 'Non' }}
                                                    </span>
                                                </td>
                                                <td>{{ $absence->user->name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('absence.edit', $absence->id) }}" class="btn btn-warning btn-sm me-1">Modifier</a>
                                                    <form method="POST" action="{{ route('absence.destroy', $absence->id) }}" style="display: inline;" onsubmit="return confirm('Confirmer la suppression ?')">
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
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-control');
                }
            });

            // Afficher/masquer le champ matière selon le type
            $('#type').on('change', function() {
                if ($(this).val() === 'absence') {
                    $('#matiere_field').show();
                    @if (auth()->user()->role === 'admin')
                        $('#matiere_id').prop('required', true);
                    @endif
                } else {
                    $('#matiere_field').hide();
                    $('#matiere_id').prop('required', false);
                }
            });

            // Initialiser l'état du champ matière
            if ($('#type').val() === 'absence') {
                $('#matiere_field').show();
                @if (auth()->user()->role === 'admin')
                    $('#matiere_id').prop('required', true);
                @endif
            } else {
                $('#matiere_field').hide();
                $('#matiere_id').prop('required', false);
            }
        });
    </script>
@endsection
