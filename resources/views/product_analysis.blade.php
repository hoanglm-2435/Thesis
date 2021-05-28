@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">
                            <span>Category: <a href="{{ $shop->category->url }}"> {{ $shop->category->name  }}</a></span>
                            <span> - Shop: <a href="{{ $shop->url }}"> {{ $shop->name }}</a></span>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('shopee.cate') }}">Shopee Category</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('shopee.show-shop', $cateID) }}">Shopee Mall</a></li>
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
                                <h3 class="card-title">Market analysis on the Shopee e-commerce platform is from <b>{{ $analysisAt ?? '' }}</b></h3>
                            </div>
                            <div class="mx-4 my-2 flex flex-column">
                                <div class="d-flex align-items-center">
                                    <h4 class="mr-2">Filter by:</h4>
                                    <select class="mb-2 form-select" id="select-filter" name="filter">
                                        <option value="price" selected>Price</option>
                                        <option value="rating">Rating</option>
                                    </select>
                                </div>
                                <input type="hidden" value="{{ $priceMax }}" id="price-max">
                                <input type="text" id="filter-range" readonly>
                            </div>

                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Price(VND)</th>
                                        <th>Sold/Month</th>
                                        <th>Revenue/Month(VND)</th>
                                        <th>Rating</th>
                                        <th>Updated At</th>
                                        <th>Reviews</th>
                                        <th>Analysis Chart</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Price(VND)</th>
                                        <th>Sold/Month</th>
                                        <th>Revenue/Month(VND)</th>
                                        <th>Rating</th>
                                        <th>Updated At</th>
                                        <th>Reviews</th>
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
    <div class="modal fade" id="commentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-fullscreen">
                <div class="modal-header">
                    <span class="font-weight-bold">List Comments Of Product: <a href="" class="product-name"></a></span>
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
    <style>
        .modal {
            padding: 0 !important; // override inline padding-right added from js
        }
        .modal .modal-dialog {
            width: 100%;
            max-width: 100%;
            height: 100%;
            margin: 0;
        }
        .modal .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
        }
        #details-table {
            word-break: break-word;
        }
        .modal .modal-body {
            overflow-y: auto;
        }
    </style>
@endsection

@section('script')
    <script src="{{ mix('js/show-comments.js') }}"></script>
    <script src="{{ mix('js/product-analysis.js') }}"></script>
@endsection
