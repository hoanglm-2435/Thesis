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
                            <a href="{{ $shop->url }}"> {{ $shop->name }}</a>
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
                                <h3 class="card-title">Market analysis on the Shopee e-commerce platform is from <b>{{ $analysisAt }}</b></h3>
                            </div>
                            <div class="mx-4 my-2 flex flex-column">
                                <div class="d-flex align-items-center">
                                    <h4 class="mr-2">Filter by:</h4>
                                    <select class="mb-2 form-select" id="select-filter" name="filter">
                                        <option value="price" selected>Price</option>
                                        <option value="rating">Rating</option>
                                    </select>
                                </div>
                                <input type="text" id="filter-range" readonly>
                            </div>

                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Price(VND)</th>
                                        <th>Sold</th>
                                        <th>Revenue(VND)</th>
                                        <th>Rating</th>
                                        <th>Updated At</th>
                                        <th>Reviews</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Product Name</th>
                                        <th>Price(VND)</th>
                                        <th>Sold</th>
                                        <th>Revenue(VND)</th>
                                        <th>Rating</th>
                                        <th>Updated At</th>
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
    <script src="{{ mix('js/product-analysis.js') }}"></script>
@endsection
