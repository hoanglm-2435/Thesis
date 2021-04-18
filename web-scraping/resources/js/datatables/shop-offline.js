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
            { data: 'id', name: 'id', className: 'text-center' },
            { data: 'name', name: 'name' },
            { data: 'rating', name: 'rating', className: 'text-center' },
            { data: 'phone_number', name: 'phone_number', className: 'text-center' },
            { data: 'location', name: 'location' },
        ]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    let $range = $("#rating-range");

    $range.ionRangeSlider({
        min: 0,
        max: 5,
        step: 1,
        type: 'double',
        postfix: " star",
        prettify_enabled: true,
        decorate_both: true,
        skin: "round",
        onChange : function (data) {
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
                        maxRange: data.to,
                    },
                },
                columns: [
                    { data: 'id', name: 'id', className: 'text-center' },
                    { data: 'name', name: 'name' },
                    { data: 'rating', name: 'rating', className: 'text-center' },
                    { data: 'phone_number', name: 'phone_number', className: 'text-center' },
                    { data: 'location', name: 'location' },
                ]
            });
        }
    });
});
