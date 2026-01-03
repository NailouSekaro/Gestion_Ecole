@extends('layaouts.template')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Effectuez une recherche. </h3>
        </div>

        <form class="forms-sample" method="GET" action="{{ route('eleve.afficher') }}">
            <div class="form-group">
                <label for="exampleSelectGender">Classe</label>
                <select class="form-control" name="classe_id" id="exampleSelectGender">
                    <option></option>
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom_classe }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('classe_id')
                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
            @enderror

            <div class="form-group">
                <label for="exampleInputPassword4">Année académique</label>
                <select class="form-control" name="annee_academique_id" id="exampleSelectGender">
                    <option></option>
                    @foreach ($annees as $annee)
                        <option value="{{ $annee->id }}" {{ request('annee_academique_id') == $annee->id ? 'selected' : '' }}>
                            {{ $annee->annee }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('annee_academique_id')
                <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary mr-2">Rechercher</button>
        </form>

        <div class="row mt-4">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Listes des élèves de la classe et l'année académique sélectionnée.</h4>

                            {{-- Boutons d'action - N'apparaissent que si des inscriptions existent --}}
                            @if (isset($inscriptions) && $inscriptions->isNotEmpty())
                                <div>
                                    <a href="{{ route('note.saisie_collective', ['classe_id' => request('classe_id'), 'annee_academique_id' => request('annee_academique_id')]) }}"
                                       class="btn btn-success btn-lg mr-2">
                                        <i class="mdi mdi-pencil-box-multiple"></i> 📝 Saisie collective
                                    </a>
                                    <a href="{{ route('note.voir_classe', ['classe_id' => request('classe_id'), 'annee_academique_id' => request('annee_academique_id')]) }}"
                                       class="btn btn-info btn-lg">
                                        <i class="mdi mdi-eye"></i> 👁️ Voir toutes les notes
                                    </a>
                                </div>
                            @endif
                        </div>

                        <p class="card-description"></p>

                        @if (Session::get('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert"
                                data-bs-dismiss="alert" aria-label="Close">
                                {{ Session::get('success_message') }}
                            </div>
                        @endif

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            @if (isset($inscriptions))
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
                                        @forelse ($inscriptions as $inscription)
                                            <tr>
                                                <td> {{ $loop->iteration }} </td>
                                                <td>
                                                    <img src="{{ $inscription->eleve->photo
                                                        ? asset('storage/' . $inscription->eleve->photo)
                                                        : asset('images/default-avatar.jpg') }}"
                                                        alt="Photo de l'élève" width="150" height="150">
                                                </td>
                                                <td>{{ $inscription->eleve->matricule_educ_master }}</td>
                                                <td> {{ $inscription->eleve->nom }} </td>
                                                <td> {{ $inscription->eleve->prenom }} </td>
                                                <td> {{ $inscription->eleve->sexe }} </td>
                                                <td>{{ $inscription->classe ? $inscription->classe->nom_classe : 'Non attribué' }}
                                                </td>
                                                <td>{{ $inscription->eleve->date_naissance->format('d/m/Y') }}</td>
                                                <td>{{ $inscription->eleve->lieu_de_naissance }}</td>
                                                <td>{{ $inscription->eleve->aptitude_sport }}</td>
                                                <td>{{ $inscription->eleve->email_parent }}</td>
                                                <td>{{ $inscription->eleve->contact_parent }}</td>
                                                <td>{{ $inscription->statut }}</td>
                                                <td>{{ $inscription->Annee_academique->annee }}</td>

                                                <td>
                                                    <a href="{{ route('eleve.edit', ['eleve' => $inscription->eleve->id]) }}"
                                                        type="button" class="btn btn-inverse-primary btn-fw">Edit</a>
                                                    <a href="{{ route('eleve.reinscription', $inscription->eleve->id) }}"
                                                        type="button" class="btn btn-inverse-warning btn-fw">Réinscrire</a>
                                                    <a href="{{ route('paiement.create', ['eleve_id' => $inscription->eleve->id, 'annee_academique_id' => $inscription->annee_academique->id]) }}"
                                                        type="button" class="btn btn-inverse-success btn-fw">Payer
                                                        scolarité</a>
                                                    <a href="{{ route('note.create', ['eleve_id' => $inscription->eleve->id, 'annee_academique_id' => $inscription->annee_academique->id]) }}"
                                                        type="button" class="btn btn-inverse-secondary btn-fw">Insérer
                                                        notes</a>
                                                    <a href="{{ route('note.voir', ['eleve_id' => $inscription->eleve->id, 'annee_academique_id' => $inscription->annee_academique->id]) }}"
                                                        type="button" class="btn btn-inverse-info btn-fw">Voir
                                                        notes</a>

                                                    <a href="{{ route('absence.create', ['eleve_id' => $inscription->eleve->id, 'annee_academique_id' => $inscription->annee_academique->id]) }}"
                                                        type="button" class="btn btn-inverse-danger btn-fw">Absence</a>
                                                    <a href="{{ route('note.moyenne', ['eleve_id' => $inscription->eleve->id, 'annee_academique_id' => $inscription->annee_academique->id]) }}"
                                                        type="button" class="btn btn-inverse-yellow btn-fw">Bulletins</a>
                                                    @php
                                                        $numero = $inscription->eleve->contact_parent;
                                                        if (Str::startsWith($numero, '0')) {
                                                            $numero = '229' . substr($numero, 1);
                                                        }
                                                        $message = urlencode(
                                                            "Bonjour, je suis le responsable de l'école et je souhaite discuter de votre enfant.",
                                                        );
                                                    @endphp

                                                    <a href="https://wa.me/{{ $numero }}?text={{ $message }}"
                                                        target="_blank" class="btn btn-success">
                                                        <i class="fab fa-whatsapp"></i> Contacter le parent
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="15" class="text-center"> Aucun élève inscrit dans cette classe. </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                    <nav class="app-pagination">
                        {{-- {{ $eleves->links() }} --}}
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection
