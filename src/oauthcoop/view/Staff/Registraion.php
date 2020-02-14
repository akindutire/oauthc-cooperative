@extend('CoopTemplate')

@build('title')
{! data('title') !}
@endbuild

@build('dynamicPageContent')

<div class="container-fluid"
    style="background-image: url('https://source.unsplash.com/daily?piggybank'); width:auto; height:auto;">
    <div class="container py-5  ">
        <div class="col-lg-4 offset-lg-8 offset-md-1 col-md-10 col-sm-12  bg-light shadow-sm my-5 border rounded">
            <form action="{! route('form/reg-staff') !}" method="post">
                <div class="form-row">
                    <label for="email" class="col-form-label">Email</label>
                    <div class="input-group mb-2 mr-sm-2">
                        <input required type="email" name="email" id="" class="form-control">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fa text-primary fa-at"></i>
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
                                <i class="fa text-primary fa-key"></i>
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
                                <i class="fa text-primary fa-key"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <label for="IPPI_no" class="col-form-label ">IPPIS no</label>
                    <div class="input-group mb-2 mr-sm-2">
                        <input type="text" name="ippis_no" class="form-control ">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fa fa-user-secret text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center">
                    <button type="submit" class="btn btn-primary  my-1">Sign Up</button>
                </p>
            </form>
        </div>
    </div>
</div>

@endbuild
