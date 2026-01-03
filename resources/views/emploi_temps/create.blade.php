@extends('layaouts.template')

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Créer un Emploi du Temps</h3>
        </div>

        {{-- <form method="POST" action="{{ route('emploi_temps.generate') }}">
            @csrf
            <div class="form-group">
                <label for="annee_academique_id">Année Académique</label>
                <select name="annee_academique_id" class="form-control" required>
                    <option value="">Sélectionner</option>
                    @foreach ($anneesAcademiques as $annee)
                        <option value="{{ $annee->id }}">{{ $annee->annee }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Générer Emplois Automatiquement</button>
        </form> --}}

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Ajouter un créneau</h4>


                        @if (session('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form class="forms-sample" method="POST" action="{{ route('emploi_temps.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="classe_id">Classe</label>
                                <select name="classe_id" class="form-control" required>
                                    <option value="">Sélectionner une classe</option>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe->id }}">{{ $classe->nom_classe }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="matiere_id">Matière</label>
                                <select name="matiere_id" class="form-control" required>
                                    <option value="">Sélectionner une matière</option>
                                    @foreach ($matieres as $matiere)
                                        <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <label for="enseignant_id">Enseignant</label>
                                <select name="enseignant_id" class="form-control" required>
                                    <option value="">Sélectionner un enseignant</option>
                                    @foreach ($enseignants as $enseignant)
                                        <option value="{{ $enseignant->id }}">{{ $enseignant->user->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <div class="form-group">
                                <label for="annee_academique_id">Année Académique</label>
                                <select name="annee_academique_id" class="form-control" required>
                                    <option value="">Sélectionner une année</option>
                                    @foreach ($anneesAcademiques as $annee)
                                        <option value="{{ $annee->id }}">{{ $annee->annee }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="jour">Jour</label>
                                <select name="jour" class="form-control" required>
                                    <option value="Lundi">Lundi</option>
                                    <option value="Mardi">Mardi</option>
                                    <option value="Mercredi">Mercredi</option>
                                    <option value="Jeudi">Jeudi</option>
                                    <option value="Vendredi">Vendredi</option>
                                    <option value="Samedi">Samedi</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="heure_debut">Heure de début</label>
                                <input type="time" name="heure_debut" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="heure_fin">Heure de fin</label>
                                <input type="time" name="heure_fin" class="form-control" required>
                            </div>

                            {{-- <div class="form-group">
                                <label for="salle">Salle (facultatif)</label>
                                <input type="text" name="salle" class="form-control">
                            </div> --}}

                            <button type="submit" class="btn btn-primary mr-2">Enregistrer</button>
                            <button class="btn btn-dark" type="reset">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classeSelect = document.getElementById('classe_id');
            const matiereSelect = document.getElementById('matiere_id');
            const anneeSelect = document.getElementById('annee_academique_id');
            const enseignantSelect = document.getElementById('enseignant_id');

            function updateEnseignant() {
                const classeId = classeSelect.value;
                const matiereId = matiereSelect.value;
                const anneeId = anneeSelect.value;

                if (classeId && matiereId && anneeId) {
                    fetch(`{{ route('emploi_temps.getEnseignant') }}?classe_id=${classeId}&matiere_id=${matiereId}&annee_academique_id=${anneeId}`)
                        .then(response => response.json())
                        .then(data => {
                            enseignantSelect.innerHTML = '<option value="">Sélectionner un enseignant</option>';
                            if (data.id) {
                                const option = new Option(data.name, data.id);
                                enseignantSelect.appendChild(option);
                                enseignantSelect.value = data.id;
                            } else {
                                alert('Aucun enseignant trouvé pour cette combinaison.');
                            }
                        });
                }
            }

            classeSelect.addEventListener('change', updateEnseignant);
            matiereSelect.addEventListener('change', updateEnseignant);
            anneeSelect.addEventListener('change', updateEnseignant);
        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classeSelect = document.getElementById('classe_id');
            const matiereSelect = document.getElementById('matiere_id');
            const anneeSelect = document.getElementById('annee_academique_id');
            const enseignantSelect = document.getElementById('enseignant_id');

            function updateEnseignant() {
                const classeId = classeSelect.value;
                const matiereId = matiereSelect.value;
                const anneeId = anneeSelect.value;

                console.log('Fetching enseignant for:', {
                    classeId,
                    matiereId,
                    anneeId
                }); // Debug

                if (classeId && matiereId && anneeId) {
                    fetch(`{{ route('emploi_temps.getEnseignant') }}?classe_id=${classeId}&matiere_id=${matiereId}&annee_academique_id=${anneeId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            console.log('Response:', data); // Debug
                            enseignantSelect.innerHTML = '<option value="">Sélectionner un enseignant</option>';
                            if (data.id) {
                                const option = new Option(data.name, data.id);
                                enseignantSelect.appendChild(option);
                                enseignantSelect.value = data.id;
                            } else {
                                alert('Aucun enseignant trouvé pour cette combinaison.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }

            classeSelect.addEventListener('change', updateEnseignant);
            matiereSelect.addEventListener('change', updateEnseignant);
            anneeSelect.addEventListener('change', updateEnseignant);
        });
    </script>
@endsection
