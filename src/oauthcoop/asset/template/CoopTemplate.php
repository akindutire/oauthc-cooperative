<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@section('title')</title>
    <link rel="stylesheet" href="bootstrap.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/fe8c9c3ce4.js" crossorigin="anonymous"></script>   
    <style>
        {!! $generalCss = asset('resource/css/general.css') !!}
        @import url({! $generalCss !});
    </style>
    {!! $LoginNotifierService = new src\oauthcoop\service\LoginNotifier !!}
    {!! $isLoggedIn = $LoginNotifierService->isLoggedIn() !!}
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-info ">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a  class="navbar-brand"href="#">OAUTHC</a>
            <div class="collapse navbar-collapse justify-content-between" id = "navbarNav" >
                        <ul class="navbar-nav">
                            <li class="nav-item"><a href="#" class="nav-link text-light">APPOINTMENTS</a></li>
                            <li class="nav-item"><a href="#" class="nav-link text-light">PATIENTS</a></li>
                            <li class="nav-item"><a href="#" class="nav-link text-light">CHATS</a></li>
                            <li class="nav-item"><a href="#" class="nav-link text-light">DOCTORS</a></li>
                        </ul>
             </div>
             <form class="form-inline">
                    <a href="#" class="nav-link text-light"><i class="fas fa-search"></i></i></a>
                    <a href="#" class="nav-link text-light"><i class="fas fa-bell"></i></a>
                    <a href="#" class="nav-link text-light"><i class="fas fa-user"></i></a>
                    <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Sign in</button>
            </form>      
        </nav>
    </header> 
    <main class="min-height-l">
        
        @section('dynamicPageContent')

    </main>
    <footer class ="">
        <div class="row text-center">
                <div class="col-md-12 col-lg-3 col-sm-12 ">
                    <div class="wrapper">
                        <p>
                            <a href="#!">OAUTHC</a>
                        </p>
                    </div>
                </div>
                <div class="col-md-12 col-lg-3 col-sm-12 ">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="#!">Contact</a></li>
                        <li class="list-group-item"><a href="#!">About Us</a></li>
                        <li class="list-group-item"><a href="#!">Terms & Condition</a></li>          
                    </ul>
                </div>
                <div class="col-md-12 col-lg-3 col-sm-12 ">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="#"><span class="badge badge-primary badge-pill"><i class="fab fa-facebook-f"></i></span>Facebook</a></li>
                        <li class="list-group-item"><a href="#"><span class="badge badge-primary badge-pill"><i class="fab fa-twitter"></i></span>Twitter</a></li>
                        <li class="list-group-item"><a href="#"><span class="badge badge-primary badge-pill"><i class="fab fa-instagram"></i></span>Instagram</a></li>
                    </ul>
                </div>
                <div class="col-md-12 col-lg-3 col-sm-12 ">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="#!">Address</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#!">Phone Number</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#!">Email</a>
                        </li>
                    </ul>
                </div>
            
        </div>
    </footer>
</body>
</html>
