@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="text-black">
                            Shopee Analysis -
                            <a href="{{ $report[0][0]->shop['url'] }}">
                                {{ $report[0][0]->shop['name'] }}
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('shopee') }}">Shopee Analysis</a></li>
                            <li class="breadcrumb-item active">Products</li>
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
{{--                                <div class="mb-2 flex flex-column">--}}
{{--                                    <div>--}}
{{--                                        <span>Max rating: </span>--}}
{{--                                        <input type="number" id="max" name="max">--}}
{{--                                    </div>--}}
{{--                                    <div class="mt-2">--}}
{{--                                        <span>Min rating: </span>--}}
{{--                                        <input type="number" id="min" name="min">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <table id="data-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Price(VND)</th>
                                        <th>Sold / Month</th>
                                        <th>Revenue / Month</th>
                                        <th>Rating</th>
                                        <th>Reviews</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($report)
                                        @foreach ($report as $key => $product)
                                            @if(!is_null($product[0]->id))
                                                <tr>
                                                    <td class="text-center">{{ $key += 1 }}</td>
                                                    <td>
                                                        <a href="{{ $product[0]->url }}">
                                                            {{ $product[0]->name }}
                                                        </a>
                                                    </td>
                                                    <td class="text-center">{{ $product[0]->price }}</td>
                                                    <td class="text-center">{{ $product[0]->soldPerMonth }}</td>
                                                    <td class="text-center">{{ $product[0]->revenuePerMonth }}</td>
                                                    <td class="text-center">{{ $product[0]->rating }}</td>
                                                    <td class="text-center">
                                                        {{ $product[0]->reviews }}
                                                        <button title="Quick View" data-toggle="modal"
                                                                class="btn btn-sm btn-default list-comments"
                                                                data-id="{{ $product[0]->id }}"
                                                                data-target="#commentModal" href="#">
                                                            <i class="far fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Sold</th>
                                        <th>Rating</th>
                                        <th>Reviews</th>
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
    <div class="modal fade" id="commentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="width: 130%">
                <div class="modal-header">
                    <h2>List Comment Of Product</h2>
                    <button type="button" class="close"
                            data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="comment-review-wrapper">
                        <div class="comment-review">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr class="text-center">
                                        <th class="width-1">
                                            Author
                                        </th>
                                        <th class="width-2">
                                            Rating
                                        </th>
                                        <th class="width-3">
                                            Content
                                        </th>
                                        <th class="width-4">
                                            Time
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody id="details-table">
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="3">Total number of valuable comments: </td>
                                        <td colspan="1" class="comment-total"></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ mix('js/show-comments.js') }}"></script>
    <script>
        $(function () {
            $("#data-table").DataTable({
                "paging": true,
                "lengthChange": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        });
    </script>
@endsection
