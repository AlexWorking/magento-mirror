var itemBannerInstance = {
    result: undefined,
    modes: [],
    inputIdentifiers: [],
    relCoords: {},
    mainWindowCropping: {},
    previewWindowCropping: {},
    getResult: function () {
        if (typeof this.result === "undefined") {
            this.result = this.figureOutIsIt()
        }
        return this.result;
    },
    figureOutIsIt: function () {
        var ibi = this;
        ibi.result = ($j("#type").val() === 'itembanner/banner');
        ibi.inputIdentifiers = ['x', 'y', 'x2', 'y2', 'w', 'h'];
        ibi.modes = ['grid', 'list'];
        ibi.modes.forEach(function (mode) {
            ibi.relCoords[mode] = {
                active: [],
                temporary: [],
                changed: null
            };
        });
        return ibi.result;
    },
    setRelCoords: function () {
        this.modes.forEach(function (mode) {
            var relCoords = eval('outerVariables.' + mode + 'RelCoords');
            if (relCoords) {
                itemBannerInstance.relCoords[mode].active = relCoords;
            }
        });
    },
    manageSelects: function (cropping) {
        this.modes.forEach(function (mode) {
            cropping.jcObjects[mode].manageSelect(mode);
        })
    }
};

$j( document ).ready(function () {
    if (itemBannerInstance.getResult()) {
        var formJq = $j( "#edit_form");
        formJq.attr("enctype", "multipart/form-data" );
        $j( ".scalable.save" ).each(function () {
            var onclick = $j(this).attr('onclick');
            onclick = 'extendOnclick("' + onclick + '")';
            $j(this).attr('onclick', onclick);
        });

        $j( "#widget_instace_tabs_properties_section" ).click( function () {
            if (typeof outerVariables === "undefined") {
                return;
            }
            if ($j.isEmptyObject(itemBannerInstance.mainWindowCropping)) {
                itemBannerInstance.setRelCoords();
                itemBannerInstance.mainWindowCropping = new Cropping();
                itemBannerInstance.mainWindowCropping.attach();
            }
            itemBannerInstance.modes.forEach(function (mode) {
                var enable = $j( "#ib_crop_enable_" +  mode);
                var revert = $j( "#ib_crop_revert_" +  mode);
                enable.html(outerVariables.frozen);
                enable.click(function (e) {
                    e.preventDefault();
                    if(enable.html() === outerVariables.frozen) {
                        itemBannerInstance.mainWindowCropping.jcObjects[mode].api.enable();
                        itemBannerInstance.mainWindowCropping.jcObjects[mode].disabled = false;
                        enable.html(outerVariables.unfrozen);
                    } else {
                        itemBannerInstance.mainWindowCropping.jcObjects[mode].api.disable();
                        itemBannerInstance.mainWindowCropping.jcObjects[mode].disabled = true;
                        enable.html(outerVariables.frozen);
                    }
                });
                revert.html(outerVariables.revert);
                revert.click(function (e) {
                    e.preventDefault();
                    revert.attr('style', 'visibility: hidden');
                })
            });
        });
    }
});

function Cropping(currentWindow) {

    this.windowObject = (currentWindow) ? currentWindow : window;

    this.isMainWindowCropping = (!currentWindow);

    var p = this,
        modes = itemBannerInstance.modes,
        inputIdentifiers = itemBannerInstance.inputIdentifiers,
        coordsToFill;

    if (this.isMainWindowCropping) {
        coordsToFill = 'active';
    } else {
        coordsToFill = 'temporary';
    }

    this.jq = undefined;

    this.jcObjects = {};

    this.image = {};

    (function () {
        p.jq = p.windowObject.jQuery;
        var dimension = 'width';
        var square = 1;
        modes.forEach(function (mode) {
            p.jcObjects[mode] = {};
            p.image[dimension] = parseFloat(eval("p.jq( '#image_preview_" + mode + "' )." + dimension + "()"));
            square *= p.image[dimension];
            if (dimension === 'width') {
                dimension = 'height'
            } else {
                p.image.minSquare = (square / (outerVariables.origImageWidth * outerVariables.origImageHeight)) * 10000;
            }
        });
    })();

    this.updateWindowObject = function (w) {
        p.windowObject = w;
        p.jq = p.windowObject.jQuery
    };

    this.calculateSelect = function (mode) {
        var coords = itemBannerInstance.relCoords[mode].active;
        if (coords[4] > 0 && coords[5] > 0) {
            var selectArray = [];
            var dimension = 'width';
            for (var i = 0; i < 4; i++) {
                selectArray.push(coords[i] * p.image[dimension]);
                dimension = (dimension === 'width') ? 'height' : 'width';
            }
        }
        return selectArray;
    };

    this.attach = function () {
        modes.forEach(function (mode) {
            if (p.isMainWindowCropping && !itemBannerInstance.relCoords[mode].changed && p.launched) {
                return;
            }
            p.jq( "#image_preview_" + mode ).Jcrop(
                p.jcObjects[mode],
                function () {
                    p.jcObjects[mode].api = this;
                }
            );
            if (p.jcObjects[mode].disabled === true) {
                p.jcObjects[mode].api.disable();
            }
            if (itemBannerInstance.relCoords[mode].changed) {
                itemBannerInstance.relCoords[mode].changed = false;
            }
        });
        p.launched = true;
    };

    modes.forEach(function (mode) {
        p.jcObjects[mode] = {
            onSelect: function (c) {
                if(!p.jcObjects[mode].needsOnSelectFire) {
                    p.jcObjects[mode].needsOnSelectFire = true;
                }
                if (c[inputIdentifiers[4]] * c[inputIdentifiers[5]] < p.image.minSquare) {
                    p.windowObject.alert('The cropping square is not large enough!');
                    p.jcObjects[mode].api.release();
                } else {
                    var dimension = 'width';
                    for (var i = 0; i < 6; i++) {
                        itemBannerInstance.relCoords[mode][coordsToFill][i] = c[inputIdentifiers[i]] / p.image[dimension];
                        dimension = (dimension === 'width') ? 'height' : 'width';
                    }
                    if (p.launched) {
                        itemBannerInstance.relCoords[mode].changed = true;
                    }
                }
            },
            onRelease: function () {
                itemBannerInstance.relCoords[mode][coordsToFill] = [];
                itemBannerInstance.relCoords[mode].changed = true;
            },
            manageSelect: function (mode) {
                if (itemBannerInstance.relCoords[mode].changed === false) {
                    return;
                }
                if (itemBannerInstance.relCoords[mode].changed === null && p.launched) {
                    return;
                }
                var selectArray = p.calculateSelect(mode);
                if (selectArray) {
                    p.jcObjects[mode].setSelect = selectArray;
                } else {
                    delete p.jcObjects[mode].setSelect;
                    p.jcObjects[mode].needsOnSelectFire = true;
                    if (p.isMainWindowCropping && typeof p.jcObjects[mode].api !== "undefined") {
                        p.jcObjects[mode].api.release();
                    }
                }
            },
            bgColor: 'transparent',
            bgOpacity: .2,
            aspectRatio: 1 / eval('outerVariables.' + mode + 'AspectRatio'),
            disabled: p.isMainWindowCropping,
            needsOnSelectFire: false,
            api: undefined,
            launched: false
        };
        p.jcObjects[mode].manageSelect(mode);
    });
}

function imagePreview(element){
    if($(element)){
        var win = window;
        if(!itemBannerInstance.getResult()) {
            win = win.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
            win.document.open();
            win.document.write('<body style="padding:0;margin:0"><img src="'+$(element).src+'" id="image_preview"/></body>');
            win.document.close();
            Event.observe(win, 'load', function(){
                var img = win.document.getElementById('image_preview');
                win.resizeTo(img.width+40, img.height+80);
            });
        } else {
            if (itemBannerInstance.previewWindowCropping.windowObject &&
                !itemBannerInstance.previewWindowCropping.windowObject.closed) {
                itemBannerInstance.previewWindowCropping.windowObject.focus();
                return;
            }
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
            win.document.write('<h4>' + outerVariables.gridCroppingWindow + '</h4>');
            win.document.write('</div>');
            win.document.write('<div class="ib_containers">');
            win.document.write('<img id="image_preview_list" class="ib_crops" src="'+$(element).src+'"/>');
            win.document.write('<h4>' + outerVariables.listCroppingWindow + '</h4>');
            win.document.write('</div>');
            win.document.write('<button id="ib_submit">' + outerVariables.submitText + '</button>');
            win.document.write('</div>');
            win.document.write('</body>');
            win.document.close();
            if (!!document.documentMode || (!isIE && !!window.StyleMedia)) {
                msCallback();
            } else {
                Event.observe(win, 'load', msCallback);
            }
            Event.observe(win, 'unload', function () {
               itemBannerInstance.modes.forEach(function (mode) {
                   if (itemBannerInstance.relCoords[mode].changed) {
                       itemBannerInstance.relCoords[mode].changed = false;
                       itemBannerInstance.previewWindowCropping.launched = false;
                   }
               });
            });
            function msCallback(){
                if ($j.isEmptyObject(itemBannerInstance.previewWindowCropping)) {
                    itemBannerInstance.previewWindowCropping = new Cropping(win);
                    itemBannerInstance.manageSelects(itemBannerInstance.previewWindowCropping);
                    itemBannerInstance.previewWindowCropping.attach();

                } else {
                    itemBannerInstance.previewWindowCropping.updateWindowObject(win);
                    itemBannerInstance.manageSelects(itemBannerInstance.previewWindowCropping);
                    itemBannerInstance.previewWindowCropping.attach();
                }
                win.document.getElementById("ib_submit").addEventListener("click", function () {
                    var tempArray = [];
                    itemBannerInstance.modes.forEach(function (mode) {
                        for (var i = 0; i < 6 ; i++) {
                            itemBannerInstance.relCoords[mode].active[i] = itemBannerInstance.relCoords[mode].temporary[i];
                        }
                    });
                        itemBannerInstance.manageSelects(itemBannerInstance.mainWindowCropping);
                        itemBannerInstance.manageSelects(itemBannerInstance.previewWindowCropping);
                    itemBannerInstance.mainWindowCropping.attach();
                    itemBannerInstance.previewWindowCropping.launched = false;
                    win.close();
                });
                var container = win.document.getElementsByTagName('div')[0];
                var widthForResize = container.offsetWidth;
                var heightForResize = container.offsetHeight;
                win.resizeTo(widthForResize + 40, heightForResize + 100);
            }
        }
    }
}

function extendOnclick(onclick) {
    if (typeof outerVariables !== "undefined") {
        if (!$j.isEmptyObject(itemBannerInstance.previewWindowCropping) &&
            !itemBannerInstance.previewWindowCropping.windowObject.closed) {
            itemBannerInstance.previewWindowCropping.windowObject.close();
        }
        itemBannerInstance.modes.forEach(function (mode) {
            $cond = itemBannerInstance.relCoords[mode].changed !== null && !$j( "#" + outerVariables.instanceHtmlHash + "_image_delete" ).is(':checked');
            $j( "#" + outerVariables.instanceHtmlHash + '_rel_coords_' + mode).attr(
                'value',
                ($cond) ? JSON.stringify(itemBannerInstance.relCoords[mode].active) : ''
            )
        });
    }
    eval(onclick);
}