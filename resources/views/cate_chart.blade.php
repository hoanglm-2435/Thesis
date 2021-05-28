@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">
                            Analysis Chart
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('shopee.cate') }}">Shopee Category</a></li>
                            <li class="breadcrumb-item active">Chart</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <span class="font-weight-bold"><a href="{{ $cate->url }}"> {{ $cate->name }}</a></span>
                                </h3>
                            </div>
                            <div class="card-body" style="min-height: 500px">
                                <div class="chart">
                                    <canvas id="barChart" width="400px" height="180px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="{{ mix('js/category-chart.js') }}"></script>
@endsection
