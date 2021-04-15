$(document).ready(function() {
    $('.list-comments').on('click', function (e) {
        e.preventDefault();
        const id = $(this).attr('data-id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '/show-comments/' + id,
            type: 'get',
            dataType: 'json',
            success: function (response) {
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
