/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************************************!*\
  !*** ./resources/js/datatables/product-analysis.js ***!
  \*****************************************************/
$(document).ready(function () {
  var url = window.location.pathname;
  var shopId = url.substring(url.lastIndexOf('/') + 1);
  var table = $("#data-table").DataTable({
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
      url: route('shopee.get-products', shopId)
    },
    columns: [{
      data: "id",
      className: 'text-nowrap text-center',
      render: function render(data, type, row, meta) {
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    }, {
      data: 'name',
      render: function render(data, type, full) {
        return "<a href=\"".concat(full.url, "\">").concat(full.name, "</a>");
      }
    }, {
      data: 'price',
      className: 'text-nowrap text-center'
    }, {
      data: 'sold',
      className: 'text-nowrap text-center'
    }, {
      data: 'revenue',
      className: 'text-nowrap text-center'
    }, {
      data: 'rating',
      className: 'text-nowrap text-center'
    }, {
      data: 'updated_at',
      className: 'text-nowrap text-center'
    }, {
      data: 'reviews',
      className: 'text-nowrap text-center',
      orderable: false
    }, {
      data: 'chart',
      className: 'text-nowrap text-center',
      orderable: false
    }]
  });
  var $range = $("#filter-range");
  var price_max = $("#price-max").val();
  $range.ionRangeSlider({
    min: 0,
    max: price_max,
    from: 0,
    to: price_max,
    step: 100000,
    type: 'double',
    postfix: " VND",
    prettify_enabled: true,
    prettify_separator: '.',
    decorate_both: true,
    skin: "round",
    onChange: function onChange(data) {
      apiFilter(data, 'price');
    }
  });
  var instance = $range.data("ionRangeSlider");
  $('#select-filter').change(function () {
    var filterType = $(this).find("option:selected").val();

    if (filterType === 'price') {
      instance.update({
        min: 0,
        max: price_max,
        from: 0,
        to: price_max,
        step: 100000,
        postfix: " VND"
      });
    } else if (filterType === 'rating') {
      instance.update({
        min: 0,
        max: 5,
        from: 0,
        to: 5,
        step: 1,
        postfix: " star"
      });
    }

    instance.update({
      onChange: function onChange(data) {
        apiFilter(data, filterType);
      }
    });
  });

  function apiFilter(data, filterType) {
    var url = window.location.pathname;
    var shopId = url.substring(url.lastIndexOf('/') + 1);

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
      language: {
        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
      },
      serverSide: true,
      ajax: {
        url: route('filter.products', shopId),
        type: 'POST',
        data: {
          filterType: filterType,
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
        render: function render(data, type, full) {
          return "<a href=\"".concat(full.url, "\">").concat(full.name, "</a>");
        }
      }, {
        data: 'price',
        className: 'text-nowrap text-center'
      }, {
        data: 'sold',
        className: 'text-nowrap text-center'
      }, {
        data: 'revenue',
        className: 'text-nowrap text-center'
      }, {
        data: 'rating',
        className: 'text-nowrap text-center'
      }, {
        data: 'updated_at',
        className: 'text-nowrap text-center'
      }, {
        data: 'reviews',
        className: 'text-nowrap text-center',
        orderable: false
      }, {
        data: 'chart',
        className: 'text-nowrap text-center',
        orderable: false
      }]
    });
  }
});
/******/ })()
;