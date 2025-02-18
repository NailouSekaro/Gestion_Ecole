<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gestion d'école</title>

    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel-2/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel-2/owl.theme.default.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    {{-- <link rel="stylesheet" href="style.css"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

</head>

<body>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Remplir le formulaire pour changer votre mot de passe </h4>
                    @if (session('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error_message') }}
                        </div>
                    @endif
                    <p class="card-description"> Avec les éléments correspondants </p>
                    <form class="forms-sample" method="POST" action="{{ route('updatePassword') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="form-group">
                            <label for="exampleInputName1">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="exampleInputName1"
                                placeholder="Nouveau mot de passe" required="required" name="new-password"
                                value="{{ old('password') }}">
                        </div>
                        @error('new-password')
                            <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                        @enderror


                        <div class="form-group">
                            <label for="exampleInputName1">Confirmer mot de passe</label>
                            <input type="password" class="form-control" id="exampleInputName1"
                                placeholder="Confirmer mot de passe" required="required" name="new-password_confirmation"
                                value="{{ old('password') }}">
                        </div>
                        @error('new-password_confirmation')
                            <div style="color:rgba(255, 0, 0, 0.858)"> {{ $message }}</div>
                        @enderror



                        <button type="submit" class="btn btn-primary mr-2">Valider</button>
                        {{-- <button class="btn btn-dark" type="reset">Annuler</button> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/vendors/owl-carousel-2/owl.carousel.min.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <!-- End custom js for this page -->

    </script>
    {{-- <script src="{{ asset('assets/js/bootstrap.bundle.min.js' )}}"></script> --}}
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

</body>

</html>
