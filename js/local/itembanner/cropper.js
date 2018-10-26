jQuery(function($) {
    $('#image_preview').Jcrop();
});

jQuery(document).ready(function(){

    jQuery('#image_preview').Jcrop({
        onChange: showCoords,
        onSelect: showCoords,
        bgColor:     'transparent',
        bgOpacity:   .4,
        aspectRatio: 16 / 9
    });

});

// Simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
function showCoords(c)
{
    jQuery('#x1').val(c.x);
    jQuery('#y1').val(c.y);
    jQuery('#x2').val(c.x2);
    jQuery('#y2').val(c.y2);
    jQuery('#w').val(c.w);
    jQuery('#h').val(c.h);
};

