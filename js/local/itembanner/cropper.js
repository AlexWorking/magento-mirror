
jQuery(document).ready(function(){

    jQuery('#image_preview_grid').Jcrop({
        onChange: showCoordsGrid,
        onSelect: showCoordsGrid,
        bgColor:     'transparent',
        bgOpacity:   .4,
        aspectRatio: 16 / 9
    });

    jQuery('#image_preview_list').Jcrop({
        onChange: showCoordsList,
        onSelect: showCoordsList,
        bgColor:     'transparent',
        bgOpacity:   .4,
        aspectRatio: 16 / 9
    });

});

// Simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
function showCoordsGrid(c)
{
    jQuery('#x1_grid').val(c.x);
    jQuery('#y1_grid').val(c.y);
    jQuery('#x2_grid').val(c.x2);
    jQuery('#y2_grid').val(c.y2);
    jQuery('#w_grid').val(c.w);
    jQuery('#h_grid').val(c.h);
}

function showCoordsList(c)
{
    jQuery('#x1_list').val(c.x);
    jQuery('#y1_list').val(c.y);
    jQuery('#x2_list').val(c.x2);
    jQuery('#y2_list').val(c.y2);
    jQuery('#w_list').val(c.w);
    jQuery('#h_list').val(c.h);
}

