@extends('layaouts.template')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --money-gradient: linear-gradient(135deg, #fdc830 0%, #f37335 100%);
            --dark-bg: #0f0c29;
            --card-bg: #16213e;
            --card-hover: #1a1f3a;
            --text-primary: #e8e8e8;
            --text-secondary: #a8a8a8;
            --border-glow: rgba(102, 126, 234, 0.3);
        }

        .content-wrapper {
            background: linear-gradient(135deg, #0f0c29 0%, #1a1a2e 50%, #24243e 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .page-header h3 {
            color: var(--text-primary);
            font-weight: 800;
            font-size: 2.8rem;
            text-shadow: 0 0 30px rgba(102, 126, 234, 0.6);
            animation: fadeInDown 0.8s ease;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
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
                transform: scale(1.1);
            }
        }

        @keyframes glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(102, 126, 234, 0.4);
            }
            50% {
                box-shadow: 0 0 40px rgba(102, 126, 234, 0.8);
            }
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-glow);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.8s ease;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform 0.5s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.5);
            border-color: rgba(102, 126, 234, 0.8);
        }

        .card-body {
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .card-title {
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .card-title i {
            font-size: 2rem;
            margin-right: 0.8rem;
            animation: pulse 2s infinite;
        }

        .card-description {
            font-size: 2.8rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            animation: fadeInUp 1s ease;
        }

        /* Couleurs spécifiques pour les icônes */
        .text-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Styles pour les graphiques */
        .chart-container {
            position: relative;
            height: 300px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        canvas {
            filter: drop-shadow(0 4px 12px rgba(102, 126, 234, 0.3));
        }

        /* Styles pour les tableaux */
        .table {
            color: var(--text-primary);
            margin: 0;
        }

        .table thead {
            background: var(--primary-gradient);
            position: relative;
        }

        .table thead th {
            color: white;
            font-weight: 700;
            padding: 1.2rem;
            border: none;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .table tbody tr {
            background: var(--card-bg);
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: var(--card-hover);
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        }

        .table tbody td {
            padding: 1.2rem;
            color: var(--text-secondary);
            border: none;
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background: rgba(102, 126, 234, 0.03);
        }

        /* Animation progressive pour les cartes */
        .grid-margin:nth-child(1) .card { animation-delay: 0.1s; }
        .grid-margin:nth-child(2) .card { animation-delay: 0.2s; }
        .grid-margin:nth-child(3) .card { animation-delay: 0.3s; }
        .grid-margin:nth-child(4) .card { animation-delay: 0.4s; }
        .grid-margin:nth-child(5) .card { animation-delay: 0.5s; }
        .grid-margin:nth-child(6) .card { animation-delay: 0.6s; }

        /* Effet de brillance sur les cartes statistiques */
        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 70%
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% {
                left: -200%;
            }
            100% {
                left: 200%;
            }
        }

        /* Style pour les badges de montant */
        .money-badge {
            background: var(--money-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 900;
        }

        /* Responsive pour les petits écrans */
        @media (max-width: 768px) {
            .page-header h3 {
                font-size: 1.8rem;
            }

            .card-description {
                font-size: 2rem;
            }

            .card-title i {
                font-size: 1.5rem;
            }
        }

        /* Effet de lueur pour les tableaux */
        .table-wrapper {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(102, 126, 234, 0.2);
        }

        /* Style pour les lignes du tableau avec animation au survol */
        .table tbody tr {
            cursor: pointer;
        }

        .table tbody tr td:first-child {
            font-weight: 600;
            color: var(--text-primary);
        }
    </style>

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">📊 Dashboard Premium - {{ $currentDate }}</h3>
        </div>

        <!-- Statistiques générales avec icônes -->
        <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            <i class="bi bi-people-fill text-primary"></i>
                            Nombre d'Élèves
                        </h4>
                        <p class="card-description">{{ $nombreEleves }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            <i class="bi bi-person-badge-fill text-success"></i>
                            Nombre d'Enseignants
                        </h4>
                        <p class="card-description">{{ $nombreEnseignants }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            <i class="bi bi-building-fill text-warning"></i>
                            Nombre de Classes
                        </h4>
                        <p class="card-description">{{ $nombreClasses }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            <i class="bi bi-bar-chart-fill text-info"></i>
                            Moyenne Générale
                        </h4>
                        <p class="card-description">{{ number_format($moyenneNotes, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            <i class="bi bi-clock-history text-danger"></i>
                            Nombre d'Absences
                        </h4>
                        <p class="card-description">{{ $nombreAbsences }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h4 class="card-title">
                            <i class="bi bi-currency-exchange text-success"></i>
                            Total Paiements
                        </h4>
                        <p class="card-description money-badge">{{ number_format($totalPaiements) }} CFA</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="bi bi-graph-up text-danger"></i>
                            Absences par Trimestre
                        </h4>
                        <div class="chart-container">
                            <canvas id="absencesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="bi bi-cash-coin text-success"></i>
                            Paiements Mensuels
                        </h4>
                        <div class="chart-container">
                            <canvas id="paiementsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableaux récapitulatifs -->
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="bi bi-mortarboard-fill text-primary"></i>
                            Élèves Récents
                        </h4>
                        <div class="table-wrapper">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Classe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($elevesRecents as $eleve)
                                        <tr>
                                            <td>{{ $eleve->nom }}</td>
                                            <td>{{ $eleve->prenom }}</td>
                                            <td>{{ $eleve->inscription->classe->nom_classe ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="bi bi-person-workspace text-success"></i>
                            Enseignants
                        </h4>
                        <div class="table-wrapper">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Matière</th>
                                        <th>Classes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($enseignants as $enseignant)
                                        <tr>
                                            <td>{{ $enseignant->user->name }}</td>
                                            <td>{{ $enseignant->matiere->nom }}</td>
                                            <td>{{ $enseignant->classes->implode('nom_classe', ', ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="bi bi-collection-fill text-warning"></i>
                            Classes et Effectifs
                        </h4>
                        <div class="table-wrapper">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom de la Classe</th>
                                        <th>Effectif</th>
                                        <th>Cycle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classes as $classe)
                                        <tr>
                                            <td>{{ $classe->nom_classe }}</td>
                                            <td>{{ $classe->inscriptions_count }}</td>
                                            <td>{{ $classe->cycle }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuration pour le thème sombre
        Chart.defaults.color = '#a8a8a8';
        Chart.defaults.borderColor = 'rgba(102, 126, 234, 0.1)';

        // Vérifier si les données sont disponibles
        const absencesLabels = {!! json_encode($absencesParTrimestre->pluck('trimestre_id')) !!} || [];
        const absencesData = {!! json_encode($absencesParTrimestre->pluck('total')) !!} || [];

        const absencesCtx = document.getElementById('absencesChart').getContext('2d');
        new Chart(absencesCtx, {
            type: 'bar',
            data: {
                labels: absencesLabels.length ? absencesLabels.map(t => 'Trimestre ' + t) : ['Aucune donnée'],
                datasets: [{
                    label: 'Nombre d\'Absences',
                    data: absencesData.length ? absencesData : [0],
                    backgroundColor: 'rgba(250, 112, 154, 0.6)',
                    borderColor: 'rgba(250, 112, 154, 1)',
                    borderWidth: 2,
                    borderRadius: 10,
                    hoverBackgroundColor: 'rgba(254, 225, 64, 0.8)'
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
                            color: '#a8a8a8',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#a8a8a8',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
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
                            },
                            padding: 20
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });

        const paiementsLabels = {!! json_encode($paiementsMensuels->pluck('mois')) !!} || [];
        const paiementsData = {!! json_encode($paiementsMensuels->pluck('total')) !!} || [];

        const paiementsCtx = document.getElementById('paiementsChart').getContext('2d');
        new Chart(paiementsCtx, {
            type: 'line',
            data: {
                labels: paiementsLabels.length ? paiementsLabels : ['Aucune donnée'],
                datasets: [{
                    label: 'Paiements (CFA)',
                    data: paiementsData.length ? paiementsData : [0],
                    backgroundColor: 'rgba(17, 153, 142, 0.2)',
                    borderColor: 'rgba(56, 239, 125, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointBackgroundColor: 'rgba(56, 239, 125, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(56, 239, 125, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(56, 239, 125, 0.1)'
                        },
                        ticks: {
                            color: '#a8a8a8',
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            callback: function(value) {
                                return value.toLocaleString() + ' CFA';
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(56, 239, 125, 0.05)'
                        },
                        ticks: {
                            color: '#a8a8a8',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
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
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(22, 33, 62, 0.95)',
                        titleColor: '#e8e8e8',
                        bodyColor: '#a8a8a8',
                        borderColor: 'rgba(56, 239, 125, 0.5)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' CFA';
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
    </script>
@endsection
