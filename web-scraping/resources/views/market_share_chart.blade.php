@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="text-black">
                            Analysis of market share of the industry on Shopee in {{ date('Y') }}
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('shopee.cate') }}">Shopee Category</a></li>
                            <li class="breadcrumb-item active">Market Share Chart</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Shop Chart</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="shopChart" style="height: 450px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="card card-cyan">
                            <div class="card-header">
                                <h3 class="card-title">Product Chart</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="productChart" style="height: 450px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Sold Chart</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="soldChart" style="height: 450px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="card card-lightblue">
                            <div class="card-header">
                                <h3 class="card-title">Revenue Chart</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="revenueChart" style="height: 450px; max-width: 100%;"></canvas>
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
    <script src="{{ mix('js/market-share-chart.js') }}"></script>
@endsection
