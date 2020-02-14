@extend('CoopTemplate')

@build('title')
Login
@endbuild

@build('dynamicPageContent')
<div class="container-fluid "
    style="background-image: url('https://source.unsplash.com/daily?piggybank'); min-height: 400px;">
    <div class="row py-5">
        <div class="card offset-lg-6 col-lg-4 col-sm-12 col-md-12 shadow-sm bg-light pt-5">
            <form action="{! route('') !}" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <p class="text-center">
                    <button type="submit" class="btn btn-sm btn-primary">Login</button>
                </p>
                <p class="text-center">

                </p>
            </form>
            <div class="card-body text-center ">
                <a href="{! route('staff/registration') !}" class="card-link">Create An Account</a>
            </div>

        </div>
    </div>
</div>
@endbuild