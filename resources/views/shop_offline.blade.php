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

                            <div class="mx-4 my-2 flex flex-column">
                                <div class="align-items-center">
                                    <h4 class="mr-2">Filter by rating:</h4>
                                    <input type="text" id="rating-range" readonly>
                                </div>
                            </div>

                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Shop Name</th>
                                        <th>Rating</th>
                                        <th>User Rating</th>
                                        <th>City</th>
                                        <th>Address</th>
                                        <th>Phone Number</th>
                                        <th>Reviews</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Shop Name</th>
                                        <th>Rating</th>
                                        <th>User Rating</th>
                                        <th>City</th>
                                        <th>Address</th>
                                        <th>Phone Number</th>
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
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>List Reviews Of Place</h2>
                    <button type="button" class="close"
                            data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="review-wrapper">
                        <div class="review-review">
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
                                        <td colspan="3">Total number of valuable reviews: </td>
                                        <td colspan="1" class="review-total"></td>
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
        }.modal-dialog {
            width: 100%;
            height: 100%;
        }
        .modal-content {
            height: auto;
            min-height: 100%;
            border-radius: 0;
        }
    </style>
@endsection

@section('script')
    <script src="{{ mix('js/show-reviews.js') }}"></script>
    <script src="{{ mix('js/shop-offline.js') }}"></script>
@endsection
