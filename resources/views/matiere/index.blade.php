@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Listes des matières</h3>
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
                                        <th> Matières </th>
                                        {{-- <th> Cycle </th>
                                        <th> Scolarité </th>
                                        <th> Liste des élèves </th> --}}
                                        {{-- <th> Liste </th> --}}
                                        <th> Actions </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($matieres as $matiere)
                                        <tr>
                                            <td> {{ $loop->iteration }} </td>
                                            <td> {{ $matiere->nom }} </td>
                                            {{-- <td> {{ $classe->cycle }} </td>
                                            <td> {{ $classe->frais_scolarite }} </td> --}}
                                            {{-- <td> 0 </td> --}}
                                            {{-- <td> <a href="{{ route('classe.liste', $classe->id) }}" type="button"
                                                    class="btn btn-inverse-warning btn-fw">Voir liste</a> </td> --}}
                                            <td><a href="{{ route('matiere.edit', ['matiere' => $matiere->id]) }}"
                                                    type="button" class="btn btn-inverse-primary btn-fw">Edit</a>
                                                <button type="button" class="btn btn-inverse-danger btn-fw"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir continuer ?')">Delete</button>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td> Aucune matière créée </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav class="app-pagination col-lg-1">
                        {{ $matieres->links() }}
                    </nav>
                </div>
            </div>

        </div>
    @endsection
