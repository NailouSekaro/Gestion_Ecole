@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Créer une nouvelle année académique. </h3>
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

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}

                            </div>
                        @endif

                        {{-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}
                        <p class="card-description"> Avec les éléments correspondants </p>
                        <form class="forms-sample" method="POST" action="{{ route('annee.store') }}">
                            @csrf
                            @method('POST')

                            <div class="form-group">
                                <label for="exampleInputName1">Année Academique</label>
                                <input type="text" class="form-control" id="exampleInputPassword4"
                                    placeholder="xxxx-xxxx" maxlength="9" oninput="limiterChiffres(this,9)" min="0"
                                    name="annee" value={{ old('annee') }}>
                            </div>

                            @error('annee')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputCity1">Date début </label>
                                <input type="date" name="date_debut" class="form-control" id="date_fin" placeholder=""
                                    value="{{ old('date_debut', $dateDebutDefault) }}">
                            </div>

                            @error('date_debut')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputCity1">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control" id="date_fin" placeholder=""
                                    value="{{ old('date_fin', $dateFinDefault) }}">
                            </div>

                            @error('date_fin')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            {{-- <div class="form-group">
                                <label for="exampleSelectGender">Cycle</label>
                                <select class="form-control" id="exampleSelectGender" name="cycle"
                                    value={{ old('cycle') }}>
                                    <option></option>
                                    <option>Premier cycle</option>
                                    <option>Second cycle</option>
                                </select>
                            </div>

                            @error('cycle')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputPassword4">Scolarité</label>
                                <input type="number" min="0" class="form-control" id="exampleInputPassword4"
                                    placeholder="Frais de scolarité" name="frais_scolarite"
                                    value={{ old('frais_scolarite') }}>
                            </div>

                            @error('frais_scolarite')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror --}}

                            {{-- <div class="form-group">
                                    <label for="exampleSelectGender">Gender</label>
                                    <select class="form-control" id="exampleSelectGender">
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>File upload</label>
                                    <input type="file" name="img[]" class="file-upload-default">
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled
                                            placeholder="Upload Image">
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary"
                                                type="button">Upload</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputCity1">City</label>
                                    <input type="text" class="form-control" id="exampleInputCity1"
                                        placeholder="Location">
                                </div>
                                <div class="form-group">
                                    <label for="exampleTextarea1">Textarea</label>
                                    <textarea class="form-control" id="exampleTextarea1" rows="4"></textarea>
                                </div> --}}
                            <button type="submit" class="btn btn-primary mr-2">Envoyer</button>
                            <button class="btn btn-dark" type="reset">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            const form = document.getElementById('trimestreForm');
            const dateDebut = document.getElementById('date_debut');
            const dateFin = document.getElementById('date_fin');

            form.addEventListener('submit', (e) => {
                const debut = new Date(dateDebut.value);
                const fin = new Date(dateFin.value);

                if (fin < debut) {
                    e.preventDefault(); // Empêche l'envoi du formulaire
                    alert('La date de fin doit être postérieure ou égale à la date de début.');
                }
            });
        </script>
        <script>
            function limiterChiffres(input, maxLength) {
                if (input.value.length > maxLength) {
                    input.value = input.value.slice(0, maxLength);
                }
            }
        </script>
    @endsection
