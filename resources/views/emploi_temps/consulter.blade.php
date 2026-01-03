<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps - {{ $classe->nom_classe }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .emploi-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 20px auto;
            max-width: 1200px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .header-section h1 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header-section .info {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .jour-section {
            margin-bottom: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .jour-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 15px;
            display: inline-block;
        }
        .creneau-card {
            background: white;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .creneau-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .creneau-time {
            font-weight: bold;
            color: #667eea;
            font-size: 1rem;
        }
        .creneau-matiere {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 8px 0;
        }
        .creneau-enseignant {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .no-cours {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
        .btn-retour {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-retour:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-imprimer {
            background: #28a745;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        @media print {
            body {
                background: white;
            }
            .btn-retour, .btn-imprimer {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="emploi-container">
            <!-- En-tête -->
            <div class="header-section">
                <h1><i class="bi bi-calendar-week me-2"></i>Emploi du Temps</h1>
                <div class="info">
                    <strong>Classe :</strong> {{ $classe->nom_classe }} |
                    <strong>Année Académique :</strong> {{ $anneeAcademique->annee }}
                </div>
            </div>

            @if($emplois->isEmpty())
                <div class="no-cours">
                    <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                    <h4 class="mt-3">Aucun emploi du temps disponible</h4>
                    <p>Veuillez contacter l'administration pour plus d'informations.</p>
                </div>
            @else
                <!-- Emploi du temps par jour -->
                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $jour)
                    @if(isset($emplois[$jour]) && $emplois[$jour]->isNotEmpty())
                        <div class="jour-section">
                            <div class="jour-title">
                                <i class="bi bi-calendar-day me-2"></i>{{ $jour }}
                            </div>

                            @foreach($emplois[$jour] as $emploi)
                                <div class="creneau-card">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="creneau-time">
                                                <i class="bi bi-clock me-2"></i>
                                                {{ \Carbon\Carbon::parse($emploi->heure_debut)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($emploi->heure_fin)->format('H:i') }}
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="creneau-matiere">
                                                <i class="bi bi-book me-2"></i>{{ $emploi->matiere->nom ?? 'Matière non définie' }}
                                            </div>
                                            @if($emploi->enseignant)
                                                <div class="creneau-enseignant">
                                                    <i class="bi bi-person me-2"></i>{{ $emploi->enseignant->nom }} {{ $emploi->enseignant->prenom }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @endif

            <!-- Boutons d'action -->
            <div class="text-center mt-4">
                <button onclick="window.print()" class="btn btn-imprimer me-2">
                    <i class="bi bi-printer me-2"></i>Imprimer
                </button>
                <a href="{{ url('/login') }}" class="btn btn-retour">
                    <i class="bi bi-arrow-left me-2"></i>Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
