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
            <ul style="padding-bottom: 1rem">
                @foreach ($errors->all() as $error)
                    <li class="text-danger"> {{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <form action="{{ route('register') }}" method="post" class="login100-form validate-form">
            {{ csrf_field() }}
            <span class="login100-form-title p-b-37">
                Sign Up
            </span>

            <div class="login-form">
                <div class="wrap-input100 validate-input m-b-20" data-validate="Enter username">
                    <input class="input100" type="text" name="name" placeholder="Username">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-20" data-validate="Enter email">
                    <input class="input100" type="email" name="email" placeholder="Email">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-25" data-validate="Enter password">
                    <input class="input100" type="password" name="password" placeholder="Password">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-25" data-validate="Enter password again">
                    <input class="input100" type="password" name="password_confirmation"
                           placeholder="Password Confirmation">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-20" data-validate="Enter address">
                    <input class="input100" type="text" name="address" placeholder="Address">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-20" data-validate="Enter phone number">
                    <input class="input100" type="text" name="phone_number" placeholder="Phone">
                    <span class="focus-input100"></span>
                </div>
            </div>

            <div class="container-login100-form-btn">
                <button id="register" type="submit" class="login100-form-btn">
                    Sign Up
                </button>
            </div>
        </form>

        <div class="text-center p-t-30 p-b-20">
            <span class="txt1">
                You already have an account!
            </span>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="txt2 hov1">
                Return Sign In
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
