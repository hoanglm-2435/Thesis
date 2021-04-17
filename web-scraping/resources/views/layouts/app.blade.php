<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @routes()

    <title>Market Analysis</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-app.png') }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('bower_components/admin-lte/plugins/fontawesome-free/css/all.min.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('bower_components/admin-lte/dist/css/adminlte.min.css') }}">

    <!-- Ion Slider -->
    <link rel="stylesheet" href="{{ asset('bower_components/admin-lte/plugins/ion-rangeslider/css/ion.rangeSlider.min.css') }}">
    <!-- bootstrap slider -->
    <link rel="stylesheet" href="{{ asset('bower_components/admin-lte/plugins/bootstrap-slider/css/bootstrap-slider.min.css') }}">
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Home</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Navbar Search -->
            <li class="nav-item">
                <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                    <i class="fas fa-search"></i>
                </a>
                <div class="navbar-search-block">
                    <form class="form-inline">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-navbar" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <img src="{{ asset('images/logo-app.png') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Market Analysis</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- SidebarSearch Form -->
            <div class="form-inline mt-2">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a
                            href="{{ route('shopee') }}"
                            class="nav-link {{ str_contains(request()->route()->uri(), 'shopee-analysis') ? 'active' : ''}}"
                        >
                            <i class="mx-1 fas fa-chart-line"></i>
                            <p class="ml-1">
                                Shopee Analysis
                                <span class="right badge badge-danger">New</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            href="{{ route('shop-offline') }}"
                            class="nav-link {{ str_contains(request()->route()->uri(), 'shop-offline') ? 'active' : ''}}"
                        >
                            <i class="mx-2 fas fa-map-marker-alt"></i>
                            <p class="ml-0">
                                Shop Offline
                                <span class="right badge badge-danger">New</span>
                            </p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Main content -->
    @yield('content')
    <!-- /.content -->

    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Made by Le Minh Hoang - 17020764</b>
        </div>
        <strong>Graduation thesis for semester 2 academic year 2020-2021.</strong>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('bower_components/admin-lte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('bower_components/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('bower_components/admin-lte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('bower_components/admin-lte/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('bower_components/admin-lte/dist/js/demo.js') }}"></script>

<!-- Ion Slider -->
<script src="{{ asset('bower_components/admin-lte/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
<!-- Bootstrap slider -->
<script src="{{ asset('bower_components/admin-lte/plugins/bootstrap-slider/bootstrap-slider.min.js') }}"></script>

@yield('script')
</body>
</html>
