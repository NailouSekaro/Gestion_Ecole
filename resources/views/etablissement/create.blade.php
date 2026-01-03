@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Configuration des informations d'établissement</h3>
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
                        @if (Session::get('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                data-bs-dismiss="alert" aria-label="Close">
                                {{ Session::get('success_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <h4 class="card-title">Remplir le formulaire</h4>
                        <p class="card-description"> Avec les éléments correspondants </p>
                        <form class="forms-sample" method="POST" action="{{ route('etablissement.updateconfiguration') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('POST')

                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}" id="">
                            <div class="form-group">
                                <label for="nom">Nom de l'établissement</label>
                                <input type="text" class="form-control" id="nom"
                                    placeholder="Nom de l'établissement" name="nom"
                                    value="{{ $etablissement ? $etablissement->nom : '' }}">
                            </div>
                            @error('nom')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="republique">République</label>
                                <input type="text" class="form-control" id="republique" placeholder="Pays / République"
                                    name="republique" value="{{ $etablissement ? $etablissement->republique : '' }}">
                            </div>
                            @error('republique')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="contact">Contact</label>
                                <input type="text" class="form-control" id="contact" placeholder="Téléphone ou email"
                                    name="contact" value="{{ $etablissement ? $etablissement->contact : '' }}">
                            </div>
                            @error('contact')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="devise">Devise</label>
                                <input type="text" class="form-control" id="devise" placeholder="Ex: CFA, EUR, USD"
                                    name="devise" value="{{ $etablissement ? $etablissement->devise : '' }}">
                            </div>
                            @error('devise')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <div class="form-group">
                                <label for="logo">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                                @if ($etablissement && $etablissement->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $etablissement->logo) }}" alt="Logo"
                                            width="100">
                                    </div>
                                @endif
                            </div>
                            @error('logo')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn btn-primary mr-2">
                                {{ $etablissement ? 'Mettre à jour' : 'Enregistrer' }}
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    @endsection
