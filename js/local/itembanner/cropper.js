
jQuery(function($) {
    $(document).ready(function () {

        $('#image_preview_grid').Jcrop({
            onChange: showCoordsGrid,
            onSelect: showCoordsGrid,
            bgColor:     'white',
            bgOpacity:   .4,
            aspectRatio: 1 / gridAspectRatio
        });

        $('#image_preview_list').Jcrop({
            onChange: showCoordsList,
            onSelect: showCoordsList,
            bgColor:     'white',
            bgOpacity:   .4,
            aspectRatio: 1 / listAspectRatio
        });

        // Simple event handler, called from onChange and onSelect
        // event handlers, as per the Jcrop invocation above
        function showCoordsGrid(c)
        {
            $('#x1_grid').val(c.x);
            $('#y1_grid').val(c.y);
            $('#x2_grid').val(c.x2);
            $('#y2_grid').val(c.y2);
            $('#w_grid').val(c.w);
            $('#h_grid').val(c.h);
        }

        function showCoordsList(c)
        {
            $('#x1_list').val(c.x);
            $('#y1_list').val(c.y);
            $('#x2_list').val(c.x2);
            $('#y2_list').val(c.y2);
            $('#w_list').val(c.w);
            $('#h_list').val(c.h);
        }
    })
});



