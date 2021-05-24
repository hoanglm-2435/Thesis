$(document).ready(function() {
    $(document).on('click', '#list-comments' ,function (e) {
        e.preventDefault();
        const id = $(this).attr('data-id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: route('product.comments', id),
            type: 'get',
            dataType: 'json',
            success: function (response) {
                const product = response.product;
                const comment = response.comment;

                if (comment !== null) {
                    for (let i = 0; i < comment.length; i++) {
                        $('#details-table').append(
                            `<tr class="text-center">
                            <td>${comment[i]['author']}</td>
                            <td>${comment[i]['rating']}</td>
                            <td>${comment[i]['content']}</td>
                            <td class="text-nowrap">${comment[i]['time']}</td>
                        </tr>`
                        );
                    }

                    $('.product-name').html(product.name);
                    $('.product-name').attr('href', product.url);
                    $('.comment-total').html(comment.length);
                } else {
                    $('.comment-total').html('0');
                }

                $(".modal").on("hidden.bs.modal", function(){
                    $("#details-table").empty();
                });
            }
        });
    });
});
