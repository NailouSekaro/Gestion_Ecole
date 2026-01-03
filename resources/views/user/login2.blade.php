<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestion d'École - Connexion Sécurisée</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fontawesome.min.css">
    <link rel="stylesheet" href="bootstrap-icons-1.4.1/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">


    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary-color: #4f46e5;
            /* Bleu indigo moderne pour l'éducation */
            --secondary-color: #06b6d4;
            /* Cyan pour les accents */
            --gradient-bg: linear-gradient(135deg, rgba(79, 70, 229, 0.9) 0%, rgba(6, 182, 212, 0.9) 100%);
            --card-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            --text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        body {
            background: url('assets/images/ecole.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.4), rgba(79, 70, 229, 0.2));
            z-index: 1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            max-width: 450px;
            width: 100%;
            padding: 40px;
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            display: block;
            margin: 0 auto 30px;
            width: 120px;
            height: auto;
            filter: drop-shadow(var(--text-shadow));
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .card-title {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 40px;
            text-shadow: var(--text-shadow);
        }

        .form-floating {
            position: relative;
            margin-bottom: 20px;
        }

        .form-floating input {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px 15px 15px 50px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-floating input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
            background: white;
        }

        .form-floating label {
            padding-left: 15px;
            color: #6b7280;
            font-weight: 500;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            z-index: 3;
        }

        .btn-login {
            background: var(--gradient-bg);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            padding: 15px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
        }

        .payment-link {
            text-align: center;
            margin-top: 20px;
        }

        .payment-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .payment-link a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .payment-link i {
            margin-right: 8px;
        }

        /* Modal pour paiement */
        .payment-modal .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
        }

        .payment-modal .modal-header {
            background: var(--gradient-bg);
            color: white;
            border-radius: 20px 20px 0 0;
            border: none;
        }

        .payment-modal .modal-title {
            font-weight: 600;
        }

        .payment-modal .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 2px solid #e5e7eb;
        }

        .payment-modal .btn-pay {
            background: var(--secondary-color);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .payment-modal .btn-pay:hover {
            background: #0891b2;
            transform: translateY(-1px);
        }

        /* Alertes stylées */
        .alert {
            border-radius: 10px;
            border: none;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #059669;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                margin: 20px;
                padding: 30px 20px;
            }
        }

        /* Effet de particules subtil pour surprise (optionnel, via CSS) */
        .bg-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Particules d'arrière-plan pour effet wow (générées via JS) -->
        <div class="bg-particles" id="particles"></div>

        <div class="login-card">
            <img src="assets/images/OIG1.jpg" alt="Logo de l'École" class="logo">
            <h2 class="card-title">Bienvenue dans votre Espace Sécurisé</h2>

            <form action="{{ route('handelogin') }}" method="POST">
                @csrf
                @method('POST')

                @if (session('error_message'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success_message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="form-floating">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" class="form-control" id="floatingEmail" placeholder="name@example.com"
                        required name="email">
                    <label for="floatingEmail">Adresse Email</label>
                </div>

                <div class="form-floating">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password" required
                        name="password">
                    <label for="floatingPassword">Mot de Passe</label>
                </div>

                <button type="submit" class="btn btn-primary btn-login">Se Connecter <i
                        class="bi bi-arrow-right ms-2"></i></button>
            </form>

            <div class="payment-link">
                <a href="#" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="bi bi-credit-card"></i> Paiement de Scolarité (pour les parents)
                </a>
            </div>
            <div class="payment-link">
                <a href="#" data-bs-toggle="modal" data-bs-target="#emploiModal">
                    <i class="bi bi-calendar"></i> Consulter Emploi du Temps (Élèves)
                </a>
            </div>
        </div>
    </div>

    <!-- Modal pour Paiement -->
    <div class="modal fade payment-modal" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="bi bi-credit-card me-2"></i>Paiement de la Scolarité
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-4">Veuillez saisir le numéro éduc master de votre enfant pour procéder au paiement
                        sécurisé.</p>
                    <!-- Dans le modal -->
                    <form id="paiement-form" action="{{ route('paiement.parent.verify') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Numéro éduc master (ex: EDU-12345)"
                                required name="educ_master" maxlength="13">
                        </div>
                        <button type="submit" class="btn btn-pay">Vérifier et Poursuivre <i
                                class="bi bi-arrow-right ms-2"></i></button>
                    </form>

                    <script>
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
                                        alert(data.error); // Ou affiche dans un div
                                    } else {
                                        window.location.href = data.redirect;
                                    }
                                })
                                .catch(() => alert('Erreur serveur.'));
                        });
                    </script>
                    <p class="mt-3 text-muted small">Paiement sécurisé via notre partenaire. Vous serez redirigé vers la
                        page de paiement.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour Consulter l'Emploi du Temps -->
    <div class="modal fade" id="emploiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emploiModalLabel">
                        <i class="bi bi-calendar me-2"></i>Consulter l'Emploi du Temps
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Zone d'alerte pour les messages -->
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
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-verify">
                                <i class="bi bi-search me-2"></i>Vérifier et Afficher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emploiForm = document.getElementById('emploi-form');
            const alertBox = document.getElementById('emploi-alert');
            const button = document.getElementById('btn-verify');

            if (!emploiForm) return;

            emploiForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Désactiver le bouton et afficher un loader
                button.disabled = true;
                button.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Vérification...';
                alertBox.classList.add('d-none');

                // Log pour debug
                console.log('URL:', this.action);
                console.log('FormData:', Object.fromEntries(formData));

                // Récupération du token CSRF dans la balise meta
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content');

                // Envoi de la requête
                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken // 🔒 Protection CSRF Laravel
                        }
                    })
                    .then(response => {
                        // Si Laravel renvoie une erreur HTTP
                        if (!response.ok) {
                            throw new Error('Erreur réseau (' + response.status + ')');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Réponse serveur:', data);

                        if (data.success && data.redirect) {
                            // ✅ Redirection vers l'emploi du temps
                            window.location.href = data.redirect;
                        } else {
                            // ❌ Afficher l'erreur renvoyée
                            alertBox.textContent = data.error || 'Une erreur est survenue.';
                            alertBox.classList.remove('d-none');

                            // Réactiver le bouton
                            button.disabled = false;
                            button.innerHTML = '<i class="bi bi-search me-2"></i>Vérifier et Afficher';
                        }
                    })
                    .catch(error => {
                        // ❌ Gestion des erreurs réseau ou de conversion JSON
                        console.error('Erreur:', error);
                        alertBox.textContent = 'Erreur de connexion au serveur. Veuillez réessayer.';
                        alertBox.classList.remove('d-none');

                        // Réactiver le bouton
                        button.disabled = false;
                        button.innerHTML = '<i class="bi bi-search me-2"></i>Vérifier et Afficher';
                    });
            });

            // Réinitialiser le formulaire quand le modal est fermé
            document.getElementById('emploiModal').addEventListener('hidden.bs.modal', function() {
                emploiForm.reset();
                alertBox.classList.add('d-none');
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-search me-2"></i>Vérifier et Afficher';
            });
        });
    </script>


    {{-- <script>
document.getElementById('emploi-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const button = document.getElementById('btn-verify');
    const alertBox = document.getElementById('emploi-alert');
    const formData = new FormData(this);

    // Désactiver le bouton et afficher un loader
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Vérification...';
    alertBox.classList.add('d-none');

    // Log pour debug
    console.log('URL:', this.action);
    console.log('FormData:', Object.fromEntries(formData));

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.redirect) {
            // Redirection vers l'emploi du temps
            window.location.href = data.redirect;
        } else {
            // Afficher l'erreur
            alertBox.textContent = data.error || 'Une erreur est survenue';
            alertBox.classList.remove('d-none');

            // Réactiver le bouton
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-search me-2"></i>Vérifier et Afficher';
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alertBox.textContent = 'Erreur de connexion au serveur. Veuillez réessayer.';
        alertBox.classList.remove('d-none');

        // Réactiver le bouton
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-search me-2"></i>Vérifier et Afficher';
    });
});

// Réinitialiser le formulaire quand le modal est fermé
document.getElementById('emploiModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('emploi-form').reset();
    document.getElementById('emploi-alert').classList.add('d-none');
});
</script> --}}




    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Génération de particules pour effet surprise (subtil)
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 10; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.width = Math.random() * 4 + 2 + 'px';
                particle.style.height = particle.style.width;
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                particlesContainer.appendChild(particle);
            }
        }
        createParticles();
    </script>
</body>

</html>
