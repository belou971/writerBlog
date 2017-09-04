/**
 * Created by belou on 21/07/17.
 */
$('.btn-md').on('click', function() {
    var lastIdx  = $(this).data('idx');
    var url = '/more/'+ lastIdx,
        more_btn = $(this);

    $.get(url)
        .done(function(data) {
            $('.post_list').append(data.content);
            more_btn.data('idx', data.idx);
            if(data.hide == true) {
                more_btn.parent().css('display', 'none');
            }
    })
});


$('.fa-trash').click(function() {
    var $i_tag = $(this),
        $tr_tag = $i_tag.parent().parent().parent(),
        $post_id = $tr_tag.attr('id'),
        $url = "/admin/del/post/".concat($post_id);

    $.get($url)
        .done(function(data){
            if(data == "1") {
                $tr_tag.remove();
            }
            else {
                alert("Oups! Une erreur s'est produite ... Ce post n'a pas été supprimé!");
            }
        })
});


function changePostStatus($element, $url, $classToRemove, $classToAdd)
{
    var $tr_tag = $element.parent().parent().parent(),
        $post_id = $tr_tag.attr('id');

    $.post($url, {id: $post_id})
        .done(function(data) {
            $element.removeClass($classToRemove).addClass($classToAdd);
            $('.unpublished').html(data.nbUnpublishedPost);
            $('.published').html(data.nbPublishedPost);
    })
}

function openModal() {

    $("#modal-comment").modal('show');
}

$('.comment-form-link').on('click', function(event) {
    event.preventDefault();

    var post_id = $(this).data("post-id"),
        parent_id = $(this).data("parent-id");

    $(".comment-form").find("input[name=post_id]").val(post_id);

    openModal();
});

$('.report').on('click', function(){
    var $report = $(this);
    var id = $report.parent().data("id"),
        url = "/comment/alert";

    $.post(url, {"id": id})
        .done(function(data) {
            $report.parent().prepend('<i class="fa fa-flag-o"> Signalé</i>');
            $report.remove();
        });
});

function doRefresh(id, url)
{
    var commentRow = $('.comment-contain-footer[data-id=' + id + ']').closest('.one-comment-row'),
        commentList = commentRow.closest('.comment-list');

    $.post(url, {"id": id})
        .done(function(data) {
            if(data.id == id) {
                commentRow.remove();

                if(commentList.find('.one-comment-row').length == 0) {
                    commentList.closest('.one-post-row').remove();
                }
            }
        });
}

function doRefresh2(id, url, selectorToUpdate)
{
    var commentRow = $('.comment-contain-footer[data-id=' + id + ']').closest('.one-comment-row'),
        commentList = commentRow.closest('.comment-list');

    $.post(url, {"id": id })
        .done(function(data) {
            if(data.id == id) {
                commentRow.remove();

                if(commentList.find('.one-comment-row').length == 0) {
                    commentList.closest('.one-post-row').remove();
                }

                $(selectorToUpdate).replaceWith(data.html);

                registerListenersOnComments($(selectorToUpdate));
            }
        });
}


$('.dialog-confirmation .btn-confirm').on('click', function(){
    var id = $(this).data("id"),
        url = "/admin/comment/delete";

    doRefresh(id, url);

    $('.dialog-confirmation').modal('hide');
});

$('.mark-published').on('click', function(){
    var id = $(this).parent().data("id"),
        url = "/admin/comment/publish";

    doRefresh(id, url);
});

function doCollaspe(root, body, classToShow, classToHide)
{
    var iconElement = '';

    if(root.find('.fa-chevron-down').length > 0) {
        body.show();

        iconElement = root.find('.fa-chevron-down');
        iconElement.removeClass(classToShow).addClass(classToHide);
    }
    else if(root.find('.fa-chevron-up').length > 0) {
        body.hide();

        iconElement = root.find('.fa-chevron-up');
        iconElement.removeClass(classToHide).addClass(classToShow);
    }
}

registerListenersOnComments($('body'));

function registerListenersOnComments($parent) {
    $parent.find('.mark-read').on('click', function(){
        var id = $(this).parent().data("id"),
            url = "/admin/comment/read";

        doRefresh2(id, url, '.read-comment-section');
    });

    $parent.find('.mark-unread').on('click', function(){
        var id = $(this).parent().data("id"),
            url = "/admin/comment/unread";

        doRefresh2(id, url, '.unread-comment-section');
    });

    $parent.find('.comment-group').on('click', function(){
        var classToHide = 'fa fa-chevron-up fa-2x',
            classToShow = 'fa fa-chevron-down fa-2x',
            bodyElement = $(this).parent().find(".comment-group-body");

        doCollaspe($(this), bodyElement, classToShow, classToHide);
    });

    $parent.find('.comment-contain').on('click', function() {
        var classToHide = 'fa fa-chevron-up',
            classToShow = 'fa fa-chevron-down',
            bodyElements = $(this).parent().find(".comment-contain-body");

        doCollaspe($(this), bodyElements, classToShow, classToHide);
    });

    $parent.find('.reply').on('click', function(event) {
        var post_id = $(this).parent().data("post-id"),
            id = $(this).parent().data("id");

        $(".comment-form").find("input[name=pid]").val(id);
        $(".comment-form").find("input[name=post_id]").val(post_id);
        $("#message").val('');

        openModal();
    });

    $parent.find('.msg-delete').on('click', function(){
        var comment_id = $(this).parent().data("id"),
            dialog_confirm = $('.dialog-confirmation'),
            button_confirm = dialog_confirm.find('.btn-confirm');

        button_confirm.data('id', comment_id);

        dialog_confirm.find('.modal-title').html("Confirmer la suppression");
        dialog_confirm.find('.dialog-content').html('Attention, vous allez supprimer définitivement ce commentaire.<br/>' +
            'Voulez-vous vraiment le faire?');

        dialog_confirm.modal('show');
    });
}