<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>1010</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">


        <!-- Styles -->
        {{-- <link rel="stylesheet" href="{{ asset('public/css/style.css')}}"> --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

        <style>
            .home-logo{
                margin-top: 20%;
                width: 100%;
                height: auto;
            }
            .home-btn{
                padding: 15px 8px;
                border-radius: 0 !important;
            }
        </style>

    </head>
    <body>
        <div class="container">
            <div class="row ">
                <div class="col-md-12  d-flex flex-column justify-content-center align-items-center" style="height: 55vh">
                    {{-- <h1 class="text-light home-h1">1010 Ephesians Human Resources Inc.</h1> --}}
                    <img class="home-logo" src="{{ asset('images/1010-logo.jpg')}}" alt="">
                    <a href="{{ url('admin')}}"><button class="home-btn mt-5 btn btn-primary">Go To Admin Dashboard</button></a>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    </body>
</html>
