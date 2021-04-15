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
                            <li class="breadcrumb-item active">Shopee Analysis</li>
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
                                <h3 class="card-title">Market analysis on the Shopee e-commerce platform</h3>
                            </div>

                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Shop Name</th>
                                        <th>Shop URL</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($shopAnalysis)
                                        @foreach ($shopAnalysis as $key => $shop)
                                            <tr>
                                                <td class="text-center">{{ $key += 1 }}</td>
                                                <td>
                                                    <a href="{{ route('products', $shop['shop_id']) }}">
                                                        {{ $shop['name'] }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ $shop['url'] }}">
                                                        {{ $shop['url'] }}
                                                    </a>
                                                </td>
                                                <td class="text-center">{{ $shop['sold'] }}</td>
                                                <td class="text-center">{{ $shop['revenue'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Shop Name</th>
                                        <th>Shop URL</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
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
    <script>
        $(function () {
            $("#data-table").DataTable({
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
