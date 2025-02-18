@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Configuration des adresses de paiement CINETPAY</h3>
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
                        <form class="forms-sample" method="POST" action="{{ route('payments.Updateconfiguration') }}">
                            @csrf
                            @method('POST')

                            <div class="form-group">
                                <label for="exampleInputName1">API KEY</label>
                                <input type="text" class="form-control" id="exampleInputName1"
                                    placeholder="Renseigner l'api key" name="api_key" value="{{ $paymentInfo ? $paymentInfo->api_key : '' }}">
                            </div>

                            @error('api_key')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputName1">SITE ID</label>
                                <input type="text" class="form-control" id="exampleInputName1"
                                    placeholder="Renseigner votre site id" name="site_id" value="{{ $paymentInfo ? $paymentInfo->site_id : '' }}">
                            </div>

                            @error('site_id')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror


                            <div class="form-group">
                                <label for="exampleInputPassword4">SECRET KEY</label>
                                <input type="text" class="form-control" id="exampleInputPassword4"
                                    placeholder="Renseigner votre secret key" name="secret_key"
                                    value="{{ $paymentInfo ? $paymentInfo->secret_key : '' }}">
                            </div>

                            @error('secret_key')
                                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                            @enderror

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
                            <button type="submit" class="btn btn-primary mr-2">{{ $paymentInfo ? 'Mettre à jour' : 'Enregistrer' }}</button>
                            {{-- <button class="btn btn-dark" type="reset">Annuler</button> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
