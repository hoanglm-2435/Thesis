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
            url: route('shop-offline.shop'),
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'rating', name: 'rating' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'location', name: 'location' },
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});
