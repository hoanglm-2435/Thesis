/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************!*\
  !*** ./resources/js/show-reviews.js ***!
  \**************************************/
$(document).ready(function () {
  $(document).on('click', '#list-reviews', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $.ajax({
      url: route('shop.reviews', id),
      type: 'get',
      dataType: 'json',
      success: function success(response) {
        var review = response.review;

        if (review !== null) {
          for (var i = 0; i < review.length; i++) {
            $('#details-table').append("<tr class=\"text-center\">\n                            <td>".concat(review[i]['author'], "</td>\n                            <td>").concat(review[i]['rating'], "</td>\n                            <td>").concat(review[i]['content'], "</td>\n                            <td class=\"text-nowrap\">").concat(review[i]['time'], "</td>\n                        </tr>"));
          }

          $('.review-total').html(review.length);
        } else {
          $('.review-total').html('0');
        }

        $(".modal").on("hidden.bs.modal", function () {
          $("#details-table").empty();
        });
      }
    });
  });
});
/******/ })()
;