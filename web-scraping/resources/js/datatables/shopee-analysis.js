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
        serverSide: true,
        ajax: {
            url: route('shopee.shops'),
        },
        columns: [
            { data: 'id', name: 'id' },
            {
                data: 'name',
                name: 'name',
                render: function (data, type, full) {
                    const productUrl = route('shopee.shop', full.id);

                    return `<a href="${productUrl}">${full.name}</a>`;
                }
            },
            {
                data: 'url',
                name: 'url',
                render: function (data, type, full) {
                    return `<a href="${full.url}">${full.url}</a>`;
                }
            },
            { data: 'sold', name: 'sold' },
            { data: 'revenue', name: 'revenue' },
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
