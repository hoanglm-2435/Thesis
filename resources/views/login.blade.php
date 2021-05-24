<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <title>Market Analysis</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-app.png') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/vendor/animate/animate.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/vendor/css-hamburgers/hamburgers.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/vendor/animsition/css/animsition.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/vendor/select2/select2.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
          href="{{ asset('bower_components/auth-template/vendor/daterangepicker/daterangepicker.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/auth-template/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/auth-template/css/main.css') }}">
    <!--===============================================================================================-->
</head>
<body>

<div class="container-login100"
     style="background-image: url({{ asset('bower_components/auth-template/images/bg_img.jpg') }});">
    <div class="wrap-login100 p-l-55 p-r-55 p-t-50 p-b-20">
        @if (count($errors) > 0)
            <ul>
                @foreach($errors->all() as $error)
                    <li class="text-danger"> {{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <form action="{{ route('login') }}" method="POST" class="login100-form validate-form">
            {{ csrf_field() }}
            <span class="login100-form-title p-b-37">
                Sign In
            </span>

            <div class="wrap-input100 validate-input m-b-20" data-validate="Enter your email">
                <input class="input100" type="email" name="email" placeholder="Your email">
                <span class="focus-input100"></span>
            </div>

            <div class="wrap-input100 validate-input m-b-25" data-validate="Enter password">
                <input class="input100" type="password" name="password" placeholder="Password">
                <span class="focus-input100"></span>
            </div>

            <div class="d-flex justify-content-center align-items-center wrap-input100 validate-input m-b-25" data-validate="Enter password">
                <input size="md" type="checkbox" id="remember" name="remember_me">
                <label class="ml-2 mt-2" for="remember">
                    Remember me
                </label>
            </div>

            <div class="container-login100-form-btn">
                <button class="login100-form-btn">
                    Sign In
                </button>
            </div>
        </form>

        <div class="text-center p-t-30 p-b-20">
            <span class="txt1">
                Do not have an account?
            </span>
        </div>

        <div class="text-center">
            <a href="{{ route('register') }}" class="txt2 hov1">
                Sign Up
            </a>
        </div>
    </div>
</div>


<div id="dropDownSelect1"></div>

<script src="{{ asset('bower_components/auth-template/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('bower_components/auth-template/vendor/animsition/js/animsition.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('bower_components/auth-template/vendor/bootstrap/js/popper.js') }}"></script>
<script src="{{ asset('bower_components/auth-template/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('bower_components/auth-template/vendor/select2/select2.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('bower_components/auth-template/vendor/daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('bower_components/auth-template/vendor/daterangepicker/daterangepicker.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('bower_components/auth-template/vendor/countdowntime/countdowntime.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ asset('bower_components/auth-template/js/main.js') }}"></script>

</body>
</html>
