@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> Modification des notes de {{ $eleve->nom }} {{ $eleve->prenom }}. </h3>
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
                        <form class="forms-sample" method="POST"
                            action="">
                            @csrf
                            @method('PUT')

                            {{-- <h2>Notes de {{ $eleve->nom }}</h2> --}}

                            @foreach ($matieres as $matiere)
                                <h3>{{ $matiere->nom }}</h3>

                                @foreach (['interrogation 1', 'interrogation 2', 'interrogation 3', 'Devoir 1', 'Devoir 2'] as $typeEvaluation)
                                    @php
                                        $note = $notesExistantes[$matiere->id][$typeEvaluation][0] ?? null;
                                    @endphp
                                    <div class="form-group">
                                        <label
                                            for="exampleInputName1">{{ ucfirst(str_replace('_', ' ', $typeEvaluation)) }}</label>
                                        <input type="number" class="form-control" id="exampleInputPassword4"
                                            placeholder="Entrer la note"
                                            name="notes[{{ $matiere->id }}][{{ $typeEvaluation }}]"
                                            value="{{ $note ? $note->valeur_note : '' }}" min="0" max="20"
                                            step="0.01">
                                    </div>
                                @endforeach
                            @endforeach

                            <button type="submit" class="btn btn-primary mr-2">Modifier</button>
                            <button class="btn btn-dark" type="reset">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
