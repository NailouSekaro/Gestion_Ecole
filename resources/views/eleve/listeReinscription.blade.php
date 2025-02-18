@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Listes des élèves réinscrire.</h3>
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                  <li class="breadcrumb-item">Effectif total : {{ $effectif_total }} </li>
                  <li class="breadcrumb-item">Garçons : {{ $garçon }} </li>
                  <li class="breadcrumb-item">Filles : {{ $fille }} </li>
                  <li class="breadcrumb-item">Passants : {{ $passant}} </li>
                  <li class="breadcrumb-item">Doublants : {{ $doublant }} </li>
                </ol> --}}
            </nav>
        </div>
        @if (Session::get('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-bs-dismiss="alert"
                aria-label="Close">
                {{ Session::get('success_message') }}

            </div>
        @endif


        @if (session('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        {{-- <h4 class="card-title"> Classes disponibles </h4> --}}
                        <p class="card-description">
                        </p>
                        <div class="table-responsive">
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th>Photo</th>
                                        <th>Matricule</th>
                                        <th> Nom </th>
                                        <th> Prénom </th>
                                        <th> Sexe </th>
                                        <th> Classe </th>
                                        <th> Date de naissance </th>
                                        <th>Lieu de naissance</th>
                                        <th>Aptitude sport</th>
                                        <th>Email parent</th>
                                        <th>Contact parent</th>
                                        <th>Statut</th>
                                        <th>Année académique</th>
                                        <th> Actions </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reinscriptions as $inscription)
                                        <tr>
                                            <td> {{ $loop->iteration }} </td>
                                            @if ($inscription->eleve->photo)
                                                <td>
                                                    <img src="{{ Storage::url($inscription->eleve->photo) }}"
                                                        alt="Photo de l'élève" width="150" height="150">
                                                </td>
                                            @else
                                                <td><img src="{{ $inscription->eleve->photo ? asset('storage/' . $inscription->eleve->photo) : asset('public/images/default-avatar.jpg') }}"
                                                        alt="Photo par défaut" width="150" height="150"></td>
                                            @endif
                                            <td>{{ $inscription->eleve->matricule_educ_master }}</td>
                                            <td> {{ $inscription->eleve->nom }} </td>
                                            <td> {{ $inscription->eleve->prenom }} </td>
                                            <td> {{ $inscription->eleve->sexe }} </td>
                                            <td>{{ $inscription->classe ? $inscription->classe->nom_classe : 'Non attribué' }}
                                            </td>


                                            <td>{{ $inscription->eleve->date_naissance }}</td>
                                            <td>{{ $eleve->lieu_de_naissance }}</td>
                                            <td>{{ $eleve->aptitude_sport }}</td>
                                            <td>{{ $eleve->email_parent }}</td>
                                            <td>{{ $eleve->contact_parent }}</td>
                                            <td>
                                                {{ $inscription->statut }}
                                            </td>

                                            <td>{{ $inscription->Annee_academique ? $inscription->Annee_academique->annee : 'Non attribué' }}
                                            </td>

                                            <td>
                                                {{-- <a href="{{ route('eleve.edit', ['eleve' => $eleve->id]) }}" type="button"
                                                    class="btn btn-inverse-primary btn-fw">Edit</a> --}}
                                                <button type="button" class="btn btn-inverse-danger btn-fw"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir continuer ?')">Delete</button>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td> Aucun élève réinscrire. </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav class="app-pagination">
                        {{-- {{ $eleves->links() }} --}}
                    </nav>
                </div>
            </div>

        </div>
    @endsection
