<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@section('title')</title>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
            integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/fe8c9c3ce4.js" crossorigin="anonymous"></script>
    <style>
        {!! $generalCss = asset('resource/css/general.css') !!}
        @import url(  {!  $generalCss  !}  );
    </style>
    {!! $LoginNotifierService = new src\oauthcoop\service\LoginNotifier !!}
    {!! $isLoggedIn = $LoginNotifierService->isLoggedIn() !!}
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light std-bg-primary">
        <button class="navbar-toggler text-light" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="{! route('') !}">OAUTHC</a>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav menu">
                <li class="nav-item"><a href="#" class="nav-link text-light">APPOINTMENTS</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-light">PATIENTS</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-light">CHATS</a></li>
                <li class="nav-item"><a href="#" class="nav-link text-light">DOCTORS</a></li>
            </ul>
        </div>
        <div class="justify-content-end collapse navbar-collapse mr-auto menu">
            <a href="#" class="nav-link text-light"><i class="fas fa-search"></i></i></a>
            <a href="#" class="nav-link text-light"><i class="fas fa-bell"></i></a>
            <a href="#" class="nav-link text-light"><i class="fas fa-user"></i></a>
            <a class="btn btn-outline-light my-2 my-sm-0 btn-sm text-light">Sign in</a>
        </div>
    </nav>
</header>
<main class="min-height-l">

    @section('dynamicPageContent')

</main>
<footer class="std-bg-primary pt-4">

<!--    <hr class="container my-4" style="background: #9fa8da  ;">-->

    <div class="row container text-center">

        <div class="col-md-12 col-lg-4 col-sm-12">
            <div class="row">
                <div class="col-12 text-light"><h5 class="std-bt py-1">Fast links</h5></div>
                <div class="col-12">
                    <ul class="list-group list-group-flush std-bg-primary text-light">
                        <li class="list-group-item"><a class="text-light" href="#!">Contact</a></li>
                        <li class="list-group-item"><a href="#!">About Us</a></li>
                        <li class="list-group-item"><a href="#!">Terms & Condition</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="col-md-12 col-lg-4 col-sm-12">
            <div class="row">
                <div class="col-12 text-light"><h5 class="std-bt py-1">Social Media</h5></div>
                <div class="col-12">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="#"><span class="badge badge-primary badge-pill mr-1"><i
                                            class="fab fa-facebook-f"></i></span>Facebook</a></li>
                        <li class="list-group-item"><a href="#"><span class="badge badge-primary badge-pill mr-1"><i
                                            class="fab fa-twitter"></i></span>Twitter</a></li>
                        <li class="list-group-item"><a href="#"><span class="badge badge-primary badge-pill mr-1"><i
                                            class="fab fa-instagram"></i></span>Instagram</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="col-md-12 col-lg-4 col-sm-12">
            <div class="row">
                <div class="col-12 text-light">
                    <h5 class="std-bt py-1">Help & Support</h5>
                </div>
                <div class="col-12">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="#!">Address: +234 3442445</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#!">Phone: +234 3442445</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#!">Email: we@fg.com</a>
                        </li>
                    </ul>

                </div>
            </div>

        </div>

    </div>

    <div class="row">
        <p></p>
    </div>

    <div class="row" style="background: #303f9f ;">
        <p class="col-12 d-block text-center text-light pt-3">&copy; OAUTHC {! date('Y') !}</p>
    </div>
</footer>
</body>
</html>
