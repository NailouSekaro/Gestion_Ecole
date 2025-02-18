@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Listes des trimestres</h3>
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Tables</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Basic tables</li>
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
                                        <th> Nom </th>
                                        <th> Date début </th>
                                        <th> Date fin </th>
                                        <th> Année académique </th>

                                        {{-- <th> Liste </th> --}}
                                        <th> Actions </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($trimestres as $trimestre)
                                        <tr>
                                            <td> {{ $loop->iteration }} </td>
                                            <td> {{ $trimestre->nom }} </td>
                                            <td> {{ $trimestre->date_debut->format('d/m/Y') ? : 'Pas renseignée' }} </td>
                                            <td> {{ $trimestre->date_fin->format('d/m/Y')  ? : 'Pas renseignée' }} </td>
                                            <td> {{ $trimestre->Annee_academique ? $trimestre->Annee_academique->annee : 'Pas trouvé' }}
                                            </td>

                                            {{-- <td> 0 </td> --}}
                                            {{-- <td> <a href="{{ route('classe.liste', $classe->id) }}" type="button"
                                                    class="btn btn-inverse-warning btn-fw">Voir liste</a> </td> --}}
                                            <td><a href="{{ route('trimestre.edit', ['trimestre' => $trimestre->id]) }}"
                                                    type="button" class="btn btn-inverse-primary btn-fw">Edit</a>
                                                <button type="button" class="btn btn-inverse-danger btn-fw"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir continuer ?')">Delete</button>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td> Aucun trimestre créé </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav class="app-pagination col-lg-1">
                        {{ $trimestres->links() }}
                    </nav>
                </div>
            </div>

        </div>
    @endsection
