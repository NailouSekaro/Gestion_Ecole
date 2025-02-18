@extends('layaouts.template')
@section('content')
<h2>Notes de {{ $eleve->nom }} pour l'année académique {{ $eleve->annee_academique->annee }}</h2>
    <table cellpadding="10">
        <thead>
            <tr>
                <th>Matière</th>
                <th>Type d'évaluation</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notes as $note)
            <tr>
                <td>{{ $note->matiere->nom }}</td>
                <td>{{ $note->type_evaluation }}</td>
                <td>{{ $note->valeur }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
