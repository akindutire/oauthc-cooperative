<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <title>{! data('title') !}</title>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>

    <div class="container-fluid my-5  " style="background-image: url('https://source.unsplash.com/weekly?hospital?money');">
        <div class="container py-5 ">
            <div class="col-lg-4 offset-lg-8 offset-md-1 col-md-10 col-sm-12  bg-light shadow-sm my-5 border rounded">
                <form action="" class="form-group">
                    <div class="form-row">
                        <label for="email" class="col-form-label">Email</label>
                        <div class="input-group mb-2 mr-sm-2">
                            <input required type="email" name="email" id="" class="form-control">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fa text-info fa-at"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="password" class="col-form-label ">Password</label>
                        <div class="input-group mb-2 mr-sm-2">
                            <input required type="password" name="password" id="" class="form-control">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fa text-info fa-key"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="password_2" class="col-form-label ">Confirm password</label>
                        <div class="input-group mb-2 mr-sm-2">
                            <input required type="password" name="password_2" id="" class="form-control ">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fa text-info fa-key"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="IPPI_no" class="col-form-label ">IPPIS no</label>
                        <div class="input-group mb-2 mr-sm-2">
                            <input required type="text" class="form-control ">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fa fa-user-secret text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-center">
                        <button type="submit" class="btn btn-info  my-1">Sign Up</button>
                    </p>
                </form>
            </div>
        </div> 
    </div>
</body>

</html>