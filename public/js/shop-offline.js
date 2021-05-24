/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************************************!*\
  !*** ./resources/js/datatables/shop-offline.js ***!
  \*************************************************/
$(function () {
  $("#data-table").DataTable({
    dom: 'Bfrtip',
    paging: true,
    lengthChange: false,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: true,
    buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
    processing: true,
    language: {
      processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
    },
    serverSide: true,
    ajax: {
      url: route('shop-offline.shop')
    },
    columns: [{
      data: "id",
      className: 'text-nowrap text-center',
      render: function render(data, type, row, meta) {
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    }, {
      data: 'name',
      name: 'name'
    }, {
      data: 'rating',
      name: 'rating',
      className: 'text-center'
    }, {
      data: 'user_rating',
      name: 'user_rating',
      className: 'text-nowrap text-center'
    }, {
      data: 'city',
      name: 'city',
      className: 'text-nowrap text-center'
    }, {
      data: 'address',
      name: 'address'
    }, {
      data: 'phone_number',
      name: 'phone_number',
      className: 'text-nowrap text-center',
      render: function render(data, type, full) {
        if (data) {
          return "<span>".concat(data, "</span>");
        }

        return 'No phone number';
      }
    }, {
      data: 'reviews',
      className: 'text-center'
    }]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  var $range = $("#rating-range");
  $range.ionRangeSlider({
    min: 0,
    max: 5,
    step: 1,
    type: 'double',
    postfix: " star",
    prettify_enabled: true,
    decorate_both: true,
    skin: "round",
    onChange: function onChange(data) {
      if ($.fn.DataTable.isDataTable('#data-table')) {
        $("#data-table").DataTable().destroy();
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $("#data-table").DataTable({
        dom: 'Bfrtip',
        paging: true,
        lengthChange: false,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
        processing: true,
        serverSide: true,
        ajax: {
          url: route('shop-offline.filter'),
          type: 'POST',
          data: {
            minRange: data.from,
            maxRange: data.to
          }
        },
        columns: [{
          data: "id",
          className: 'text-nowrap text-center',
          render: function render(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        }, {
          data: 'name',
          name: 'name'
        }, {
          data: 'rating',
          name: 'rating',
          className: 'text-center'
        }, {
          data: 'user_rating',
          name: 'user_rating',
          className: 'text-center'
        }, {
          data: 'city',
          name: 'city',
          className: 'text-center'
        }, {
          data: 'address',
          name: 'address'
        }, {
          data: 'phone_number',
          name: 'phone_number',
          className: 'text-center'
        }, {
          data: 'reviews',
          className: 'text-center'
        }]
      });
    }
  });
});
/******/ })()
;