@extends('layaouts.template')

@section('content')

    {{-- CSS spécifique au bulletin dans un fichier séparé ou avec un scope --}}
    <style>
        /* CSS scopé pour éviter les conflits avec le template */
        .bulletin-container {
            font-family: "Times New Roman", serif;
            color: #2d3748;
            background: #fff;
        }

        .bulletin-container .content-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: none;
        }

        .bulletin-page {
            page-break-after: always;
            margin-bottom: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .bulletin-page:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }

        .bulletin-container .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }

        .bulletin-container .header-left img {
            max-width: 80px;
            height: auto;
        }

        .bulletin-container .header-right {
            text-align: right;
            font-size: 12px;
            line-height: 1.5;
            color: #374151;
        }

        .bulletin-container .title {
            text-align: center;
            margin: 15px 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e40af;
            letter-spacing: 1px;
        }

        .bulletin-container .student-info {
            margin-bottom: 15px;
            font-size: 12px;
            line-height: 1.5;
            border: 2px solid #e5e7eb;
            padding: 10px;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .bulletin-container table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
            border: 2px solid #374151;
        }

        .bulletin-container th,
        .bulletin-container td {
            border: 1px solid #6b7280;
            padding: 8px 6px;
            text-align: center;
            font-size: 11px;
            vertical-align: middle;
        }

        .bulletin-container thead th {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bulletin-container tbody td {
            background: #ffffff;
        }

        .bulletin-container tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .bulletin-container tbody tr:hover td {
            background: #e0f2fe;
        }

        .bulletin-container tfoot th {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        .bulletin-container .bilan-classe {
            margin: 15px 0;
            padding: 10px;
            border: 2px solid #10b981;
            border-radius: 8px;
            font-size: 12px;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            font-weight: 600;
            text-align: center;
            color: #065f46;
        }

        .bulletin-container .appreciation {
            margin: 15px 0;
            padding: 12px;
            border: 2px solid #8b5cf6;
            border-left: 6px solid #8b5cf6;
            border-radius: 8px;
            font-size: 12px;
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            font-style: italic;
            color: #581c87;
            line-height: 1.5;
        }

        .bulletin-container .recompenses {
            margin: 15px 0;
            font-size: 11px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .bulletin-container .recompenses label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            color: #374151;
            cursor: default;
        }

        .bulletin-container .recompenses input[type="checkbox"] {
            transform: scale(1.2);
        }

        .bulletin-container .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #374151;
        }

        .bulletin-container .signature {
            text-align: center;
            width: 45%;
            padding-top: 20px;
        }

        .bulletin-container .signature p:first-child {
            font-weight: bold;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .bulletin-container .signature p:last-child {
            border-top: 2px solid #374151;
            padding-top: 5px;
            margin-top: 15px;
            font-size: 10px;
        }

        /* Styles d'impression améliorés */
        @media print {
            .no-print {
                display: none !important;
            }

            .bulletin-container {
                font-size: 10px;
                color: #000;
            }

            .bulletin-container .content-wrapper {
                padding: 0;
                max-width: 100%;
                box-shadow: none;
            }

            .bulletin-page {
                box-shadow: none;
                border-radius: 0;
                padding: 15px;
                margin-bottom: 0;
                page-break-after: always;
            }

            .bulletin-page:last-child {
                page-break-after: auto;
            }

            .bulletin-container .header {
                border-bottom: 2px solid #000;
                margin-bottom: 15px;
            }

            .bulletin-container .title {
                color: #000;
                font-size: 18px;
            }

            .bulletin-container .student-info {
                background: none !important;
                border: 1px solid #000;
            }

            .bulletin-container table {
                border: 2px solid #000 !important;
            }

            .bulletin-container th,
            .bulletin-container td {
                border: 1px solid #000 !important;
                background: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .bulletin-container thead th {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .bulletin-container tfoot th {
                background: #666 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .bulletin-container .bilan-classe,
            .bulletin-container .appreciation {
                background: none !important;
                border: 1px solid #000 !important;
            }

            .bulletin-container .recompenses {
                background: none !important;
                border: 1px solid #000 !important;
            }
        }
    </style>

    <div class="bulletin-container">
        <div class="content-wrapper">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    {{-- Boutons d'action globaux --}}
                    <div class="no-print mb-4 p-3 bg-light rounded">
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary" onclick="printAllBulletins()">
                                <i class="fas fa-print"></i> Imprimer Tous
                            </button>
                            <button class="btn btn-info" onclick="window.print()">
                                <i class="fas fa-print"></i> Aperçu d'impression
                            </button>
                            <a href="{{ route('bulletin.exportPdf', [$inscription->eleve_id, $anneeAcademique->id]) }}"
                                class="btn btn-success">
                                <i class="fas fa-file-pdf"></i> Exporter PDF
                            </a>
                        </div>
                    </div>

                    @forelse ($trimestres as $trimestre)
                        <div class="bulletin-page" id="bulletin-page-{{ $trimestre->id }}">
                            <div class="header">
                                <div class="header-left">
                                    <img src="{{ asset('assets/images/OIG1.jpg') }}" alt="Logo École">
                                </div>
                                <div class="header-center">
                                    <p><strong>{{ $etablissement->republique ?? 'République du Bénin' }}</strong></p>
                                    <p><strong>{{ $etablissement->nom ?? 'Nom de l\'École' }}</strong></p>
                                    <p>Contact: {{ $etablissement->contact ?? '+229 00 00 00 00' }}</p>
                                    <p>Devise: {{ $etablissement->devise ?? 'Amour - Discipline - Travail' }}</p>
                                </div>

                                <div class="header-right">
                                    <img src="{{ $inscription->eleve->photo
                                        ? asset('storage/' . $inscription->eleve->photo)
                                        : asset('images/default-avatar.jpg') }}"
                                        alt="Photo de l'élève" width="80" height="80">
                                </div>
                            </div>

                            <div class="title">
                                Bulletin Scolaire - {{ $anneeAcademique->annee ?? 'Année Non Définie' }}
                            </div>

                            <div class="student-info">
                                <p><strong>Élève :</strong> {{ $inscription->eleve->nom ?? 'N/A' }}
                                    {{ $inscription->eleve->prenom ?? 'N/A' }} |
                                    <strong>Date Naissance :</strong>
                                    {{ $inscription->eleve->date_naissance ? \Carbon\Carbon::parse($inscription->eleve->date_naissance)->format('d/m/Y') : 'N/A' }}
                                    |
                                    <strong>Lieu :</strong> {{ $inscription->eleve->lieu_de_naissance ?? 'N/A' }}
                                </p>
                                <p><strong>Matricule :</strong> {{ $inscription->eleve->matricule_educ_master ?? 'N/A' }} |
                                    <strong>Classe :</strong> {{ $inscription->classe->nom_classe ?? 'N/A' }} |
                                    <strong>Effectif :</strong> {{ $inscription->classe->inscriptions->count() ?? 0 }}
                                </p>
                                <p><strong>Statut :</strong> {{ $inscription->statut ?? 'N/A' }} |<strong>Trimestre
                                        :</strong> {{ $trimestre->nom ?? 'N/A' }} |
                                    <strong>Période :</strong>
                                    {{ $trimestre->date_debut ? \Carbon\Carbon::parse($trimestre->date_debut)->format('d/m/Y') : 'N/A' }}
                                    -
                                    {{ $trimestre->date_fin ? \Carbon\Carbon::parse($trimestre->date_fin)->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>

                            <div class="no-print mb-3">
                                <button class="btn btn-outline-primary btn-sm"
                                    onclick="printSingleBulletin('{{ $trimestre->id }}')">
                                    <i class="fas fa-print"></i> Imprimer ce trimestre
                                </button>
                            </div>

                            {{-- Tableau principal avec design amélioré --}}
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 18%;">Matière</th>
                                        <th style="width: 10%;">Moy. Interro</th>
                                        <th style="width: 12%;">Devoir 1</th>
                                        <th style="width: 12%;">Devoir 2</th>
                                        <th style="width: 10%;">Moy. Devoirs</th>
                                        <th style="width: 10%;">Moy. Matière</th>
                                        <th style="width: 10%;">Moy. Coeff.</th>
                                        <th style="width: 8%;">Coeff.</th>
                                        <th style="width: 8%;">Rang</th>
                                        <th style="width: 12%;">Mention</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($coefficients as $coeff)
                                        @php
                                            $matiereNom = $coeff->matiere->nom ?? 'Matière inconnue';
                                            $notes = $notesDetaillees[$trimestre->id][$matiereNom] ?? [];

                                            // Devoirs
                                            $devoirsArray = array_filter([
                                                $notes['Devoir 1'] ?? null,
                                                $notes['Devoir 2'] ?? null,
                                            ]);
                                            $devoirs = !empty($devoirsArray) ? implode(' - ', $devoirsArray) : '--';
                                        @endphp
                                        <tr>
                                            <td style="text-align: left; font-weight: 500;">{{ $matiereNom }}</td>

                                            <td style="font-weight: 600;">
                                                {{ $moyennesInterrogations[$trimestre->id][$matiereNom] ?? '--' }}
                                            </td>

                                            <td>
                                                @if (isset($notes['Devoir 1']))
                                                    {{ $notes['Devoir 1'] }}
                                                @else
                                                    --
                                                @endif
                                            </td>

                                            <td>
                                                @if (isset($notes['Devoir 2']))
                                                    {{ $notes['Devoir 2'] }}
                                                @else
                                                    --
                                                @endif
                                            </td>

                                            <td style="font-weight: 600;">
                                                {{ $moyennesDevoirs[$trimestre->id][$matiereNom] ?? '--' }}
                                            </td>

                                            <td style="font-weight: bold; color: #1e40af;">
                                                {{ $moyennesParTrimestre[$trimestre->id][$matiereNom] ?? '--' }}
                                            </td>

                                            {{-- Nouvelle colonne : Moyenne coefficiée --}}
                                            <td style="font-weight: bold; color: #047857;">
                                                @php
                                                    // Convertit en float si la valeur existe
                                                    $moyMatiere = isset(
                                                        $moyennesParTrimestre[$trimestre->id][$matiereNom],
                                                    )
                                                        ? floatval($moyennesParTrimestre[$trimestre->id][$matiereNom])
                                                        : null;

                                                    $coeffMatiere = floatval($coeff->valeur_coefficient ?? 1);

                                                    $moyCoeff =
                                                        $moyMatiere !== null
                                                            ? number_format($moyMatiere * $coeffMatiere, 2)
                                                            : '--';
                                                @endphp

                                                {{ $moyCoeff }}
                                            </td>

                                            <td style="font-weight: 600;">
                                                {{ $coeff->valeur_coefficient ?? 1 }}
                                            </td>

                                            <td style="font-weight: 600;">
                                                {{ $rangsMatiere[$trimestre->id][$matiereNom] ?? '--' }}
                                            </td>

                                            <td style="font-weight: 500; color: #059669;">
                                                {{ $mentions[$trimestre->id][$matiereNom] ?? '--' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10"
                                                style="text-align: center; font-style: italic; color: #6b7280;">
                                                Aucune matière trouvée pour cette classe
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="6">TOTAUX</th>
                                        <th>{{ $totaux[$trimestre->id]['somme_moyennes_coefficientees'] ?? 0 }}</th>
                                        <th>{{ $totaux[$trimestre->id]['somme_coefficients'] ?? 0 }}</th>
                                        <th colspan="2"></th>
                                    </tr>

                                    <tr>
                                        <th colspan="9">MOYENNE GÉNÉRALE</th>
                                        <th style="font-size: 12px; font-weight: bold;">
                                            {{ $moyennesGenerales[$trimestre->id] ?? '--' }}/20
                                        </th>
                                    </tr>

                                    <tr>
                                        <th colspan="9">RANG TRIMESTRIEL</th>
                                        <th style="font-size: 12px; font-weight: bold;">
                                            {{ $rangsTrimestriels[$trimestre->id] ?? '--' }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="bilan-classe">
                                <p><strong>BILAN DE LA CLASSE</strong></p>
                                <p>
                                    Moyenne la plus faible :
                                    <strong>{{ $moyenneFaible[$trimestre->id] ?? '--' }}/20</strong> |
                                    Moyenne la plus forte :
                                    <strong>{{ $moyenneForte[$trimestre->id] ?? '--' }}/20</strong> |
                                    Moyenne de la classe :
                                    <strong>{{ $moyenneClasse[$trimestre->id] ?? '--' }}/20</strong>
                                </p>
                            </div>

                            <div class="recompenses">
                                <label><input type="checkbox" disabled
                                        {{ $recompenses[$trimestre->id]['felicitations'] ?? false ? 'checked' : '' }}>
                                    <strong>Félicitations</strong>
                                </label>

                                <label><input type="checkbox" disabled
                                        {{ $recompenses[$trimestre->id]['encouragements'] ?? false ? 'checked' : '' }}>
                                    <strong>Encouragements</strong>
                                </label>

                                <label><input type="checkbox" disabled
                                        {{ $recompenses[$trimestre->id]['tableau_honneur'] ?? false ? 'checked' : '' }}>
                                    <strong>Tableau d'Honneur</strong>
                                </label>

                                <label><input type="checkbox" disabled
                                        {{ $recompenses[$trimestre->id]['avertissement'] ?? false ? 'checked' : '' }}>
                                    <strong>Avertissement</strong>
                                </label>

                                <label><input type="checkbox" disabled
                                        {{ $recompenses[$trimestre->id]['blame'] ?? false ? 'checked' : '' }}>
                                    <strong>Blâme</strong>
                                </label>
                            </div>

                            <div class="appreciation">
                                <p><strong>APPRÉCIATION DU CONSEIL DE CLASSE :</strong></p>
                                <p>{{ $appreciationPrincipal[$trimestre->id] ?? 'Travail satisfaisant, continuez sur cette voie.' }}
                                </p>
                            </div>


                            {{-- Bilan annuel uniquement pour le 3ème trimestre --}}
                            @if (str_contains(strtolower($trimestre->nom ?? ''), '3') ||
                                    str_contains(strtolower($trimestre->nom ?? ''), 'troisième'))
                                <table style="margin-top: 20px;">
                                    <thead>
                                        <tr>
                                            <th>BILAN ANNUEL</th>
                                            <th>1er Trimestre</th>
                                            <th>2ème Trimestre</th>
                                            <th>3ème Trimestre</th>
                                            <th>Moyenne Annuelle</th>
                                            <th>Rang Annuel</th>
                                            <th>Décision du Conseil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="font-weight: bold;">Moyennes</td>
                                            <td>{{ $moyennesGeneralesAnnuelles[1] ?? ($moyennesGenerales[1] ?? '--') }}/20
                                            </td>
                                            <td>{{ $moyennesGeneralesAnnuelles[2] ?? ($moyennesGenerales[2] ?? '--') }}/20
                                            </td>
                                            <td>{{ $moyennesGeneralesAnnuelles[3] ?? ($moyennesGenerales[3] ?? '--') }}/20
                                            </td>
                                            <td style="font-weight: bold; color: #1e40af;">
                                                {{ $moyenneGeneraleAnnuelle ?? '--' }}/20</td>
                                            <td style="font-weight: bold;">{{ $rangAnnuel ?? '--' }}</td>
                                            <td
                                                style="font-weight: bold; color: {{ str_contains($decisionConseil ?? '', 'Admis') ? '#059669' : '#dc2626' }};">
                                                {{ $decisionConseil ?? 'En attente' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif

                            <div class="footer">
                                <div class="signature">
                                    <p>LE DIRECTEUR</p>
                                    <p>Signature</p>
                                </div>
                                <div class="signature">
                                    <p>LE PARENT/TUTEUR</p>
                                    <p>Signature</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Aucun trimestre trouvé pour cette année académique.</strong>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function printSingleBulletin(trimestreId) {
            const bulletinPage = document.getElementById('bulletin-page-' + trimestreId);
            if (!bulletinPage) {
                alert('Bulletin non trouvé pour le trimestre ' + trimestreId);
                return;
            }

            const content = bulletinPage.outerHTML;
            const styles = document.querySelector('style').innerHTML;

            const printWindow = window.open('', '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
            printWindow.document.write(`
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Bulletin Scolaire - Trimestre</title>
                    <style>${styles}</style>
                </head>
                <body>
                    <div class="bulletin-container">
                        <div class="content-wrapper">
                            ${content}
                        </div>
                    </div>
                </body>
            </html>
        `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        function printAllBulletins() {
            const allBulletins = document.querySelectorAll('.bulletin-page');
            if (allBulletins.length === 0) {
                alert('Aucun bulletin à imprimer');
                return;
            }

            let content = '';
            allBulletins.forEach(bulletin => {
                content += bulletin.outerHTML;
            });

            const styles = document.querySelector('style').innerHTML;

            const printWindow = window.open('', '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');
            printWindow.document.write(`
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Bulletins Scolaires - Année Complète</title>
                    <style>${styles}</style>
                </head>
                <body>
                    <div class="bulletin-container">
                        <div class="content-wrapper">
                            ${content}
                        </div>
                    </div>
                </body>
            </html>
        `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    </script>
@endsection
