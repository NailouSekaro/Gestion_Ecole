@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Modifier un enseignant </h3>
            <nav aria-label="breadcrumb"></nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Remplir le formulaire</h4>
                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                            </div>
                        @endif
                        <p class="card-description"> Avec les éléments correspondants </p>
                        <form class="forms-sample" method="POST" action="{{ route('enseignant.update', $enseignant->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="matricule">Matricule</label>
                                <input type="text" class="form-control" id="matricule" name="matricule"
                                    value="{{ $enseignant->matricule }}" required>
                            </div>

                            @error('matricule')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="name">Nom</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $enseignant->user->name }}" required>
                            </div>

                            @error('name')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom"
                                    value="{{ $enseignant->user->prenom }}" required>
                            </div>

                            @error('prenom')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $enseignant->user->email }}" required>
                            </div>

                            @error('email')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Laisser vide pour ne pas changer">
                            </div>

                            @error('password')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone"
                                    value="{{ $enseignant->telephone }}" required>
                            </div>

                            @error('telephone')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="sexe">Sexe</label>
                                <select class="form-control" id="sexe" name="sexe" required>
                                    <option value="M" {{ $enseignant->sexe == 'M' ? 'selected' : '' }}>Masculin
                                    </option>
                                    <option value="F" {{ $enseignant->sexe == 'F' ? 'selected' : '' }}>Féminin
                                    </option>
                                </select>
                            </div>

                            @error('sexe')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="diplomes">Diplômes</label>
                                <input type="text" class="form-control" id="diplomes" name="diplomes"
                                    value="{{ $enseignant->diplomes }}" required>
                            </div>

                            @error('diplomes')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="matiere_id">Matière</label>
                                <select class="form-control" id="matiere_id" name="matiere_id" required>
                                    @foreach ($matieres as $matiere)
                                        <option value="{{ $matiere->id }}"
                                            {{ $enseignant->matiere_id == $matiere->id ? 'selected' : '' }}>
                                            {{ $matiere->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('matiere_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse"
                                    value="{{ $enseignant->adresse }}" required>
                            </div>

                            @error('adresse')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="annee_academique_id">Année académique</label>
                                <select class="form-control" id="annee_academique_id" name="annee_academique_id" required>
                                    @foreach ($annees as $annee)
                                        <option value="{{ $annee->id }}"
                                            {{ $enseignant->annee_academique_id == $annee->id ? 'selected' : '' }}>
                                            {{ $annee->annee }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('annee_academique_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="classe_id">Classes assignées</label>
                                <select class="form-control" id="classe_id" name="classe_id[]" multiple required>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe->id }}"
                                            {{ in_array($classe->id, $enseignant->classes->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $classe->nom_classe }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Maintenez la touche Ctrl (Windows) ou Command (Mac)
                                    pour sélectionner plusieurs classes.</small>
                            </div>

                            @error('classe_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="photo">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo">
                            </div>

                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function limiterChiffres(input, maxLength) {
                if (input.value.length > maxLength) {
                    input.value = input.value.slice(0, maxLength);
                }
            }
        </script>
    @endsection
