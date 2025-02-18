@extends('layaouts.template')

@section('content')
    <style>
        /* S'assure que les traits dans le tfoot couvrent toute la largeur */
        table tfoot th {
            border-top: 2px solid white;
            text-align: left;
            padding: 10px;
        }

        /* Améliore le style du tableau */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }

        /* Spécifique pour le tfoot */
        table tfoot tr th {
            text-align: center;
        }

        /* Ligne séparatrice visible pour le tfoot */
        table tfoot tr:first-child th {
            border-top: 3px solid #666;
        }

        table tfoot tr:last-child th {
            border-bottom: 2px solid #333;
        }

        /* Masquer les éléments inutiles lors de l'impression */
        @media print {
            .no-print {
                display: none;
            }

            .page-header {
                display: none;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .table-responsive {
                overflow: visible;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table th,
            table td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }

            .signature {
                margin-top: 50px;
                text-align: right;
            }
        }
    </style>

    <div class="content-wrapper">
        <div class="page-header">
            <div class="card" style="width: 1500px;">
                <div class="card-body">
                    <h2 class="card-title">Moyennes de {{ $inscription->eleve->nom }} {{ $inscription->eleve->prenom }} pour
                        l'année {{ $anneeAcademique->annee }}</h2>
                    {{-- <button class="btn btn-primary mr-2 no-print" onclick="window.print()"> <span class="glyphicon glyphicon-print "></span>Imprimer tous les bulletins</button> --}}
                    <div class="table-responsive">
                        @foreach ($trimestres as $trimestre)
                            <h4 class="mt-4">Trimestre : {{ $trimestre->nom }}</h4>
                            @if (!empty($moyennesParTrimestre[$trimestre->id]))
                                <button class="btn btn-secondary no-print" onclick="printBulletin('{{ $trimestre->id }}')" > <i class="bi bi-print "></i> Imprimer ce bulletin</button> <br>
                                <table class="table table-dark" style="width: 100%; border-collapse: collapse;" id="bulletin-{{ $trimestre->id }}"> <br>
                                    <thead>
                                        <tr>
                                            <th>Matière</th>
                                            <th>Coefficient</th>
                                            <th>Moyenne des Interrogations</th>
                                            <th>Moyenne Finale</th>
                                            <th>Rang</th>
                                            <th>Mention</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($moyennesParTrimestre[$trimestre->id] as $matiereNom => $moyenneFinale)
                                            @php
                                                $coefficient = $coefficients->firstWhere('matiere.nom', $matiereNom)
                                                    ?->valeur_coefficient;
                                                $moyenneInterrogations =
                                                    $moyennesInterrogations[$trimestre->id][$matiereNom] ?? null;
                                                $rangMatiere =
                                                    $rangsMatiere[$trimestre->id][$matiereNom] ?? [];
                                                $rang =
                                                    $rangMatiere[$inscription->eleve->id] ?? 'N/A';
                                                $mention = $mentions[$trimestre->id][$matiereNom] ?? 'N/A';
                                            @endphp
                                            <tr>
                                                <td>{{ $matiereNom }}</td>
                                                <td>{{ $coefficient !== null ? $coefficient : 'Non défini' }}</td>
                                                <td>{{ $moyenneInterrogations !== null ? number_format($moyenneInterrogations, 2) : 'Aucune note' }}
                                                </td>
                                                <td>{{ $moyenneFinale !== null ? number_format($moyenneFinale, 2) : 'Aucune note' }}
                                                </td>
                                                <td>{{ $rang }}</td>
                                                <td>{{ $mention }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total Coefficients</th>
                                            <th>{{ $totaux[$trimestre->id]['somme_coefficients'] }}</th>
                                            <th>Total Moyennes</th>
                                            <th>{{ number_format($totaux[$trimestre->id]['somme_moyennes'], 2) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Total Moyennes Coefficientées</th>
                                            <th>{{ number_format($totaux[$trimestre->id]['somme_moyennes_coefficientees'], 2) }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Moyenne Générale</th>
                                            <th>{{ $moyennesGenerales[$trimestre->id] !== null ? number_format($moyennesGenerales[$trimestre->id], 2) : 'Non calculée' }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="3">Rang Trimestriel</th>
                                            <th>{{ $rangsTrimestriels[$trimestre->id] ?? 'N/A' }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                <p>Aucune note pour ce trimestre.</p>
                            @endif
                        @endforeach
                    </div>

                    <!-- Espace pour signature -->
                    <div class="signature no-print">
                        <p>Signature du Directeur/Directrice :</p>
                        <p>________________________</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printBulletin(trimestreId) {
            var printContents = document.getElementById('bulletin-' + trimestreId).outerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(); // Recharger la page pour restaurer le contenu original
        }
    </script>
@endsection
