<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="index.html"><img src="{{ asset('assets/images/logo.svg') }}"
                alt="logo" /></a>
        <a class="sidebar-brand brand-logo-mini" href="index.html"><img src="{{ asset('assets/images/logo-mini.svg') }}"
                alt="logo" /></a>
    </div>
    <ul class="nav">
        <li class="nav-item profile">
            <div class="profile-desc">
                <div class="profile-pic">
                    <div class="navbar-profile">
                        <img class="img-xs rounded-circle"
                            src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('assets/images/faces/face15.jpg') }}"
                            alt="Photo de profil">
                    </div>
                    <div class="profile-name">
                        <h5 class="mb-0 font-weight-normal">{{ Auth::user()->name }} {{ Auth::user()->prenom }}</h5>
                        <span>Gold Member</span>
                    </div>
                </div>
                <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list"
                    aria-labelledby="profile-dropdown">
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-settings text-primary"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-onepassword  text-info"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-calendar-today text-success"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                        </div>
                    </a>
                </div>
            </div>
        </li>
        <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Acceuil</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-icon">
                    <i class="mdi mdi-laptop"></i>
                </span>
                <span class="menu-title">Elève</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.create') }}">Ajout d'un nouveau
                            élève</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.index') }}">Listes des élèves</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.listeReinscription') }}">Listes des
                            réinscriptions</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.recherche') }}"> Listes élèves
                            par classe </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-paiement" aria-expanded="false" aria-controls="ui-paiement">
                <span class="menu-icon">
                    <i class="mdi mdi-laptop"></i>
                </span>
                <span class="menu-title">Paiement</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-paiement">
                <ul class="nav flex-column sub-menu">
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.create') }}">Ajout d'un nouveau
                            élève</a></li> --}}
                    <li class="nav-item"> <a class="nav-link" href="{{ route('paiement.index') }}">Listes des paiements</a>
                    </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('paiements.recherche') }}">Paiements par classe</a>
                    </li>
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.listeReinscription') }}">Listes
                            des
                            réinscriptions</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.recherche') }}"> Listes élèves
                            par classe </a> --}}
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-classe" aria-expanded="false"
                aria-controls="ui-classe">
                <span class="menu-icon">
                    <i class="mdi mdi-table-large"></i>
                </span>
                <span class="menu-title">Classe</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-classe">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('classe.create') }}">Création de
                            classe</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('classe.index') }}">Listes des
                            classes</a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('classe.create') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-playlist-play"></i>
                </span>
                <span class="menu-title">Création de Classe</span>
            </a>
        </li> --}}

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-enseignant" aria-expanded="false"
                aria-controls="ui-enseignant">
                <span class="menu-icon">
                    <i class="mdi mdi-laptop"></i>
                </span>
                <span class="menu-title">Enseignant</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-enseignant">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('enseignant.create') }}">Ajout d'un
                            enseignant</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('enseignant.index') }}">Listes des
                            enseignants</a>
                    </li>
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('enseignant.listeReinscription') }}">Listes
                            des
                            réinscriptions</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.recherche') }}"> Listes élèves
                            par classe </a>
                    </li> --}}
                </ul>
            </div>
        </li>


        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-matiere" aria-expanded="false"
                aria-controls="ui-matiere">
                <span class="menu-icon">
                    <i class="mdi mdi-laptop"></i>
                </span>
                <span class="menu-title">Matiere</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-matiere">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('matiere.create') }}">Ajout d'une
                            matiere</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('matiere.index') }}">Listes des
                            matieres</a>
                    </li>
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('enseignant.listeReinscription') }}">Listes
                            des
                            réinscriptions</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.recherche') }}"> Listes élèves
                            par classe </a>
                    </li> --}}
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-annee" aria-expanded="false"
                aria-controls="ui-annee">
                <span class="menu-icon">
                    <i class="mdi mdi-chart-bar"></i>
                </span>
                <span class="menu-title">Année académique</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-annee">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('annee.create') }}">Créer
                            année académique</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('annee.index') }}">Listes
                            années académiques</a>
                    </li>
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('enseignant.listeReinscription') }}">Listes
                            des
                            réinscriptions</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.recherche') }}"> Listes élèves
                            par classe </a>
                    </li> --}}
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-trimestre" aria-expanded="false"
                aria-controls="ui-trimestre">
                <span class="menu-icon">
                    <i class="mdi mdi-laptop"></i>
                </span>
                <span class="menu-title">Trimestre</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-trimestre">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('trimestre.create') }}">Ajout d'un
                            trimestre</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('trimestre.index') }}">Listes des
                            trimestres</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#ui-coefficient" aria-expanded="false"
                aria-controls="ui-coefficient">
                <span class="menu-icon">
                    <i class="mdi mdi-laptop"></i>
                </span>
                <span class="menu-title">Coefficient</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-coefficient">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> <a class="nav-link" href="{{ route('coefficient.create') }}">Ajout d'un
                            coefficient</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('coefficient.index') }}">Listes des
                            coefficients</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link" href="#">
                <span class="menu-icon">
                    <i class="mdi mdi-contacts"></i>
                </span>
                <span class="menu-title">Contacts </span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <span class="menu-icon">
                    <i class="mdi mdi-security"></i>
                </span>
                <span class="menu-title">User Pages</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('eleve.recherche') }}"> Listes élèves
                            par classe </a>
                    </li> --}}
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('matiere.create') }}">Création des
                            matières </a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('matiere.index') }}"> Listes des
                            matières </a></li> --}}
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('trimestre.create') }}"> Création de
                            trimestre </a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('trimestre.index') }}"> Listes des
                            trimestres </a></li> --}}
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('coefficient.create') }}"> Ajout des
                            coefficients </a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('coefficient.index') }}"> Listes des
                            coefficients </a></li> --}}
                    {{-- <li class="nav-item"> <a class="nav-link" href="{{ route('enseignant.create') }}"> Ajout d'un
                            enseignant </a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('enseignant.index') }}"> Listes des
                            enseignants </a></li> --}}
                </ul>
            </div>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link"
                href="http://www.bootstrapdash.com/demo/corona-free/jquery/documentation/documentation.html">
                <span class="menu-icon">
                    <i class="mdi mdi-file-document-box"></i>
                </span>
                <span class="menu-title">Paramètre de paiements</span>
            </a>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link"
                href="{{ route('payment.configuration') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-file-document-box"></i>
                </span>
                <span class="menu-title">Configurations</span>
            </a>
        </li>

        <li class="nav-item menu-items">
            <a class="nav-link"
                href="http://www.bootstrapdash.com/demo/corona-free/jquery/documentation/documentation.html">
                <span class="menu-icon">
                    <i class="mdi mdi-file-document-box"></i>
                </span>
                <span class="menu-title">Documentation</span>
            </a>
        </li>
    </ul>
</nav>
