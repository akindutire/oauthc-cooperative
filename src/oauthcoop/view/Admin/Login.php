@extend('CoopTemplate')

@build('title')
    data('title')
@endbuild

@build('dynamic-page-content')
<div class="container">
        <div class="col-md-6" style="width: 600px; margin-left: 250px; margin-top: 50px;">
            <form class="form-horizontal" style="margin-left: 50px;">
                <div class="jumbotron">
                    <h1 style="margin-left:100px;">Admin Login</h1>
                    <div class="form-group input-group">
                        <span style="font-size: 25px; color: dimgray; margin-right: 5px;">
                            <i class="fas fa-user-tie"></i>
                        </span>
                        <input type="text" placeholder="Enter Username" required="" class="form-control" style="border-radius: 50px;";>
                    </div>
                    <div class="form-group input-group">
                        <span style="font-size: 25px; color: dimgray; margin-right: 5px;">
                            <i class="fas fa-key"></i>
                        </span>
                         <input type="password" placeholder="Enter Password" pattern=".{2,3}" required title="5 to 8 character" class="form-control" style="border-radius: 50px;";>
                    </div>
                    <div class="form-group">
                        <div class="col-se-offset-2 col-se-18">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox">Remember me
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary btn-lg" style="width: 430px;">Login</button>
                    </div>
                    <div class="col-12 forgot">
                        <a href="#" style="margin-left:250px;">Forgot Password?</a>
                    </div>
                </div>
            </form>
        </div>
</div>
@endbuild