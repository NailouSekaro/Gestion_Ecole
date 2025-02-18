@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Listes des années académiques</h3>
            <nav aria-label="breadcrumb">
                {{-- <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Tables</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Basic tables</li>
                </ol> --}}
            </nav>
        </div>
        @if (Session::get('success_message'))
            <div class="alert alert-success alert-dismissible fade show">
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
                                        <th> Année académique </th>
                                        <th> Date début </th>
                                        <th> Date fin </th>
                                        {{-- <th> Liste des élèves </th> --}}
                                        {{-- <th> Liste </th> --}}
                                        <th> Actions </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($annees as $annee)
                                        <tr>
                                            <td> {{ $loop->iteration }} </td>
                                            <td> {{ $annee->annee }} </td>
                                            <td> {{ $annee->date_debut->format('d/m/Y') }} </td>
                                            <td> {{ $annee->date_fin->format('d/m/Y') }} </td>
                                            {{-- <td> 0 </td> --}}
                                            {{-- <td> <a href="{{ route('classe.liste', $classe->id) }}" type="button"
                                                    class="btn btn-inverse-warning btn-fw">Voir liste</a> </td> --}}
                                            <td><a href="{{ route('annee.edit', ['annee_academique' => $annee->id]) }}"
                                                    type="button" class="btn btn-inverse-primary btn-fw">Edit</a>
                                                <button type="button" class="btn btn-inverse-danger btn-fw"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir continuer ?')">Delete</button>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td> Aucune année académique créée </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <nav class="app-pagination col-lg-1">
                        {{ $annees->links() }}
                    </nav>
                </div>
            </div>

        </div>
    @endsection
