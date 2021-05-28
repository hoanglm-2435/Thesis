/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************************!*\
  !*** ./resources/js/datatables/shopee-cate.js ***!
  \************************************************/
$(function () {
  $("#data-table").DataTable({
    dom: 'Bfrtip',
    paging: false,
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
      url: route('shopee.get-cate')
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
      data: 'shop_count',
      name: 'shop_count',
      className: 'text-nowrap text-center'
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
      data: 'shop_list',
      name: 'shop_list',
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