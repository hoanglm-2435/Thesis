@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Shopee Analysis</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Shopee Category</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header row align-items-center d-flex">
                                <h3 class="card-title">Market analysis on the Shopee e-commerce platform is from <b>{{ $analysisAt ?? '' }}</b></h3>
                                <a class="ml-auto" href="{{ route('market-share.show-chart') }}">
                                    <button
                                        title="Analysis Chart"
                                        class="btn btn-default"
                                    >
                                        <i class="fas fa-chart-pie"> Market Share Analysis</i>
                                    </button>
                                </a>
                            </div>

                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Shop Count</th>
                                        <th>Product Count</th>
                                        <th>Sold</th>
                                        <th>Revenue (VND)</th>
                                        <th>Updated At</th>
                                        <th>Shop List</th>
                                        <th>Analysis Chart</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Shop Count</th>
                                        <th>Product Count</th>
                                        <th>Sold</th>
                                        <th>Revenue (VND)</th>
                                        <th>Updated At</th>
                                        <th>Shop List</th>
                                        <th>Analysis Chart</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="{{ mix('js/shopee-cate.js') }}"></script>
@endsection
