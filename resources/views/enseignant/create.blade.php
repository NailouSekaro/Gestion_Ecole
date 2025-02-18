@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Ajouter un nouvel enseignant </h3>
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
                        <form class="forms-sample" method="POST" action="{{ route('enseignant.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="role" value="enseignant">

                            <div class="form-group">
                                <label for="exampleInputPassword4">Matricule</label>
                                <input type="number" class="form-control" id="exampleInputPassword4"
                                    placeholder="Matricule de l'enseignant" min="0"
                                    oninput="limiterChiffres(this,13)" name="matricule" value={{ old('matricule') }}>
                            </div>
                            @error('matricule')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputName1">Nom</label>
                                <input type="text" class="form-control" id="exampleInputName1"
                                    placeholder="Nom de l'enseignant" name="name" value="{{ old('name') }}">
                            </div>
                            @error('name')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputName1">Prénom(s)</label>
                                <input type="text" class="form-control" id="exampleInputName1"
                                    placeholder="Prénom(s) de l'enseignant" name="prenom" value="{{ old('prenom') }}">
                            </div>
                            @error('prenom')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputEmail1">Email</label>
                                <input type="email" class="form-control" id="exampleInputEmail1"
                                    placeholder="Email de l'enseignant" name="email" value="{{ old('email') }}">
                            </div>
                            @error('email')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputEmail1">Adresse</label>
                                <input type="text" class="form-control" id="exampleInputEmail1"
                                    placeholder="Quartier de l'enseignant" name="adresse" value="{{ old('adresse') }}">
                            </div>
                            @error('adresse')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputPassword4">Année académique</label>
                                <select class="form-control" name="annee_academique_id" id="exampleSelectGender">
                                    <option>Sélectionner une année académique</option>
                                    @foreach ($annees as $annee)
                                        <option value="{{ $annee->id }}">{{ $annee->annee }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('annee_academique_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputCity1">Contact</label>
                                <input type="number" name="telephone" class="form-control" id="exampleInputCity1"
                                    placeholder="Numéro de telephone" oninput="limiterChiffres(this,10)"
                                    value="{{ old('telephone') }}">
                            </div>
                            @error('telephone')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleSelectGender">Sexe</label>
                                <select name="sexe" class="form-control" id="exampleSelectGender">
                                    <option value="">Sélectionner le genre</option>
                                    <option value="M">M</option>
                                    <option value="F">F</option>
                                </select>
                            </div>
                            @error('sexe')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputPassword4">Matière</label>
                                <select class="form-control" name="matiere_id" id="exampleSelectGender">
                                    <option value="">Sélectionner la matiere de l'enseignant</option>
                                    @foreach ($matieres as $matiere)
                                        <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('matiere_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputName1">Diplômes(s)</label>
                                <input type="text" class="form-control" id="exampleInputName1"
                                    placeholder="Diplômes(s) de l'enseignant" name="diplomes"
                                    value="{{ old('diplomes') }}">
                            </div>
                            @error('diplomes')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleSelectClasses">Classes assignées</label>
                                <select name="classe_id[]" class="form-control" id="exampleSelectClasses" multiple>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe->id }}">{{ $classe->nom_classe }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Maintenez la touche Ctrl (Windows) ou Command (Mac)
                                    pour sélectionner plusieurs classes.</small>
                            </div>
                            @error('classe_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputName1">Mot de passe</label>
                                <input type="password" class="form-control" id="exampleInputName1"
                                    placeholder="Créer un mot de passe provisoire de l'enseignant" name="password"
                                    value="{{ old('password') }}">
                            </div>
                            @error('password')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label>Photo d'identitée</label>
                                <div class="input-group col-xs-12">
                                    <input type="file" name="photo" id="photo" accept="image/*" readonly
                                        class="form-control file-upload-info" placeholder="Insérer image">
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <label for="exampleInputCity1">Photo</label>
                                <input type="file" name="photo" class="form-control" id="exampleInputCity1"
                                    accept="image/*">
                            </div>
                            @error('photo')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror --}}

                            <button type="submit" class="btn btn-primary mr-2">Envoyer</button>
                            <button class="btn btn-dark" type="reset">Annuler</button>
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
