@extend('CoopTemplate')

@build('title')
    Login
@endbuild

@build('dynamic-page-content')
    <div class="row">
        <form action="{! route('') !}" method="POST" class="col-4 offset-4">
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
        </form>
    </div>
@endbuild
