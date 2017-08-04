/**
 * Created by belou on 21/07/17.
 */

$('.fa-trash').click(function() {
    var $i_tag = $(this),
        $tr_tag = $i_tag.parent().parent().parent(),
        $post_id = $tr_tag.attr('id'),
        $url = "/del/post/".concat($post_id);

    $.get($url)
        .done(function(data){
            if(data == "1") {
                $tr_tag.remove();
            }
            else {
                alert("Oups! Une erreur s'est produite ... Ce post n'a pas été supprimé!");
            }
        })
        .fail(function(data){
            alert("Oups! Une erreur s'est produite ... "+data);
        });

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

function openModal(post_id, parent_id) {
    $("#modal-comment").modal('show');
}

$('.comment-form-link').click(function(event) {
    event.preventDefault();

    var post_id = $(this).data("post-id"),
        parent_id = $(this).data("parent-id");

    openModal(post_id, parent_id);
});

$('.reply').click(function(event) {
    var post_id = $(this).parent().data("post-id"),
        id = $(this).parent().data("id");

    $(".comment-form").find("input[name=pid]").val(id);

    openModal(post_id, id);
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

$('.comment-group').on('click', function(){
    var classToHide = 'fa fa-chevron-up fa-2x',
        classToShow = 'fa fa-chevron-down fa-2x',
        bodyElement = $(this).parent().find(".comment-group-body");

    doCollaspe($(this), bodyElement, classToShow, classToHide);
});

$('.comment-contain').on('click', function() {
    var classToHide = 'fa fa-chevron-up',
        classToShow = 'fa fa-chevron-down',
        bodyElements = $(this).parent().find(".comment-contain-body");

    doCollaspe($(this), bodyElements, classToShow, classToHide);
});