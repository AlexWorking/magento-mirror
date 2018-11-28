var itemBannerInstance = {
    result: 'undefined',
    modes: 'undefined',
    inputs: 'undefined',
    croppingDataObject: 'undefined',
    mainWindowCropping: 'undefined',
    previewWindowCropping: 'undefined',
    figureOutIsIt: function () {
        if (this.result === "undefined") {
            $j( document ).ready(function (p) {
                p.result = ($j( "#type" ).val() === 'itembanner/banner');
                p.modes = ['grid', 'list'];
                p.inputs = {
                    baseArray: ['x', 'y', 'x2', 'y2', 'w', 'h', 's'],
                    img: {
                        "w_img": 'document.getElementById("image_preview_list").style.width',
                        "h_img": 'document.getElementById("image_preview_grid").style.height'
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
                listAspectRatio: listAspectRatio
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

        var ibImageUrl = $(thumbNailId).src;
        itemBannerInstance.modes.forEach(function (mode) {
            $j( "#image_preview_" + mode ).attr('src', ibImageUrl)
        });

        $j( "#widget_instace_tabs_properties_section" ).click( function () {
            if (itemBannerInstance.mainWindowCropping === "undefined") {
                itemBannerInstance.mainWindowCropping = new Cropping(itemBannerInstance.getCroppingDataObject(), true);
                itemBannerInstance.mainWindowCropping.attach();
            } else {
                itemBannerInstance.mainWindowCropping.attach();
            }
            Object.keys(itemBannerInstance.inputs.img).forEach(function (identifier) {
                var input = document.getElementById(identifier);
                input.setAttribute('value', eval(itemBannerInstance.inputs.img[identifier]));
            });
        });

        itemBannerInstance.modes.forEach(function (mode) {
            var element = $j( "#ib_crop_enable_" +  mode);
            element.html(frozen);
            element.click(function (e) {
                e.preventDefault();
                if(element.html() === frozen) {
                    itemBannerInstance.mainWindowCropping.api[mode].enable();
                    itemBannerInstance.mainWindowCropping.jcObjects[mode].disabled = false;
                    element.html(unfrozen);
                } else {
                    itemBannerInstance.mainWindowCropping.api[mode].disable();
                    itemBannerInstance.mainWindowCropping.jcObjects[mode].disabled = true;
                    element.html(frozen);
                }
            });
        });
    }
});

function Cropping(dataObject, preDisabled) {

    var modes = dataObject.modes,
        gridInputs = dataObject.gridInputs,
        listInputs = dataObject.listInputs,
        baseArray = dataObject.baseArray,
        gridAspectRatio = dataObject.gridAspectRatio,
        listAspectRatio = dataObject.listAspectRatio,
        p = this;

    preDisabled =(preDisabled === true);

    this.jq = jQuery;

    this.jcObjects = {
        'grid': 'undefined',
        'list': 'undefined'
    };

    this.api = {
        'grid': 'undefined',
        'list': 'undefined'
    };

    this.setWindowToJq = function (w) {
        p.jq = w.jQuery;
    };

    this.calculateSelect = function (mode) {
        var inputs = eval(mode + 'Inputs');
        var surface = p.jq(inputs[6]).val();
        if (surface > 0) {
            var selectArray = [];
            for (var i = 0; i < 4; i++) {
                selectArray.push(p.jq(inputs[i]).val())
            }
        }
        return selectArray;
    };

    this.attach = function () {
        modes.forEach(function (mode) {
            p.jq("#image_preview_" + mode ).Jcrop(p.jcObjects[mode], function () {
                p.api[mode] = this;
            });
            if (p.jcObjects[mode].disabled === true) {
                p.api[mode].disable();
            }
        });
    };

    modes.forEach(function (mode) {
        var aspectRatio = mode + 'AspectRatio';
        p.jcObjects[mode] = {
            onSelect: function (c) {
                for (var i = 0; i < 6; i++) {
                    p.jq(eval(mode + 'Inputs')[i]).attr('value', c[baseArray[i]]);
                }
                p.jq(eval(mode + 'Inputs')[6]).attr('value', c[baseArray[4]] * c[baseArray[5]]);
            },
            onRelease: function () {
                for (var i = 0; i < 6; i++) {
                    p.jq(eval(mode + 'Inputs')[i]).attr('value', null);
                }
                p.jq(eval(mode + 'Inputs')[6]).attr('value', null);
            },
            manageSelect: function (selectArray) {
                if (selectArray) {
                    p.jcObjects[mode].setSelect = selectArray;
                } else {
                    delete p.jcObjects[mode].setSelect;
                    if (p === itemBannerInstance.mainWindowCropping) {
                        p.api[mode].release();
                    }
                }
            },
            bgColor: 'transparent',
            bgOpacity: .15,
            aspectRatio: 1 / eval(aspectRatio),
            disabled: preDisabled
        };
        p.jcObjects[mode].manageSelect(p.calculateSelect(mode));
    });
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
                itemBannerInstance.modes.forEach(function (mode) {
                    var inputs = eval(mode + 'Inputs');
                    if (win.document.getElementById(inputs[6]).value > 0) {
                        inputs.forEach(scatterValues);
                    } else {
                        inputs.forEach(annulValues);
                    }
                    itemBannerInstance.mainWindowCropping.jcObjects[mode].manageSelect(
                        itemBannerInstance.mainWindowCropping.calculateSelect(mode)
                    );
                });
                itemBannerInstance.mainWindowCropping.attach();

                Object.keys(itemBannerInstance.inputs.img).forEach(scatterValues);

                function scatterValues(identifier) {
                    var input = document.getElementById(identifier);
                    input.setAttribute('value', win.document.getElementById(identifier).value);
                }

                function annulValues(identifier) {
                    var input = document.getElementById(identifier);
                    input.setAttribute('value', 'null');
                }

                win.close();
            });
            function msCallback(){
                if (itemBannerInstance.previewWindowCropping === "undefined") {
                    itemBannerInstance.previewWindowCropping = new Cropping(itemBannerInstance.getCroppingDataObject());
                    itemBannerInstance.previewWindowCropping.setWindowToJq(win);
                    itemBannerInstance.previewWindowCropping.attach();

                } else {
                    itemBannerInstance.previewWindowCropping.setWindowToJq(win);
                    itemBannerInstance.modes.forEach(function (mode) {
                        itemBannerInstance.previewWindowCropping.jcObjects[mode].manageSelect(
                            itemBannerInstance.previewWindowCropping.calculateSelect(mode)
                        );
                    });
                    itemBannerInstance.previewWindowCropping.attach();
                }
                var form = win.document.getElementById('preview_form');
                Object.keys(itemBannerInstance.inputs.img).forEach(function (identifier) {
                    var input = win.document.createElement('input');
                    input.setAttribute('id', identifier);
                    input.setAttribute('value', eval('win.' + itemBannerInstance.inputs.img[identifier]));
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
