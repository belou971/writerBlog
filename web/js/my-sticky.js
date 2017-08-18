/**
 * Created by Eve ODIN on 21/05/17.
 */

$('#my-sticky').affix({
    offset: {
        top: $('header').height()
    }
});

$("#my-sticky").on('affix.bs.affix', function(){
    $('.my-brand').css('display', 'none');
});
