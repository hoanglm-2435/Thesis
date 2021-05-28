/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************************!*\
  !*** ./resources/js/datatables/shopee-analysis.js ***!
  \****************************************************/
$(function () {
  var url = window.location.pathname;
  var cateId = url.substring(url.lastIndexOf('/') + 1);
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
      url: route('shopee.get-shop', cateId)
    },
    columns: [{
      data: "id",
      className: 'text-nowrap text-center',
      render: function render(data, type, row, meta) {
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    }, {
      data: 'name',
      name: 'name',
      render: function render(data, type, full) {
        return "<a href=\"".concat(full.url, "\">").concat(full.name, "</a>");
      }
    }, {
      data: 'product_count',
      name: 'product_count',
      className: 'text-nowrap text-center'
    }, {
      data: 'sold',
      name: 'sold',
      className: 'text-nowrap text-center'
    }, {
      data: 'revenue',
      name: 'revenue',
      className: 'text-nowrap text-center'
    }, {
      data: 'updated_at',
      name: 'updated_at',
      className: 'text-nowrap text-center'
    }, {
      data: 'products',
      name: 'products',
      className: 'text-nowrap text-center',
      orderable: false
    }, {
      data: 'chart',
      className: 'text-nowrap text-center',
      orderable: false
    }]
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
/******/ })()
;