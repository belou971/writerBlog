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
    })
    .fail(function(data){
        alert("Oups! Une erreur s'est produite ... "+data.post_status);
    });
}