@extends('layaouts.template')

@section('content')
    <style>
        .dashboard-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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
        .filter-container {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-sm {
            font-size: 0.875rem;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
    </style>

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title text-primary">Liste des Absences</h3>
        </div>

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

        <div class="row">
            <!-- Statistiques -->
            <div class="col-12 grid-margin stretch-card">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4 class="card-title">Statistiques des Absences pour {{ $anneeActiveLabel }}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="absencesParEleveChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="absencesParClasseChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des absences -->
            <div class="col-12 grid-margin stretch-card">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h4 class="card-title">Absences pour {{ $anneeActiveLabel }}</h4>

                        <div class="filter-container">
                            <form id="filterForm" method="GET" action="{{ route('absence.index') }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select name="annee_academique_id" id="annee_academique_id" class="form-control">
                                            <option value="">-- Toutes les années --</option>
                                            @foreach ($anneesAcademiques as $annee)
                                                <option value="{{ $annee->id }}"
                                                    {{ $anneeSelectedId == $annee->id ? 'selected' : '' }}>
                                                    {{ $annee->annee }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">Filtrer</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if ($absences->isEmpty())
                            <p class="text-muted">Aucune absence enregistrée.</p>
                        @else
                            <div class="table-responsive">
                                <table id="absencesTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Élève</th>
                                            <th>Classe</th>
                                            <th>Matière</th>
                                            <th>Trimestre</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Justification</th>
                                            <th>Justifiée</th>
                                            <th>Enregistré par</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($absences as $absence)
                                            <tr>
                                                <td>{{ $absence->eleve->nom }} {{ $absence->eleve->prenom }}</td>
                                                <td>{{ $absence->classe->nom_classe }}</td>
                                                <td>{{ $absence->matiere ? $absence->matiere->nom : 'N/A' }}</td>
                                                <td>{{ $absence->trimestre->nom }}</td>
                                                <td>{{ $absence->date_absence }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $absence->type === 'absence' ? 'danger' : 'warning' }}">
                                                        {{ ucfirst($absence->type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $absence->justification ?? 'Aucune' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $absence->justifiee ? 'success' : 'danger' }}">
                                                        {{ $absence->justifiee ? 'Oui' : 'Non' }}
                                                    </span>
                                                </td>
                                                <td>{{ $absence->user->name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('absence.edit', $absence->id) }}"
                                                        class="btn btn-warning btn-sm me-1">Modifier</a>
                                                    <form method="POST" action="{{ route('absence.destroy', $absence->id) }}"
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
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#absencesTable').DataTable({
                responsive: true,
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
                    // Filtres pour Trimestre, Type, Élève, Classe
                    this.api().columns([0, 1, 3, 5]).every(function() {
                        var column = this;
                        var select = $('<select class="form-control"><option value="">Tous</option></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                    });

                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-control');
                }
            });

            $('#annee_academique_id').on('change', function() {
                $('#filterForm').submit();
            });

            // Chart.js pour absences par élève
            const ctxEleve = document.getElementById('absencesParEleveChart')?.getContext('2d');
            if (ctxEleve) {
                new Chart(ctxEleve, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($stats['parEleve']->pluck('eleve')->toArray()) !!},
                        datasets: [
                            {
                                label: 'Absences Justifiées',
                                data: {!! json_encode($stats['parEleve']->pluck('justifiees')->toArray()) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0.4)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Absences Non Justifiées',
                                data: {!! json_encode($stats['parEleve']->pluck('non_justifiees')->toArray()) !!},
                                backgroundColor: 'rgba(220, 53, 69, 0.4)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true, stacked: true },
                            x: { stacked: true }
                        },
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });
            }

            // Chart.js pour absences par classe
            const ctxClasse = document.getElementById('absencesParClasseChart')?.getContext('2d');
            if (ctxClasse) {
                new Chart(ctxClasse, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($stats['parClasse']->pluck('classe')->toArray()) !!},
                        datasets: [
                            {
                                label: 'Absences Justifiées',
                                data: {!! json_encode($stats['parClasse']->pluck('justifiees')->toArray()) !!},
                                backgroundColor: 'rgba(40, 167, 69, 0.4)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Absences Non Justifiées',
                                data: {!! json_encode($stats['parClasse']->pluck('non_justifiees')->toArray()) !!},
                                backgroundColor: 'rgba(220, 53, 69, 0.4)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true, stacked: true },
                            x: { stacked: true }
                        },
                        plugins: {
                            legend: { display: true }
                        }
                    }
                });
            }
        });
    </script>
@endsection
