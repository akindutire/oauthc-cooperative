@extend('CoopTemplate')
@build('title')
    OAUTHCoop
@endbuild

@build('dynamicPageContent')
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner ">
        <div class="carousel-item active max-h">
            <img class="d-block w-100" src="https://source.unsplash.com/daily?christian" alt="First slide">
            <div class="carousel-caption d-none d-md-block text-welcome">
                <h1 class="display-2">WELCOME</h1>
                <div class="container-fluid">
                    <div class="row display-3">
                        <div class="col-12 tc">To</div>
                    </div>
                    <div class="row display-4">
                        <div class="col-12 tc">
                        OAUTHC CHRISTIAN COOPERATIVE SOCIETY
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item max-h">
            <img class="d-block w-100 " src="https://source.unsplash.com/daily?money%20interest" alt="Second slide">
            <div class="carousel-caption d-none d-md-block">
                <div class="row">
                    <div class="col-4 min-height-2">
                        <div class="card" style="width: 18rem;">
                            <img class="card-img-top" src="https://source.unsplash.com/daily?money%20interest" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title text-primary">MONTLY SAVING</h5>
                                <p class="card-text text-info">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 min-height-2">
                        <div class="card" style="width: 18rem;">
                            <img class="card-img-top" src="https://source.unsplash.com/daily?double" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title text-primary">YEARLY REDRAWAL</h5>
                                <p class="card-text text-info">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 min-height-2 ">
                        <div class="card" style="width: 18rem;">
                            <img class="card-img-top" src="https://source.unsplash.com/daily?loan" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title text-primary">LOAN</h5>
                                <p class="card-text text-info">LOAN up to Double the amount of your saving</p>
                            </div>
                        </div>
                    </div>

                </div>
                <h5>...</h5>
                <p>...</p>
            </div>
        </div>
        <div class="carousel-item max-h">
            <img class="d-block w-100" src="https://source.unsplash.com/daily?health" alt="Third slide">
            <div class="carousel-caption d-none d-md-block">
                <h5>hello</h5>
                <p>...</p>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>
@endbuild
