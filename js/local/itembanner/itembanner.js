var itemBannerInstance = {
    result: 'undefined',
    modes: [],
    relCoords: {},
    inputIdentifiers: [],
    croppingDataObject: 'undefined',
    mainWindowCropping: 'undefined',
    previewWindowCropping: 'undefined',
    image: {
        origWidth: 'undefined',
        origHeight: 'undefined'
    },
    figureOutIsIt: function () {
        var ibi = this;
        if (ibi.result === "undefined") {
            ibi.result = ($j("#type").val() === 'itembanner/banner');
            ibi.inputIdentifiers = ['x', 'y', 'x2', 'y2', 'w', 'h'];
            ibi.modes = ['grid', 'list'];
            ibi.modes.forEach(function (mode) {
                ibi.relCoords[mode] = {
                    active: [],
                    temporary: [],
                    changed: true
                };
            });
        }
        return ibi.result;
    },
    getFormatedIdentifiers: function (mode, hashtag) {
        hashtag = (hashtag) ? '#' : '';
        var formatedArray = [];
        this.inputIdentifiers.forEach(function (identifier) {
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
                inputIdentifiers: this.inputIdentifiers,
                gridAspectRatio: gridAspectRatio,
                listAspectRatio: listAspectRatio
            }
        }
        return this.croppingDataObject;
    },
    calculateSelect: function (mode) {
        var selectArray = [];
        if (this.relCoords[mode].changed === false) {
            if (p.jcObjects[mode].setSelect) {
                selectArray = p.jcObjects[mode].setSelect;
            }
        } else {
            var coords = itemBannerInstance.relCoords[mode].active;
            if (coords[4] > 0 && coords[5] > 0) {
                selectArray = [];
                var dimension = 'width';
                for (var i = 0; i < 4; i++) {
                    selectArray.push(coords[i] * p.image[dimension]);
                    dimension = (dimension === 'width') ? 'height' : 'width';
                }
            }
            itemBannerInstance.relCoords[mode].changed = false;
        }

        return selectArray;
    }
};

$j( document ).ready(function () {
    if (itemBannerInstance.figureOutIsIt()) {
        var formJq = $j( "#edit_form");
        formJq.attr("enctype", "multipart/form-data" );
        $j( ".scalable.save" ).each(function () {
            var onclick = $j(this).attr('onclick');
            onclick = 'extendOnclick("' + onclick + '")';
            $j(this).attr('onclick', onclick);
        });
        itemBannerInstance.image.origWidth = parseFloat(origImageWidth);
        itemBannerInstance.image.origHeight = parseFloat(origImageHeight);

        var inputsToManage = itemBannerInstance.getFormatedIdentifiers('grid').concat(itemBannerInstance.getFormatedIdentifiers('list'));
        inputsToManage.forEach(function (identifier) {
            $j('<input/>', {
                type: 'text',
                id: identifier,
                name: identifier
            }).appendTo(formJq);
        });

        $j( "#widget_instace_tabs_properties_section" ).click( function () {
            if (itemBannerInstance.mainWindowCropping === "undefined") {
                itemBannerInstance.mainWindowCropping = new Cropping(false, itemBannerInstance.getCroppingDataObject(), true);
                itemBannerInstance.mainWindowCropping.attach();
            } else {
                itemBannerInstance.mainWindowCropping.attach();
            }
        });

        itemBannerInstance.modes.forEach(function (mode) {
            var element = $j( "#ib_crop_enable_" +  mode);
            element.html(frozen);
            element.click(function (e) {
                e.preventDefault();
                if(element.html() === frozen) {
                    itemBannerInstance.mainWindowCropping.jcObjects[mode].api.enable();
                    itemBannerInstance.mainWindowCropping.jcObjects[mode].disabled = false;
                    element.html(unfrozen);
                } else {
                    itemBannerInstance.mainWindowCropping.jcObjects[mode].api.disable();
                    itemBannerInstance.mainWindowCropping.jcObjects[mode].disabled = true;
                    element.html(frozen);
                }
            });
        });
    }
});

function Cropping(currentWindow, dataObject, preDisabled) {

    this.isMainWindowCropping = (!currentWindow);

    var modes = dataObject.modes,
        gridInputs = dataObject.gridInputs,
        listInputs = dataObject.listInputs,
        inputIdentifiers = dataObject.inputIdentifiers,
        gridAspectRatio = dataObject.gridAspectRatio,
        listAspectRatio = dataObject.listAspectRatio,
        windw = (this.isMainWindowCropping) ? window : currentWindow,
        coordsToFill = (this.isMainWindowCropping) ? 'active' : 'temporary',
        p = this;

    preDisabled =(preDisabled === true);

    this.jq = windw.jQuery;

    this.jcObjects = {};

    this.image = {};

    this.minSquare = 'undefined';

    (function () {
        var dimension = 'width';
        var square = 1;
        modes.forEach(function (mode) {
            p.jcObjects[mode] = 'undefined';
            p.image[dimension] = parseFloat(eval("p.jq( '#image_preview_" + mode + "' )." + dimension + "()"));
            square *= p.image[dimension];
            if (dimension === 'width') {
                dimension = 'height'
            } else {
                p.minSquare = (square / (itemBannerInstance.image.origHeight * itemBannerInstance.image.origWidth)) * 10000;
            }
        });
    })();

    this.setWindowToJq = function (w) {
        p.jq = w.jQuery;
    };

    this.calculateSelect = function (mode) {
        var selectArray = [];
        if (itemBannerInstance.relCoords[mode].changed === false) {
            if (p.jcObjects[mode].setSelect) {
                selectArray = p.jcObjects[mode].setSelect;
            }
        } else {
            var coords = itemBannerInstance.relCoords[mode].active;
            if (coords[4] > 0 && coords[5] > 0) {
                selectArray = [];
                var dimension = 'width';
                for (var i = 0; i < 4; i++) {
                    selectArray.push(coords[i] * p.image[dimension]);
                    dimension = (dimension === 'width') ? 'height' : 'width';
                }
            }
            itemBannerInstance.relCoords[mode].changed = false;
        }

        return selectArray;
    };

    this.attach = function (singleMode) {
        var hereModes = (singleMode) ? [singleMode] : modes;
        hereModes.forEach(function (mode) {
            p.jq("#image_preview_" + mode ).Jcrop(
                p.jcObjects[mode],
                function () {
                    p.jcObjects[mode].api = this;
                }
            );
            if (p.jcObjects[mode].disabled === true) {
                p.jcObjects[mode].api.disable();
            }
        });
    };

    modes.forEach(function (mode) {
        var aspectRatio = mode + 'AspectRatio';
        var inputs = eval(mode + 'Inputs');

        function setInputValue(input, value) {
            p.jq(input).attr('value', value);
        }

        p.jcObjects[mode] = {
            onSelect: function (c) {
                if (c[inputIdentifiers[4]] * c[inputIdentifiers[5]] < p.minSquare) {
                    windw.alert('The cropping square is not large enough!');
                    p.jcObjects[mode].api.release();
                } else {
                    inputs.forEach(function (input, index) {
                        setInputValue(input, c[inputIdentifiers[index]]);
                    });
                    var dimension = 'width';
                    for (var i = 0; i < 6; i++) {
                        itemBannerInstance.relCoords[mode][coordsToFill][i] = c[inputIdentifiers[i]] / p.image[dimension];
                        dimension = (dimension === 'width') ? 'height' : 'width';
                    }
                    itemBannerInstance.relCoords[mode].changed = true;
                }
            },
            onRelease: function () {
                inputs.forEach(function (input) {
                    setInputValue(input, null);
                });
                for (var i = 0; i < 6; i++) {
                    itemBannerInstance.relCoords[mode][coordsToFill][i] = null;
                }
            },
            manageSelect: function (selectArray) {
                if (selectArray.length !== 0) {
                    p.jcObjects[mode].setSelect = selectArray;
                } else {
                    delete p.jcObjects[mode].setSelect;
                    if (p.isMainWindowCropping && p.jcObjects[mode].api !== "undefined") {
                        p.jcObjects[mode].api.release();
                    }
                }
            },
            bgColor: 'transparent',
            bgOpacity: .15,
            aspectRatio: 1 / eval(aspectRatio),
            disabled: preDisabled,
            api: 'undefined'
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
            var inputsToManage = gridInputs.concat(listInputs);
            inputsToManage.forEach(function (identifier) {
                var value = document.getElementById(identifier).getAttribute('value');
                win.document.write('<input type="text" id="' + identifier + '" value="' + value + '">');
            });
            win.document.write('<button id="ib_submit">' + submitText + '</button>');
            win.document.write('</div>');
            win.document.write('</body>');
            win.document.close();
            if (!!document.documentMode || (!isIE && !!window.StyleMedia)) {
                msCallback();
            } else {
                Event.observe(win, 'load', msCallback);
            }
            function msCallback(){
                win.document.getElementById("ib_submit").addEventListener("click", function () {
                    var tempArray = [];
                    itemBannerInstance.modes.forEach(function (mode) {
                        for (var i = 0; i < 6 ; i++) {
                            itemBannerInstance.relCoords[mode].active[i] = itemBannerInstance.relCoords[mode].temporary[i];
                        }
                    });
                    itemBannerInstance.modes.forEach(function (mode) {
                        itemBannerInstance.mainWindowCropping.jcObjects[mode].manageSelect(
                            itemBannerInstance.mainWindowCropping.calculateSelect(mode)
                        );
                    });
                    itemBannerInstance.mainWindowCropping.attach();
                    win.close();
                });
                if (itemBannerInstance.previewWindowCropping === "undefined") {
                    itemBannerInstance.previewWindowCropping = new Cropping(win, itemBannerInstance.getCroppingDataObject());
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
                var container = win.document.getElementsByTagName('div')[0];
                var widthForResize = container.offsetWidth;
                var heightForResize = container.offsetHeight;
                win.resizeTo(widthForResize + 40, heightForResize + 100);
            }
        }
    }
}

function extendOnclick(onclick) {
    alert(onclick);
    eval(onclick);
}