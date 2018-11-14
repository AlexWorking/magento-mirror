
jQuery(document).ready(function ($) {

        var gridFirstCord = $( "#x1_grid" ).val();
        if (!gridFirstCord || gridFirstCord !== "null") {
            var gridSelect = [gridFirstCord, $( "#y1_grid" ).val(), $( "#x2_grid" ).val(), $( "#y2_grid" ).val()];
        }

        var listFirstCord = $( "#x1_list" ).val();
        if (!listFirstCord || listFirstCord !== "null") {
            var listSelect = [listFirstCord, $( "#y1_list" ).val(), $( "#x2_list" ).val(), $( "#y2_list" ).val()];
        }
        
        var jcObject = function (mode) {
            var aspectRatio = mode + 'AspectRatio';
            var showCords = mode + 'ShowCoords';
            var obj = {
                onChange: eval(showCords),
                onSelect: eval(showCords),
                bgColor:     'white',
                bgOpacity:   .4,
                aspectRatio: 1 / eval(aspectRatio)
            };
            var selectArray = mode + 'Select';
            if (eval(selectArray) !== "undefined") {
                obj.setSelect = eval(selectArray);
            }
            return obj;
        };

        $('#image_preview_grid').Jcrop(jcObject('grid'));

        $('#image_preview_list').Jcrop(jcObject('list'));

        // Simple event handler, called from onChange and onSelect
        // event handlers, as per the Jcrop invocation above
        function gridShowCoords(c)
        {
            $('#x1_grid').val(c.x);
            $('#y1_grid').val(c.y);
            $('#x2_grid').val(c.x2);
            $('#y2_grid').val(c.y2);
            $('#w_grid').val(c.w);
            $('#h_grid').val(c.h);
        }

        function listShowCoords(c)
        {
            $('#x1_list').val(c.x);
            $('#y1_list').val(c.y);
            $('#x2_list').val(c.x2);
            $('#y2_list').val(c.y2);
            $('#w_list').val(c.w);
            $('#h_list').val(c.h);
        }
});



