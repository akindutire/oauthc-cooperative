<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@section('title')</title>
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/fe8c9c3ce4.js" crossorigin="anonymous"></script>   
    <script src="js/bootstrap.min.js"></script>
    <style>
        {!! $generalCss = asset('resource/css/general.css') !!}
        @import url({! $generalCss !});
    </style>
    {!! $LoginNotifierService = new src\oauthcoop\service\LoginNotifier !!}
    {!! $isLoggedIn = LoginNotifierService->isisLoggedIn() !!}
</head>
<body>
    <header>
        <nav class="navbar navbar-default bg-primary">
             <div class="container">
                 @if($isLoggedIn)
                    <ul class="nav navbar-nav">
                        <li><a href="#">OAUTHC</a></li>
                        <li><a href="#">APPOINTMENTS</a></li>
                        <li><a href="#">PATIENTS</a></li>
                        <li><a href="#">CHATS</a></li>
                        <li><a href="#">DOCTORS</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#"><i class="fas fa-search"></i></i></a></li>
                        <li><a href="#"><i class="fas fa-bell"></i></a></li>
                        <li><a href="#"><i class="fas fa-user"></i></a></li>
                        <li><a href="#">SIGN OUT</a></li>
                    </ul>
                @else
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#"><i class="fas fa-search"></i></i></a></li>
                        <li><a href="#"><i class="fas fa-bell"></i></a></li>
                        <li><a href="#"><i class="fas fa-user"></i></a></li>
                        <li><a href="#">SIGN OUT</a></li>
                    </ul>
                @endif
            </div>
        </nav>
    </header>
    <main class="min-height-l">
        
        @section('dynamic-page-content')

    </main>
    <footer class="page-footer font-small indigo">
        <div class="container row">
            <div class="row">
                <div class="col-md-3 mx-auto">
                    <ul class="list-unstyled">
                        <li>
                            <a href="#!">OAUTHC</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3 mx-auto">
                    <ul class="list-unstyled">
                        <li>
                            <a href="#!">Contact</a>
                        </li>
                        <li>
                            <a href="#!">About Us</a>
                        </li>
                        <li>
                            <a href="#!">Terms & Condition</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3 mx-auto">
                    <ul class="list-unstyled">
                        <li><a href="#"><i class="fab fa-facebook-f"></i>Facebook</a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i>Twitter</a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i>Instagram</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mx-auto">
                    <ul class="list-unstyled">
                        <li>
                            <a href="#!">Address</a>
                        </li>
                        <li>
                            <a href="#!">Phone Number</a>
                        </li>
                        <li>
                            <a href="#!">Email</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
