
jQuery(document).ready(function ($) {

    var select = function (mode) {
        var inputs = eval('gotObject.' + mode + 'Inputs');
        var surface = $( inputs[6] ).val();
        if (surface > 0) {
            var selectArray = [];
            for (var i = 0; i < 4; i++) {
                selectArray.push($( inputs[i] ).val())
            }
        }
        return selectArray;
    };

    var jcObject = function (mode) {
        var aspectRatio = 'gotObject.' + mode + 'AspectRatio';
        var showCords = mode + 'ShowCoords';
        var obj = {
            onChange: eval(showCords),
            bgColor:     'white',
            bgOpacity:   .4,
            aspectRatio: 1 / eval(aspectRatio)
        };
        var selectArray = select(mode);
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
        for (i = 0; i < 6; i++) {
            $( gotObject.gridInputs[i] ).val(c[gotObject.baseArray[i]]);
        }
        $(gotObject.gridInputs[6]).val(c[gotObject.baseArray[4]] * c[gotObject.baseArray[5]]);
    }

    function listShowCoords(c)
    {
        for (i = 0; i < 6; i++) {
            $(gotObject.listInputs[i]).val(c[gotObject.baseArray[i]]);
        }
        $(gotObject.listInputs[6]).val(c[gotObject.baseArray[4]] * c[gotObject.baseArray[5]]);
    }

});

