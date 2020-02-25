@extend('CoopTemplate')

@build('title')
    Staff Registration Feedback
@endbuild

@build('dynamicPageContent')

    {!! $Message = data('message') !!}
    {!! $Status = data('status') !!}

<div class="container-fluid" style="background-image: url('https://source.unsplash.com/daily?piggybank'); width:auto; height:auto;">
    <div class="container py-5  ">
        <div class="col-lg-4 offset-lg-4 offset-md-4 col-md-10 col-sm-12  bg-light shadow-sm my-5 border rounded">
            @if($Status == true)
                <p class="text-center display-4 text-primary"> <i class="fa fa-check"></i> {! $Message !}</p>
            @else
                <p class="text-center display-4 text-danger"> <i class="fa fa-times"></i> {! $Message !}</p>
            @endif
        </div>
    </div>

</div>
@endbuild
