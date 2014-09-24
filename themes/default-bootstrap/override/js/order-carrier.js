/**
 * Created by Satz on 9/24/14.
 */
$(document).ready(function(){
debugger;
if (typeof cart_gift != 'undefined' && cart_gift && $('input#gift').is(':checked'))
    $('p#gift_div').show();

$("input#gift").on("click",function(){
    if($(this).is(':checked'))
    {
        $('p#gift_div').fadeIn();
    }
    else{
        $('p#gift_div').fadeOut();
    }
});

    $(document).on('submit', 'form[name=address_area]', function(){
        return acceptCGV();
    });
});
function acceptCGV()
{
    if (typeof msg_order_carrier != 'undefined' && $('#cgv').length && !$('input#cgv:checked').length)
    {
        if (!!$.prototype.fancybox)
            $.fancybox.open([
                {
                    type: 'inline',
                    autoScale: true,
                    minHeight: 30,
                    content: '<p class="fancybox-error">' + msg_order_carrier + '</p>'
                }],
                {
                    padding: 0
                });
        else
            alert(msg_order_carrier);
    }
    else
        return true;
    return false;
}