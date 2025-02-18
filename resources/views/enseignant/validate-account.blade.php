<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion d'école</title>
    <link rel="stylesheet" href="{{ asset ('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset ('assets/fontawesome.min.css') }}">
    <link rel="stylesheet" href="bootstrap-icons-1.4.1/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">


    <style>
        body {
            background: url({{ asset('assets/images/ecole.jpg') }} ) no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
        }

        .banniere {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            /* Ajusté pour rendre le formulaire plus petit */
            width: 100%;
        }

        .card-body {
            text-align: center;
        }

        .card-body p {
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 30px;
        }

        .form-floating label {
            padding-left: 10px;
        }

        .btn-success {
            font-weight: bold;
            width: 100%;
        }

        .logo {
            display: block;
            margin: 0 auto 20px auto;
            width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="banniere">
        <div class="container">
            <img src="{{ asset('assets/images/OIG1.jpg') }}" alt="Logo" class="logo">
            <div class="shadow-sm p-3 mb-5 bg-body rounded">
                <div class="card-body">
                    <form action="{{ route('validate.account.submit') }}" method="POST">
                        @csrf
                        @method('POST')

                        <p>Confirmation de compte</p>

                        {{-- {{ Hash::make('azerty') }} --}}

                        @if (session('error_message'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <input type="hidden" name="email" value="{{ $email }}">
                        {{-- <div class="mb-3 row">
                            <div class="form-floating col-sm-12">
                                <input type="email" class="form-control" id="floatingEmail"
                                    placeholder="name@example.com" required="required" name="email">
                                <label for="floatingEmail">&nbsp&nbspEmail</label>
                            </div>
                        </div> --}}

                        <div class="form-floating col-sm-12">
                            <input name="code" id="code" class="form-control"  placeholder="Code de validation "
                                required="required">
                            <label for="code">Code de validation </label>
                        </div>

                        <button type="submit" class="btn btn-dark mb-3 mt-3" style="width: 300px;">Se
                            connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
