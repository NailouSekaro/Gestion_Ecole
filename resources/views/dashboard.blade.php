@extends('layaouts.template')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --dark-bg: #1a1a2e;
            --card-bg: #16213e;
            --card-hover: #0f1729;
            --text-primary: #e8e8e8;
            --text-secondary: #a8a8a8;
        }

        .content-wrapper {
            background: linear-gradient(135deg, #0f0c29 0%, #1a1a2e 50%, #24243e 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .page-header h3 {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
            animation: fadeInDown 0.8s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .dashboard-card {
            background: var(--card-bg);
            border: 1px solid rgba(102, 126, 234, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.8s ease;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .dashboard-card:hover::before {
            transform: scaleX(1);
        }

        .dashboard-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 45px rgba(102, 126, 234, 0.4);
            border-color: rgba(102, 126, 234, 0.6);
        }

        .card-body {
            position: relative;
            z-index: 1;
        }

        .stat-icon {
            font-size: 3rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s infinite;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 1rem 0;
            animation: fadeInUp 1s ease;
        }

        .card-title {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin-bottom: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .table-container {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        #emploisTable {
            width: 100%;
            color: var(--text-primary);
        }

        #emploisTable thead {
            background: var(--primary-gradient);
        }

        #emploisTable thead th {
            color: white;
            font-weight: 600;
            padding: 1rem;
            border: none;
        }

        #emploisTable tbody tr {
            background: var(--card-bg);
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        #emploisTable tbody tr:hover {
            background: var(--card-hover);
            transform: scale(1.01);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
        }

        #emploisTable tbody td {
            padding: 1rem;
            color: var(--text-secondary);
            border: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-warning {
            background: var(--warning-gradient);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.5);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(118, 75, 162, 0.6);
        }

        .alert-success {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.3);
            color: var(--text-primary);
            border-radius: 15px;
            animation: fadeInDown 0.5s ease;
        }

        .text-muted {
            color: var(--text-secondary) !important;
        }

        /* Animation pour les cartes avec délai */
        .grid-margin:nth-child(1) .dashboard-card { animation-delay: 0.1s; }
        .grid-margin:nth-child(2) .dashboard-card { animation-delay: 0.2s; }
        .grid-margin:nth-child(3) .dashboard-card { animation-delay: 0.3s; }
        .grid-margin:nth-child(4) .dashboard-card { animation-delay: 0.4s; }
        .grid-margin:nth-child(5) .dashboard-card { animation-delay: 0.5s; }

        /* DataTables personnalisé */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background: var(--card-bg);
            color: var(--text-primary);
            border: 1px solid rgba(102, 126, 234, 0.3);
            border-radius: 10px;
            padding: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: var(--card-bg);
            color: var(--text-primary) !important;
            border-radius: 8px;
            margin: 0 3px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-gradient) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary);
        }

        /* Style pour les icônes des stats */
        .stat-icon-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-icon-success { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-icon-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-icon-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        /* Effet glassmorphism pour les conteneurs de graphiques */
        .chart-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Tableau de Bord ✨ </h3>
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
            @if (auth()->user()->role === 'Admin')
                <!-- Contenu pour admins -->
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="bi bi-building-fill stat-icon stat-icon-primary"></i>
                            <h4 class="card-title mt-3">Classes</h4>
                            <p class="stat-number">{{ $total_classes }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="bi bi-person-badge-fill stat-icon stat-icon-success"></i>
                            <h4 class="card-title mt-3">Enseignants</h4>
                            <p class="stat-number">{{ $total_enseignants }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-fill stat-icon stat-icon-info"></i>
                            <h4 class="card-title mt-3">Emplois du Temps</h4>
                            <p class="stat-number">{{ $total_emplois }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 grid-margin">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">📋 Derniers Emplois Créés</h4>
                            @if ($recent_emplois->isEmpty())
                                <p class="text-muted">Aucun emploi du temps récent.</p>
                            @else
                                <div class="table-container">
                                    <table id="emploisTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Classe</th>
                                                <th>Année Académique</th>
                                                <th>Date de Création</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recent_emplois as $emploi)
                                                <tr>
                                                    <td>{{ $emploi->classe->nom_classe }}</td>
                                                    <td>{{ $emploi->annee_Academique->annee }}</td>
                                                    <td>{{ $emploi->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('emploi_temps.edit', ['classeId' => $emploi->classe_id, 'anneeAcademiqueId' => $emploi->annee_academique_id]) }}" class="btn btn-warning btn-sm">Modifier</a>
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

                <div class="col-12 grid-margin">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">⚡ Actions Rapides</h4>
                            <a href="{{ route('emploi_temps.create') }}" class="btn btn-primary me-2">Créer un Emploi</a>
                            <a href="{{ route('enseignant.index') }}" class="btn btn-secondary">Gérer les Utilisateurs</a>
                        </div>
                    </div>
                </div>

            @elseif (auth()->user()->role === 'enseignant')
                <!-- Contenu pour les enseignants -->
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="bi bi-book-fill stat-icon stat-icon-primary"></i>
                            <h4 class="card-title mt-3">Cours Assignés</h4>
                            <p class="stat-number">{{ $total_cours }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <i class="bi bi-person-x-fill stat-icon stat-icon-danger"></i>
                            <h4 class="card-title mt-3">Absences</h4>
                            <p class="stat-number">{{ $total_absences }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 grid-margin">
                    <div class="card dashboard-card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">📚 Vos Cours pour {{ $annee_active }}</h4>
                            @if ($emplois->isEmpty())
                                <p class="text-muted">Aucun cours assigné pour l'année académique {{ $annee_active }}.</p>
                            @else
                                <div class="chart-container">
                                    <canvas id="coursParJourChart"></canvas>
                                </div>
                                <div class="table-container mt-4">
                                    <table id="emploisTable" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Jour</th>
                                                <th>Classe</th>
                                                <th>Matière</th>
                                                <th>Horaire</th>
                                                <th>Année Académique</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($emplois as $jour => $jourEmplois)
                                                @foreach ($jourEmplois as $emploi)
                                                    <tr>
                                                        <td>{{ $jour }}</td>
                                                        <td>{{ $emploi->classe->nom_classe }}</td>
                                                        <td>{{ $emploi->matiere->nom }}</td>
                                                        <td>{{ $emploi->heure_debut }} - {{ $emploi->heure_fin }}</td>
                                                        <td>{{ $emploi->annee_Academique->annee }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 grid-margin">
                    <div class="card dashboard-card chart-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">📊 Graphique des Absences</h4>
                            @if ($absencesParJour->isEmpty())
                                <p class="text-muted">Aucune absence enregistrée.</p>
                            @else
                                <div class="chart-container">
                                    <canvas id="absencesParJourChart"></canvas>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 grid-margin">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">⚡ Actions Rapides</h4>
                            <a href="{{ route('emploi_temps.index') }}" class="btn btn-primary">Voir tous les Emplois</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts pour DataTables et Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#emploisTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        });

        @if (auth()->user()->role === 'enseignant')
            // Chart.js pour les cours par jour avec style moderne
            const ctxCours = document.getElementById('coursParJourChart')?.getContext('2d');
            if (ctxCours) {
                new Chart(ctxCours, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($emplois->keys()->toArray()) !!},
                        datasets: [{
                            label: 'Nombre de Cours par Jour',
                            data: {!! json_encode($emplois->map->count()->toArray()) !!},
                            backgroundColor: 'rgba(102, 126, 234, 0.6)',
                            borderColor: 'rgba(102, 126, 234, 1)',
                            borderWidth: 2,
                            borderRadius: 10,
                            hoverBackgroundColor: 'rgba(118, 75, 162, 0.8)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(102, 126, 234, 0.1)'
                                },
                                ticks: {
                                    color: '#a8a8a8'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#a8a8a8'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: '#e8e8e8',
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }

            // Chart.js pour les absences par jour avec style moderne
            const ctxAbsences = document.getElementById('absencesParJourChart')?.getContext('2d');
            if (ctxAbsences) {
                new Chart(ctxAbsences, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($absencesParJour->pluck('date_absence')->toArray()) !!},
                        datasets: [{
                            label: 'Absences par Jour',
                            data: {!! json_encode($absencesParJour->pluck('total')->toArray()) !!},
                            backgroundColor: 'rgba(250, 112, 154, 0.2)',
                            borderColor: 'rgba(250, 112, 154, 1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: 'rgba(250, 112, 154, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(250, 112, 154, 0.1)'
                                },
                                ticks: {
                                    color: '#a8a8a8'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(250, 112, 154, 0.05)'
                                },
                                ticks: {
                                    color: '#a8a8a8'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    color: '#e8e8e8',
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeInOutCubic'
                        }
                    }
                });
            }
        @endif
    </script>
@endsection
