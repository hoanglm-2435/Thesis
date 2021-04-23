$(function () {
    const url = window.location.pathname;
    const cateId = url.substring(url.lastIndexOf('/') + 1);

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
            url: route('shopee.get-shop', cateId),
        },
        columns: [
            {
                data: "id",
                className: 'text-nowrap text-center',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                data: 'name',
                name: 'name',
                render: function (data, type, full) {
                    return `<a href="${full.url}">${full.name}</a>`;
                }
            },
            { data: 'product_count', name: 'product_count', className: 'text-nowrap text-center' },
            { data: 'sold', name: 'sold', className: 'text-nowrap text-center' },
            { data: 'revenue', name: 'revenue', className: 'text-nowrap text-center' },
            { data: 'updated_at', name: 'updated_at', className: 'text-nowrap text-center' },
            { data: 'products', name: 'products', className: 'text-nowrap text-center' },
            { data: 'chart', className: 'text-nowrap text-center' },
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
