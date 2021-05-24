$(document).ready(function() {
    $(document).on('click', '#list-reviews' ,function (e) {
        e.preventDefault();
        const id = $(this).attr('data-id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: route('shop.reviews', id),
            type: 'get',
            dataType: 'json',
            success: function (response) {
                const review = response.review;

                if (review !== null) {
                    for (let i = 0; i < review.length; i++) {
                        $('#details-table').append(
                            `<tr class="text-center">
                            <td>${review[i]['author']}</td>
                            <td>${review[i]['rating']}</td>
                            <td>${review[i]['content']}</td>
                            <td class="text-nowrap">${review[i]['time']}</td>
                        </tr>`
                        );
                    }
                    $('.review-total').html(review.length);
                } else {
                    $('.review-total').html('0');
                }

                $(".modal").on("hidden.bs.modal", function(){
                    $("#details-table").empty();
                });
            }
        });
    });
});
