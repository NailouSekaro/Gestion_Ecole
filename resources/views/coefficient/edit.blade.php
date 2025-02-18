@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Modification des coefficients. </h3>
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
                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                            </div>
                        @endif
                        <form class="forms-sample" method="POST"
                            action="{{ route('coefficient.update', $coefficient->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="exampleInputName1">Classe</label>
                                <select class="form-control" name="classe_id" id="exampleSelectGender">
                                    <option>Sélectionner une classe </option>
                                    @foreach ($classes as $classe)
                                        <option value="{{ $classe->id }}"
                                            {{ $coefficient->classe_id == $classe->id ? 'selected' : '' }}>
                                            {{ $classe->nom_classe }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('classe_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="exampleInputName1">Matiere</label>
                                <select class="form-control" name="matiere_id" id="exampleSelectGender">
                                    <option>Sélectionner une matiere</option>
                                    @foreach ($matieres as $matiere)
                                        <option value="{{ $matiere->id }}"
                                            {{ $coefficient->matiere_id == $matiere->id ? 'selected' : '' }}>
                                            {{ $matiere->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @error('matiere_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputName1">Coefficient</label>
                                <input type="number" class="form-control" id="exampleInputPassword4"
                                    placeholder="Entrer la valeur du coefficient" min="1" name="valeur_coefficient"
                                    value={{ $coefficient->valeur_coefficient }}>
                            </div>

                            @error('valeur_coefficient')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <button type="submit" class="btn btn-primary mr-2">Envoyer</button>
                            <button class="btn btn-dark" type="reset">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- <script>
            function limiterChiffres(input, maxLength) {
                if (input.value.length > maxLength) {
                    input.value = input.value.slice(0, maxLength);
                }
            }
        </script> --}}
    @endsection
