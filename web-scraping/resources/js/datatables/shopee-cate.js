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
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
        },
        serverSide: true,
        ajax: {
            url: route('shopee.get-cate'),
        },
        columns: [
            { data: 'id', name: 'id', className: 'text-center' },
            {
                data: 'name',
                name: 'name',
                render: function (data, type, full) {
                    return `<a href="${full.url}">${full.name}</a>`;
                }
            },
            { data: 'shop_count', name: 'shop_count', className: 'text-center' },
            { data: 'product_count', name: 'product_count', className: 'text-center' },
            { data: 'sold', name: 'sold', className: 'text-center' },
            { data: 'revenue', name: 'revenue', className: 'text-center' },
            { data: 'shop_list', name: 'shop_list', className: 'text-center' },
            { data: 'chart', className: 'text-center' },
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
