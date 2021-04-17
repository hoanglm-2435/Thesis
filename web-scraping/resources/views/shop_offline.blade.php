@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Shop Offline</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Shop Offline</li>
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
                            <div class="card-header">
                                <h3 class="card-title">Shop locations on google map</h3>
                            </div>

                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Shop Name</th>
                                        <th>Rating</th>
                                        <th>Phone Number</th>
                                        <th>Location</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Shop Name</th>
                                        <th>Rating</th>
                                        <th>Phone Number</th>
                                        <th>Location</th>
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
    <script src="{{ mix('js/shop-offline.js') }}"></script>
@endsection
