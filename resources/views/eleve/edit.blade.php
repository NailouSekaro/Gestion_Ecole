@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Mettre à jour les informations d'un élève </h3>
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Forms</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form elements</li>
                    </ol> --}}
            </nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Remplir le formulaire</h4>
                        <p class="card-description"> Avec les éléments correspondants </p>
                        <form class="forms-sample" method="POST" action="{{ route('eleve.update', $eleve->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="type" value="{{ $inscription->type }}">
                            <div class="form-group">
                                <label for="exampleInputPassword4">Matricule eduque master</label>
                                <input type="number" class="form-control" id="exampleInputPassword4"
                                    placeholder="matricule eduque master" min="0" oninput="limiterChiffres(this,13)"
                                    name="matricule_educ_master" value={{ $eleve->matricule_educ_master }}>
                            </div>


                            @error('matricule_educ_master')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputName1">Nom</label>
                                <input type="text" class="form-control" id="exampleInputName1"
                                    placeholder="Nom de l'élève" name="nom" value={{ $eleve->nom }}>
                            </div>

                            @error('nom')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleSelectGender">Prénom(s)</label>
                                <input type="text" class="form-control" id="exampleInputName1"
                                    placeholder="Prénom(s) de l'élève" name="prenom" value={{ $eleve->prenom }}>
                            </div>

                            @error('prenom')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputPassword4">Année académique</label>
                                <select class="form-control" name="annee_academique_id" id="exampleSelectGender">
                                    <option></option>
                                    @foreach ($annees as $annee)
                                        <option value="{{ $annee->id }}"
                                            {{ $inscription->annee_academique_id == $annee->id ? 'selected' : '' }}>
                                            {{ $annee->annee }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('annee_academique_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleSelectGender">Classe</label>
                                <select class="form-control" name="classe_id" id="exampleSelectGender">
                                    <option></option>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe->id }}"
                                            {{ $inscription->classe_id == $classe->id ? 'selected' : '' }}>
                                            {{ $classe->nom_classe }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('classe_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleSelectGender">Sexe</label>
                                <select name="sexe" class="form-control" id="exampleSelectGender">
                                    <option value=""></option>
                                    <option value="M">M</option>
                                    <option value="F">F</option>
                                    <option value="{{ $eleve->sexe }}"
                                        {{ $eleve->sexe == $eleve->sexe ? 'selected' : '' }}>
                                        {{ $eleve->sexe }}</option>
                                </select>
                            </div>

                            @error('sexe')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleSelectGender">Statut</label>
                                <select name="statut" class="form-control" id="exampleSelectGender">
                                    <option value=""></option>
                                    <option value="Passant(e)">Passant(e)</option>
                                    <option value="Doublant(e)">Doublant(e)</option>
                                    @foreach ($statuts as $statut)
                                        <option value="{{ $statut }}"
                                            {{ $inscription->statut == $statut ? 'selected' : '' }}>
                                            {{ $statut }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('statut')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputCity1">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-control" id="exampleInputCity1"
                                    max="<?= date('Y-m-d') ?>" value="{{ $eleve->date_naissance }}" placeholder="">
                            </div>

                            @error('date_naissance')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputCity1">Lieu de naissance</label>
                                <input type="texte" name="lieu_de_naissance" class="form-control" id="exampleInputCity1"
                                    placeholder="Lieu de naissance" value="{{ $eleve->lieu_de_naissance }}">
                            </div>

                            @error('lieu_de_naissance')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputCity1">Nationalité</label>
                                <input type="texte" name="nationalite" class="form-control" id="exampleInputCity1"
                                    placeholder="Nationalité" value="{{ $eleve->nationalite }}">
                            </div>

                            @error('nationalite')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleSelectGender">Aptitude Sport</label>
                                <select name="aptitude_sport" class="form-control" id="exampleSelectGender">
                                    <option></option>
                                    <option value="Oui">Oui</option>
                                    <option value="Non">Non</option>
                                    <option value="{{ $eleve->aptitude_sport }}"
                                        {{ $eleve->aptitude_sport == $eleve->aptitude_sport ? 'selected' : '' }}>
                                        {{ $eleve->aptitude_sport }}</option>
                                </select>
                            </div>

                            @error('aptitude_sport')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputCity1">Email parent</label>
                                <input type="email" name="email_parent" class="form-control" id="exampleInputCity1"
                                    placeholder="Entrer l'adresse email" value="{{ $eleve->email_parent }}">
                            </div>

                            @error('email_parent')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputCity1">Contact parent</label>
                                <input type="number" name="contact_parent" class="form-control" id="exampleInputCity1"
                                    placeholder="Entrer le contact" oninput="limiterChiffres(this,10)" value="{{ $eleve->contact_parent }}">
                            </div>

                            @error('contact_parent')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label>Photo d'identitée</label>
                                {{-- <input type="file" id="fileInput" style="display: none;"
                                    onchange="afficherNomFichier()" class="file-upload-default"> --}}
                                <div class="input-group col-xs-12">
                                    <input type="file" name="photo" id="photo" accept="image/*" readonly
                                        class="form-control file-upload-info" placeholder="Insérer image"
                                        value="{{ $eleve->photo }}">

                                    {{-- <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary"
                                            onclick="document.getElementById('fileInput').click();"
                                            type="button">Insérer</button>
                                    </span> --}}
                                </div>
                            </div>

                            {{-- <script>
                                function afficherNomFichier() {
                                    var input = document.getElementById('fileInput');
                                    var fileNameField = document.getElementById('fileName');
                                    fileNameField.value = input.files[0] ? input.files[0].name : "Aucun fichier sélectionné";
                                }
                            </script> --}}

                            {{-- <div class="form-group">
                                <label for="exampleTextarea1">Textarea</label>
                                <textarea class="form-control" id="exampleTextarea1" rows="4"></textarea>
                            </div> --}}
                            <button type="submit" class="btn btn-primary mr-2"
                                onclick="return confirm('Êtes-vous sûr de vouloir continuer ?')">Mettre à jour</button>
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
