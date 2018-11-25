var itemBannerInstance = {
    result: 'undefined',
    modes: 'undefined',
    inputs: 'undefined',
    croppingDataObject: 'undefined',
    api: {
        grid: 'undefined',
        list: 'undefined'
    },
    figureOutIsIt: function () {
        if (this.result === "undefined") {
            $j( document ).ready(function (p) {
                p.result = ($j( "#type" ).val() === 'itembanner/banner');
                p.modes = ['grid', 'list'];
                p.inputs = {
                    baseArray: ['x', 'y', 'x2', 'y2', 'w', 'h', 's'],
                    img: {
                        "w_img": 'win.document.getElementById("image_preview_list").width',
                        "h_img": 'win.document.getElementById("image_preview_grid").height',
                        "src_img": '$(element).src'
                    }
                };
            }(this));
        }
        return this.result;
    },
    getFormatedIdentifiers: function (mode, hashtag) {
        hashtag = (hashtag) ? '#' : '';
        var formatedArray = [];
        this.inputs.baseArray.forEach(function (identifier) {
            formatedArray.push(hashtag + identifier + '_' + mode);
        });
        return formatedArray;
    },
    getCroppingDataObject: function () {
        if (this.croppingDataObject === "undefined") {
            this.croppingDataObject = {
                modes: this.modes,
                gridInputs: this.getFormatedIdentifiers('grid', true),
                listInputs: this.getFormatedIdentifiers('list', true),
                baseArray: this.inputs.baseArray,
                gridAspectRatio: gridAspectRatio,
                listAspectRatio: listAspectRatio,
            }
        }
        return this.croppingDataObject;
    }
};

$j( document ).ready(function () {
    if (itemBannerInstance.figureOutIsIt()) {
        var formJq = $j( "#edit_form");
        formJq.attr("enctype", "multipart/form-data" );
        var inputsToManage = itemBannerInstance.getFormatedIdentifiers('grid').concat(itemBannerInstance.getFormatedIdentifiers('list').concat(Object.keys(itemBannerInstance.inputs.img)));
        inputsToManage.forEach(function (identifier) {
            $j('<input/>', {
                type: 'text',
                id: identifier,
                name: identifier
            }).appendTo(formJq);
        });

        $j( "#widget_instace_tabs_properties_section" ).click( function () {
            attachCropper(itemBannerInstance.getCroppingDataObject(true))
        });

        itemBannerInstance.modes.forEach(function (mode) {
            var element = $j( "#ib_crop_enable_" +  mode);
            element.html(frozen);
            element.click(function (e) {
                e.preventDefault();
                if(element.html() === frozen) {
                    itemBannerInstance.api[mode].enable();
                    element.html(unfrozen);
                } else {
                    itemBannerInstance.api[mode].disable();
                    element.html(frozen);
                }
            });
        });
    }
});

function Cropping(modes, gridInputs, listInputs, baseArray, gridAspectRatio, listAspectRatio) {

    var $j = ($j) ? $j : jQuery;
    this.select = function (mode) {
        var inputs = eval(mode + 'Inputs');
        var surface = $j(inputs[6]).val();
        if (surface > 0) {
            var selectArray = [];
            for (var i = 0; i < 4; i++) {
                selectArray.push($j(inputs[i]).val())
            }
        }
        return selectArray;
    };
    this.jcObject = function (mode) {
        var aspectRatio = mode + 'AspectRatio';
        var showCords = this[mode + 'ShowCoords'];
        //var submitCoords = this[mode + 'SubmitCoords'];
        var obj = {
            onChange: eval(showCords),
            bgColor: 'transparent',
            bgOpacity: .15,
            aspectRatio: 1 / eval(aspectRatio)
        };
        var selectArray = this.select(mode);
        if (eval(selectArray) !== "undefined") {
            obj.setSelect = eval(selectArray);
        }
        //if (forMainWindow) {
        //    obj.onSelect = eval(submitCoords);
        //}
        return obj;
    };
    this.gridShowCoords = function (c) {
        for (var i = 0; i < 6; i++) {
            $j(gridInputs[i]).val(c[baseArray[i]]);
        }
        $j(gridInputs[6]).val(c[baseArray[4]] * c[baseArray[5]]);
    };

    this.listShowCoords = function (c) {
        for (var i = 0; i < 6; i++) {
            $j(listInputs[i]).val(c[baseArray[i]]);
        }
        $j(listInputs[6]).val(c[baseArray[4]] * c[baseArray[5]]);
    };

    this.gridSubmitCoords = function () {
        lodgeValues(window, gridInputs, listInputs);
    };

    this.listSubmitCoords = function () {
        lodgeValues(window, gridInputs, listInputs);
    };

    this.attach = function (preDisabled) {
        var p = this;
        modes.forEach(function (mode) {
            $j("#image_preview_" + mode ).Jcrop(p.jcObject(mode), function () {
                if(preDisabled === true) {
                    itemBannerInstance.api[mode] = this;
                    this.disable();
                }
            });
        });
    };
}

function attachCropper(dataObject) {
    var cropping = new Cropping(
        dataObject.modes,
        dataObject.gridInputs,
        dataObject.listInputs,
        dataObject.baseArray,
        dataObject.gridAspectRatio,
        dataObject.listAspectRatio,
        dataObject.forMainWindow
    );
    cropping.attach();
}

function imagePreview(element){
    if($(element)){
        var win = window;
        if(!itemBannerInstance.figureOutIsIt()) {
            win = win.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
            win.document.open();
            win.document.write('<body style="padding:0;margin:0"><img src="'+$(element).src+'" id="image_preview"/></body>');
            win.document.close();
            Event.observe(win, 'load', function(){
                var img = win.document.getElementById('image_preview');
                win.resizeTo(img.width+40, img.height+80);
            });
        } else {
            var gridInputs = itemBannerInstance.getFormatedIdentifiers('grid');
            var listInputs = itemBannerInstance.getFormatedIdentifiers('list');
            win = win.open('', 'preview', 'width=1200,height=1200,resizable=1,scrollbars=1');
            win.document.open();
            win.document.write('<head>');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/itembanner/jquery.Jcrop.css" media="all">');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/itembanner/edit.css" media="all">');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/lib/jquery/jquery-1.12.0.min.js"></script>');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/local/itembanner/jquery.Jcrop.min.js"></script>');
            win.document.write('<script>' + Cropping + '\n' + attachCropper +'</script>');
            win.document.write('</head>');
            win.document.write('<body id="ibw_body">');
            win.document.write('<div id="ib_main_container">');
            win.document.write('<div class="ib_containers">');
            win.document.write('<img id="image_preview_grid" class="ib_crops" src="'+$(element).src+'"/>');
            win.document.write('<h4>' + gridCroppingWindow + '</h4>');
            win.document.write('</div>');
            win.document.write('<div class="ib_containers">');
            win.document.write('<img id="image_preview_list" class="ib_crops" src="'+$(element).src+'"/>');
            win.document.write('<h4>' + listCroppingWindow + '</h4>');
            win.document.write('</div>');
            win.document.write('<form id="preview_form">');
            var inputsToManage = gridInputs.concat(listInputs);
            inputsToManage.forEach(function (identifier) {
                var value = document.getElementById(identifier).getAttribute('value');
                win.document.write('<input type="text" id="' + identifier + '" value="' + value + '">');
            });
            win.document.write('<input id="ib_submit" type="submit"/></form>');
            win.document.write('</div>');
            win.document.write('</body>');
            win.document.close();
            if (!!document.documentMode || (!isIE && !!window.StyleMedia)) {
                msCallback();
            } else {
                Event.observe(win, 'load', msCallback);
            }
            Event.observe(win, 'submit', function () {
                lodgeValues(win, gridInputs, listInputs);
            });
            function msCallback(){
                win.attachCropper(itemBannerInstance.getCroppingDataObject());
                var form = win.document.getElementById('preview_form');
                Object.keys(itemBannerInstance.inputs.img).forEach(function (identifier) {
                    var input = win.document.createElement('input');
                    input.setAttribute('id', identifier);
                    input.setAttribute('value', eval(itemBannerInstance.inputs.img[identifier]));
                    form.appendChild(input);
                });
                var container = win.document.getElementsByTagName('div')[0];
                var widthForResize = container.offsetWidth;
                var heightForResize = container.offsetHeight;
                win.resizeTo(widthForResize + 40, heightForResize + 100);
            }
        }
    }
}

function lodgeValues(addressedWindow, gridInputs, listInputs){

    itemBannerInstance.modes.forEach(function (mode) {
        var inputs = eval(mode + 'Inputs');
        if (addressedWindow.document.getElementById(inputs[6]).value > 0) {
            inputs.forEach(scatterValues);
        } else {
            inputs.forEach(annulValues);
        }
    });

    Object.keys(itemBannerInstance.inputs.img).forEach(scatterValues);

    function scatterValues(identifier) {
        var input = document.getElementById(identifier);
        input.setAttribute('value', addressedWindow.document.getElementById(identifier).value);
    }

    function annulValues(identifier) {
        var input = document.getElementById(identifier);
        input.setAttribute('value', 'null');
    }
    addressedWindow.close();
}
