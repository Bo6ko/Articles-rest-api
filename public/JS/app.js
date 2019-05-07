$(function () {
    $('form.addComment input[name="btnAdd"]').on('click', function () {
        var container = $(this).closest('.articles');
        var form = $(this).closest('form');
        var commentElement = form.find('input[name="comment"]');
        var comment = $.trim(commentElement.val());
        if (comment === "") {
            alert("Моля добавете коментар");
        } else {
            $.post(form.attr('action'), {comment: comment}, function (res) {
                if (res.success) {
                    container.find('.comments').append('<div class="comment">' + '<i style="color: blue">Коментар създаден от ' + res.username + ':</i> ' + res.comment + '  <button style="margin-bottom: 2px!important;" class="btn btn-primary mb-2 likeComment" data-article="' + res.article_id + '" data-id="' + res.id + '">like</button> (<span class="commentLikes">0</span> likes)</div>');
                    commentElement.val('');
                } else {
                    alert(res.error);
                }
            }, 'json');
        }
        return false;
    });
    $('.likePost').on('click', function () {
        var btn = $(this);
        var container = btn.closest('.likes');
        $.post('/like-post', {id: $(this).attr('data-article')}, function (res) {
            if (res.success) {
                container.find('.postLikes').html(res.count);
            } else {
                alert(res.error);
            }
        }, 'json');
        return false;
    });
    $('.comments').on('click', '.likeComment', function () {
        var btn = $(this);
        var container = btn.closest('.comment');
        $.post('/like-comment', {id: $(this).attr('data-id')}, function (res) {
            if (res.success) {
                container.find('.commentLikes').html(res.count);
            } else {
                alert(res.error);
            }
        }, 'json');
        return false;
    });
});