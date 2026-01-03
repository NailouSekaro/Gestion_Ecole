<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>École d'Excellence - Éducation de Qualité</title>
    <meta name="description"
        content="Découvrez notre établissement scolaire de prestige. Excellence académique, infrastructure moderne et suivi personnalisé.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #4f46e5;
            --secondary-blue: #06b6d4;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --transition: all 0.4s cubic-bezier(0.645, 0.045, 0.355, 1);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            padding: 15px 0;
            transition: var(--transition);
        }

        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            font-weight: 500;
            margin: 0 8px;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-blue);
            transition: var(--transition);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(79, 70, 229, 0.85), rgba(6, 182, 212, 0.85)), url('https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 150px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .hero-subtitle {
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Section Title */
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 50px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 3px;
            background: var(--primary-blue);
            bottom: -10px;
            left: 25%;
            border-radius: 3px;
        }

        /* Classes/Programmes Cards */
        .card-hover {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            margin-bottom: 30px;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .carousel-item img {
            height: 250px;
            object-fit: cover;
            transition: var(--transition);
        }

        .card-body {
            padding: 25px;
        }

        .card-title {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .program-features {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }

        .program-features li {
            margin-bottom: 8px;
        }

        .program-features i {
            color: var(--primary-blue);
            width: 20px;
            text-align: center;
            margin-right: 5px;
        }

        .price-tag {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-blue);
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background-color: #f9f9f9;
        }

        .feature-box {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }

        /* Testimonials */
        .testimonial-section {
            padding: 80px 0;
            background: linear-gradient(rgba(79, 70, 229, 0.9), rgba(6, 182, 212, 0.9)), url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            color: white;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 30px;
            margin: 15px;
            transition: var(--transition);
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
        }

        .client-info {
            display: flex;
            align-items: center;
        }

        .client-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        /* Contact Form */
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            height: 50px;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding-left: 20px;
            transition: var(--transition);
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-blue);
        }

        textarea.form-control {
            height: auto;
            padding-top: 15px;
        }

        /* Footer */
        .footer {
            background-color: var(--dark-gray);
            color: white;
            padding: 60px 0 20px;
        }

        .footer-links h5 {
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .footer-links h5::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 2px;
            background: var(--primary-blue);
            bottom: -8px;
            left: 0;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #adb5bd;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            margin-right: 10px;
            transition: var(--transition);
        }

        .social-icons a:hover {
            background: var(--primary-blue);
            transform: translateY(-5px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
        }

        /* Modals */
        .custom-modal .modal-content {
            border-radius: 15px;
            overflow: hidden;
            border: none;
        }

        .custom-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border-bottom: none;
        }

        .custom-modal .modal-body {
            padding: 30px;
        }

        .custom-modal .form-control {
            padding-left: 45px;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-blue);
        }

        .login-btn,
        .verify-btn {
            background: var(--primary-blue);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }

        .login-btn:hover,
        .verify-btn:hover {
            background: var(--secondary-blue);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                padding: 100px 0;
                background-attachment: scroll;
            }

            .hero-title {
                font-size: 2rem;
            }

            .section-title::after {
                width: 30%;
                left: 35%;
            }
        }

        /* Back to top */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: none;
            z-index: 999;
            border-radius: 50%;
            width: 50px;
            height: 50px;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="#">
                <i class="fas fa-graduation-cap me-2"></i>École d'Excellence
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#programmes">Programmes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Avantages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#testimonials">Témoignages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item ms-lg-3 my-2 my-lg-0">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fas fa-sign-in-alt me-1"></i> Connexion
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container hero-content">
            <h1 class="hero-title display-4 fw-bold mb-4 animate__animated animate__fadeInDown">
                Bienvenue dans l'Excellence Éducative
            </h1>
            <p class="hero-subtitle lead mb-4 animate__animated animate__fadeIn animate__delay-1s">
                Un établissement de prestige alliant excellence académique, infrastructure moderne et suivi personnalisé
                pour la réussite de chaque élève.
            </p>
            <a href="#programmes"
                class="btn btn-primary btn-lg px-4 me-2 animate__animated animate__fadeInUp animate__delay-1s">
                <i class="fas fa-book-open me-1"></i> Découvrir nos Programmes
            </a>
            <button class="btn btn-outline-light btn-lg px-4 animate__animated animate__fadeInUp animate__delay-1s"
                data-bs-toggle="modal" data-bs-target="#emploiModal">
                <i class="fas fa-calendar-alt me-1"></i> Emploi du Temps
            </button>


            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="fas fa-credit-card me-1"></i> Paiement de Scolarité
            </button>

        </div>
    </section>

    <!-- Programmes/Classes -->
    <section id="programmes" class="py-5 bg-light">
    <div class="container py-5">
        <h2 class="text-center mb-5 section-title" data-aos="fade-up">Nos Programmes Éducatifs</h2>
        <div class="row g-4">
            <!-- Programme 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="program-card p-4 text-center bg-white shadow rounded-4 h-100">
                    <div class="mb-3">
                        <i class="fas fa-book-open fa-3x text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Programme du Collège</h5>
                    <p>
                        Un enseignement complet conforme au programme national, alliant rigueur académique et
                        apprentissage actif. Les élèves développent leurs compétences en mathématiques, sciences,
                        langues et culture générale.
                    </p>
                </div>
            </div>

            <!-- Programme 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="program-card p-4 text-center bg-white shadow rounded-4 h-100">
                    <div class="mb-3">
                        <i class="fas fa-flask fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Programme Scientifique et Technologique</h5>
                    <p>
                        Des cours pratiques et innovants en sciences et technologies pour stimuler la curiosité et
                        la créativité des élèves. Laboratoires modernes, ateliers de robotique et projets STEM sont au rendez-vous.
                    </p>
                </div>
            </div>

            <!-- Programme 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="program-card p-4 text-center bg-white shadow rounded-4 h-100">
                    <div class="mb-3">
                        <i class="fas fa-globe fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Programme Linguistique et Culturel</h5>
                    <p>
                        Un accent particulier est mis sur la maîtrise des langues (français, anglais, espagnol) et
                        la découverte des cultures du monde afin de former des citoyens ouverts, respectueux et
                        prêts à évoluer dans un monde globalisé.
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <!-- Programme 4 -->
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="program-card p-4 bg-white shadow rounded-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-paint-brush fa-2x text-danger me-3"></i>
                        <h5 class="fw-bold mb-0">Programme Artistique et Sportif</h5>
                    </div>
                    <p>
                        Peinture, théâtre, musique, sport collectif et individuel… nos élèves s’épanouissent à travers
                        des activités artistiques et sportives favorisant la créativité, la discipline et l’esprit d’équipe.
                    </p>
                </div>
            </div>

            <!-- Programme 5 -->
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="program-card p-4 bg-white shadow rounded-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-laptop-code fa-2x text-info me-3"></i>
                        <h5 class="fw-bold mb-0">Programme Numérique et Innovation</h5>
                    </div>
                    <p>
                        Initiation à la programmation, à la bureautique et à la citoyenneté numérique.
                        Les élèves apprennent à utiliser les outils technologiques pour créer, collaborer et innover
                        en toute responsabilité.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                    <h2 class="section-title mb-4">Contactez-nous</h2>
                    <p class="mb-4">Vous souhaitez en savoir plus sur nos programmes ou inscrire votre enfant ? Notre
                        équipe administrative est à votre disposition pour vous accompagner.</p>

                    <div class="d-flex align-items-start mb-4">
                        <div class="me-3 text-primary">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5>Adresse</h5>
                            <p class="mb-0">Quartier Scolaire, Parakou, Bénin</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="me-3 text-primary">
                            <i class="fas fa-phone-alt fa-2x"></i>
                        </div>
                        <div>
                            <h5>Téléphone</h5>
                            <p class="mb-0">+229 61 58 12 58</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="me-3 text-primary">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <div>
                            <h5>Email</h5>
                            <p class="mb-0">contact@ecole-excellence.bj</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="me-3 text-primary">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div>
                            <h5>Heures d'ouverture</h5>
                            <p class="mb-0">Lundi - Vendredi: 7h30 - 17h<br>Samedi: 8h - 12h</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="contact-form">
                        <h4 class="mb-4">Demande d'information</h4>
                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Nom du parent" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Votre email" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" placeholder="Votre téléphone" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-select">
                                    <option selected disabled>Niveau souhaité pour l'enfant</option>
                                    <option>Maternelle</option>
                                    <option>Primaire</option>
                                    <option>Collège</option>
                                    <option>Lycée</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="4" placeholder="Votre message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-1"></i> Envoyer la demande
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de Contact Programme -->
    <div class="modal fade custom-modal" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Demande d'information</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="#" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="programme" id="programmeField">
                        <div class="mb-3">
                            <label class="form-label">Nom du parent</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="telephone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nom de l'enfant</label>
                            <input type="text" name="enfant" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade custom-modal" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Connexion Sécurisée</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('handelogin') }}">
                        @csrf
                        @method('POST')

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="mb-3 position-relative">
                            <label for="email" class="form-label">Adresse Email</label>
                            <i class="fas fa-envelope input-icon"></i>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="votre.email@exemple.com">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Mot de Passe</label>
                            <i class="fas fa-lock input-icon"></i>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                autocomplete="current-password" placeholder="••••••••">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary login-btn w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-1"></i> Se Connecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Paiement Scolarité -->
    <div class="modal fade custom-modal" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card me-2"></i>Paiement de la Scolarité
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-4">Veuillez saisir le numéro Educ Master de votre enfant pour procéder au
                        paiement sécurisé.</p>

                    <form id="paiement-form" action="{{ route('paiement.parent.verify') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Numéro Educ Master</label>
                            <input type="text" class="form-control" placeholder="Ex: EDU-12345" required
                                name="educ_master" maxlength="13">
                            <small class="form-text text-muted">Format: EDU-XXXXX</small>
                        </div>
                        <button type="submit" class="btn btn-primary verify-btn w-100">
                            Vérifier et Poursuivre <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <p class="mt-4 text-center text-muted small">
                        <i class="fas fa-shield-alt me-1"></i> Paiement sécurisé via notre partenaire bancaire
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Emploi du Temps -->
    <div class="modal fade custom-modal" id="emploiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2"></i>Consulter l'Emploi du Temps
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="emploi-alert" class="alert alert-danger d-none" role="alert"></div>

                    <form id="emploi-form" action="{{ route('emploi_temps.verify') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="educ_master" class="form-label">Numéro Educ Master</label>
                            <input type="text" id="educ_master" name="educ_master" class="form-control"
                                placeholder="Ex: EDU-12345" required maxlength="13" autocomplete="off">
                            <small class="form-text text-muted">Entrez votre numéro Educ Master pour consulter votre
                                emploi du temps</small>
                        </div>
                        <button type="submit" class="btn btn-primary verify-btn w-100" id="btn-verify">
                            <i class="fas fa-search me-1"></i> Vérifier et Afficher
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Back to Top Button -->
    <a href="#" class="btn btn-primary btn-lg back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Back to top button
        const backToTopButton = document.getElementById('backToTop');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.style.display = 'block';
                backToTopButton.classList.add('animate__animated', 'animate__fadeIn');
                backToTopButton.classList.remove('animate__fadeOut');
            } else {
                backToTopButton.classList.add('animate__fadeOut');
                backToTopButton.classList.remove('animate__fadeIn');
            }
        });

        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Remplir le champ "programme" dans le modal
        document.getElementById('contactModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const programme = button.getAttribute('data-programme');
            document.getElementById('programmeField').value = programme;
        });

        // Gestion du formulaire de paiement
        document.getElementById('paiement-form').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        window.location.href = data.redirect;
                    }
                })
                .catch(() => alert('Erreur serveur.'));
        });

        // Gestion du formulaire emploi du temps
        document.addEventListener('DOMContentLoaded', function() {
            const emploiForm = document.getElementById('emploi-form');
            const alertBox = document.getElementById('emploi-alert');
            const button = document.getElementById('btn-verify');

            if (!emploiForm) return;

            emploiForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                button.disabled = true;
                button.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Vérification...';
                alertBox.classList.add('d-none');

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content');

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau (' + response.status + ')');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success && data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            alertBox.textContent = data.error || 'Une erreur est survenue.';
                            alertBox.classList.remove('d-none');
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-search me-1"></i>Vérifier et Afficher';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alertBox.textContent = 'Erreur de connexion au serveur. Veuillez réessayer.';
                        alertBox.classList.remove('d-none');
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-search me-1"></i>Vérifier et Afficher';
                    });
            });

            document.getElementById('emploiModal').addEventListener('hidden.bs.modal', function() {
                emploiForm.reset();
                alertBox.classList.add('d-none');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-search me-1"></i>Vérifier et Afficher';
            });
        });

        // Réouvrir automatiquement le modal de connexion en cas d'erreur
        @if ($errors->has('email') || $errors->has('password'))
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        @endif
    </script>
</body>

</html>
{{-- <div class="card card-hover h-100">
                        <div id="carousel1" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="d-block w-100" alt="École Maternelle">
                                </div>
                                <div class="carousel-item">
                                    <img src="https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="d-block w-100" alt="Activités Maternelle">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel1" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel1" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">École Maternelle</h5>
                            <p class="text-muted">
                                <i class="fas fa-child text-primary me-1"></i> 3 à 6 ans - Éveil et Développement
                            </p>
                            <ul class="program-features">
                                <li><i class="fas fa-check-circle"></i> Pédagogie Montessori</li>
                                <li><i class="fas fa-users"></i> Classes à effectif réduit</li>
                                <li><i class="fas fa-palette"></i> Ateliers créatifs quotidiens</li>
                                <li><i class="fas fa-language"></i> Initiation bilingue (Fr/Ang)</li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">150 000 FCFA/an</span>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#contactModal" data-programme="École Maternelle">
                                    <i class="fas fa-info-circle me-1"></i> En savoir plus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programme 2 -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card card-hover h-100">
                        <div id="carousel2" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="d-block w-100" alt="École Primaire">
                                </div>
                                <div class="carousel-item">
                                    <img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="d-block w-100" alt="Salle de classe">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel2" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel2" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">École Primaire</h5>
                            <p class="text-muted">
                                <i class="fas fa-book text-primary me-1"></i> CP à CM2 - Fondamentaux Solides
                            </p>
                            <ul class="program-features">
                                <li><i class="fas fa-check-circle"></i> Programme national enrichi</li>
                                <li><i class="fas fa-laptop"></i> Outils numériques intégrés</li>
                                <li><i class="fas fa-running"></i> Sport et activités parascolaires</li>
                                <li><i class="fas fa-award"></i> Suivi personnalisé des élèves</li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">200 000 FCFA/an</span>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#contactModal" data-programme="École Primaire">
                                    <i class="fas fa-info-circle me-1"></i> En savoir plus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programme 3 -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card card-hover h-100">
                        <div id="carousel3" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="d-block w-100" alt="Collège/Lycée">
                                </div>
                                <div class="carousel-item">
                                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="d-block w-100" alt="Laboratoires">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel3" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel3" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Collège & Lycée</h5>
                            <p class="text-muted">
                                <i class="fas fa-user-graduate text-primary me-1"></i> 6ème à Terminale - Excellence BAC
                            </p>
                            <ul class="program-features">
                                <li><i class="fas fa-check-circle"></i> Taux de réussite BAC 98%</li>
                                <li><i class="fas fa-flask"></i> Laboratoires équipés</li>
                                <li><i class="fas fa-chalkboard-teacher"></i> Enseignants qualifiés</li>
                                <li><i class="fas fa-globe"></i> Ouverture internationale</li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">300 000 FCFA/an</span>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#contactModal" data-programme="Collège & Lycée">
                                    <i class="fas fa-info-circle me-1"></i> En savoir plus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="fas fa-credit-card me-1"></i> Paiement de Scolarité
                </button>
            </div>
        </div> --}}
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container py-5">
        <h2 class="text-center mb-5 section-title" data-aos="fade-up">Pourquoi choisir notre école ?</h2>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h4>Enseignants Qualifiés</h4>
                    <p>Notre corps professoral est composé d'enseignants expérimentés et passionnés, formés aux méthodes
                        pédagogiques les plus innovantes.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h4>Technologies Modernes</h4>
                    <p>Salles de classe équipées de tableaux interactifs, laboratoires informatiques et accès aux
                        ressources numériques pour un apprentissage du 21ème siècle.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Sécurité Renforcée</h4>
                    <p>Établissement sécurisé 24/7 avec contrôle d'accès, vidéosurveillance et personnel de sécurité
                        qualifié pour la tranquillité des parents.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-bus"></i>
                    </div>
                    <h4>Transport Scolaire</h4>
                    <p>Service de ramassage scolaire couvrant toute la ville avec des véhicules modernes et conducteurs
                        expérimentés.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4>Restauration Équilibrée</h4>
                    <p>Cantine proposant des repas nutritifs et équilibrés préparés sur place avec des produits locaux
                        de qualité.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Suivi Personnalisé</h4>
                    <p>Effectifs réduits permettant un accompagnement individualisé de chaque élève selon ses besoins et
                        son rythme.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="testimonial-section py-5">
    <div class="container py-5">
        <h2 class="text-center mb-5 section-title text-white" data-aos="fade-up">Témoignages de Parents</h2>
        <div class="row">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Nos enfants sont scolarisés ici depuis 3 ans et nous sommes ravis de leurs progrès. L'équipe
                        pédagogique est très investie et à l'écoute."
                    </div>
                    <div class="client-info">
                        <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Parent"
                            class="client-img">
                        <div>
                            <h5 class="mb-0">Marie T.</h5>
                            <small>Maman de 2 enfants</small>
                        </div>
                    </div>
                </div>
            </div>




            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "L'excellence de cet établissement n'est plus à prouver. Mon fils a obtenu son BAC avec
                        mention Très Bien grâce à l'accompagnement exceptionnel des enseignants."
                    </div>
                    <div class="client-info">
                        <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="Parent" class="client-img">
                        <div>
                            <h5 class="mb-0">Yves K.</h5>
                            <small>Papa d'un bachelier</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Infrastructure moderne, personnel compétent et bienveillant. Notre fille s'épanouit
                        pleinement dans cet environnement stimulant et sécurisé."
                    </div>
                    <div class="client-info">
                        <img src="https://randomuser.me/api/portraits/women/28.jpg" alt="Parent"
                            class="client-img">
                        <div>
                            <h5 class="mb-0">Sophie A.</h5>
                            <small>Maman depuis 2020</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</section>



<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="footer-links">
                    <h5><i class="fas fa-graduation-cap me-2"></i>École d'Excellence</h5>
                    <p class="mt-3">Un établissement scolaire de référence au Bénin, offrant une éducation de qualité
                        dans un environnement moderne et sécurisé pour la réussite de chaque élève.</p>
                    <div class="social-icons mt-3">
                        <a href="https://www.facebook.com/nailou.sekaro" target="_blank"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/22961581258" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://instagram.com/votrecompte" target="_blank"><i
                                class="fab fa-instagram"></i></a>
                        <a href="https://twitter.com/votrecompte" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="footer-links">
                    <h5>Liens rapides</h5>
                    <ul>
                        <li><a href="#programmes">Nos Programmes</a></li>
                        <li><a href="#features">Nos Avantages</a></li>
                        <li><a href="#testimonials">Témoignages</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-links">
                    <h5>Contact</h5>
                    <ul>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Quartier Scolaire, Parakou, Bénin</li>
                        <li><i class="fas fa-phone-alt me-2"></i> +229 61 58 12 58</li>
                        <li><i class="fas fa-envelope me-2"></i> contact@ecole-excellence.bj</li>
                        <li><i class="fas fa-clock me-2"></i> Lun-Ven: 7h30-17h / Sam: 8h-12h</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-links">
                    <h5>Actualités</h5>
                    <p>Abonnez-vous pour recevoir nos actualités et événements scolaires.</p>
                    <form class="mt-3">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Votre email" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <p class="mb-0">&copy; 2025 École d'Excellence. Tous droits réservés. |
                <a href="#" class="text-white text-decoration-none">Politique de confidentialité</a> |
                <a href="#" class="text-white text-decoration-none">Mentions légales</a>
            </p>
        </div>
    </div>
</footer>
