var itemBannerInstance = {
    result: undefined,
    modes: [],
    inputIds: [],
    coordIdentifiers: [],
    relCoords: {},
    croppings: {},
    getResult: function () {
        if (typeof this.result === "undefined") {
            this.result = this.figureOutIsIt()
        }
        return this.result;
    },
    figureOutIsIt: function () {
        var ibi = this;
        ibi.result = ($j("#type").val() === 'itembanner/banner');
        if (ibi.result === true) {
            ibi.coordIdentifiers = ['x', 'y', 'x2', 'y2', 'w', 'h'];
            ibi.croppings.main = {};
            ibi.croppings.preview = {};
            ibi.modes = ['grid', 'list'];
            ibi.modes.forEach(function (mode) {
                ibi.inputIds[mode] = outerVariables.instanceHtmlIdPrefix + '_rel_coords_' + mode;
                ibi.relCoords[mode] = {
                    original: JSON.parse($j( "#" + ibi.inputIds[mode] ).val()),
                    aCoords: [],
                    bCoords: [],
                    forPost: 'aCoords',
                    temporary: 'bCoords',
                    currentChangeStatus: null,
                    entryChangeStatus:null
                };
            });
        }
        return ibi.result;
    },
    pullFromOrigRelCoords: function (mode, toCoords) {
        var ibi = this;
        var modes = (mode && mode !== false) ? [mode] : ibi.modes;
        modes.forEach(function (mode) {
            if (typeof toCoords === "undefined") {
                ibi.relCoords[mode].original.forEach(function (val, ind) {
                    ibi.relCoords[mode].aCoords[ind] = val;
                    ibi.relCoords[mode].bCoords[ind] = val;
                });
            } else {
                ibi.relCoords[mode].original.forEach(function (val, ind) {
                    ibi.relCoords[mode][toCoords][ind] = val;
                });
            }
        });
    },
    uploadRelCoords: function () {
        var ibi = this;
        var arrayToUpload;
        ibi.modes.forEach(function (mode) {
            arrayToUpload = ibi.relCoords[mode].forPost;
            $j( "#" + ibi.inputIds[mode] ).attr(
                'value',
                JSON.stringify(ibi.relCoords[mode][arrayToUpload])
            );
        });
    },
    swapCoordsToFill: function (mode) {
        this.relCoords[mode].forPost = (this.relCoords[mode].forPost === 'aCoords') ? 'bCoords' : 'aCoords';
        itemBannerInstance.croppings.preview.jcObjects[mode].coordsToFill = itemBannerInstance.croppings.main.jcObjects[mode].coordsToFill;
        itemBannerInstance.croppings.main.jcObjects[mode].coordsToFill = this.relCoords[mode].forPost;
        itemBannerInstance.croppings.main.jcObjects[mode].coordsForSelect = this.relCoords[mode].forPost;
        itemBannerInstance.croppings.preview.jcObjects[mode].coordsForSelect = this.relCoords[mode].forPost;
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
            if ($j.isEmptyObject(itemBannerInstance.croppings.main)) {
                itemBannerInstance.pullFromOrigRelCoords();
                itemBannerInstance.croppings.main = new Cropping(false, [workoutFreezingButton, workoutRevertButton]);
                itemBannerInstance.croppings.main.attach();
            }
        });
        $j( window ).unload(function () {
            if (!$j.isEmptyObject(itemBannerInstance.croppings.preview) &&
                !itemBannerInstance.croppings.preview.windowObject.closed) {
                itemBannerInstance.croppings.preview.windowObject.close();
            }
        });
    }
});

function Cropping(currentWindow, buttonWorkoutCallbacks) {

    this.windowObject = (currentWindow !== false) ? currentWindow : window;

    this.isMainWindowCopping = (!currentWindow);

    var p = this,
        modes = itemBannerInstance.modes,
        coordIdentifiers = itemBannerInstance.coordIdentifiers;

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

    this.calculateSelect = function (mode, fromRelCoordsOf) {
        var coords = itemBannerInstance.relCoords[mode][fromRelCoordsOf]
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

    this.attach = function (hereMode) {
        var hereModes = (!hereMode) ? modes : [hereMode];
        hereModes.forEach(function (mode) {
            if (p.jcObjects[mode].actual === true) {
                p.jcObjects[mode].actual = null;
            }
            else if (p.jcObjects[mode].actual === false) {
                p.jcObjects[mode].manageSelect();
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
            p.jcObjects[mode].actual = true;
            if (itemBannerInstance.relCoords[mode].currentChangeStatus === true) {
                itemBannerInstance.relCoords[mode].currentChangeStatus = false;
            }
            buttonWorkoutCallbacks.forEach(function (func) {
                func(p, mode);
            });
        });
    };

    modes.forEach(function (mode) {
        p.jcObjects[mode] = {
            onSelect: function (c) {
                if (c[coordIdentifiers[4]] * c[coordIdentifiers[5]] < p.image.minSquare) {
                    p.windowObject.alert('The cropping square is not large enough!');
                    p.jcObjects[mode].api.release();
                } else {
                    var dimension = 'width';
                    for (var i = 0; i < 6; i++) {
                        itemBannerInstance.relCoords[mode][p.jcObjects[mode].coordsToFill][i] = c[coordIdentifiers[i]] / p.image[dimension];
                        dimension = (dimension === 'width') ? 'height' : 'width';
                    }
                    if (p.jcObjects[mode].actual === true) {
                        p.jq( "#ib_crop_revert_" +  mode).css('visibility', 'visible');
                        itemBannerInstance.relCoords[mode].currentChangeStatus = true;
                    }
                }
            },
            onRelease: function () {
                itemBannerInstance.relCoords[mode][p.jcObjects[mode].coordsToFill] = [];
                itemBannerInstance.relCoords[mode].currentChangeStatus = true;
            },
            manageSelect: function () {
                var selectArray = p.calculateSelect(mode, p.jcObjects[mode].coordsForSelect);
                if (selectArray) {
                    p.jcObjects[mode].setSelect = selectArray;
                } else {
                    delete p.jcObjects[mode].setSelect;
                    if (p.isMainWindowCopping && typeof p.jcObjects[mode].api !== "undefined") {
                        p.jcObjects[mode].api.release();
                    }
                }
            },
            bgColor: 'transparent',
            bgOpacity: .2,
            aspectRatio: 1 / eval('outerVariables.' + mode + 'AspectRatio'),
            setSelect: undefined,
            coordsForSelect: 'aCoords',
            coordsToFill: (p.isMainWindowCopping) ? 'aCoords' : 'bCoords',
            disabled: p.isMainWindowCopping,
            api: undefined,
            actual: false
        };
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
            if (itemBannerInstance.croppings.preview.windowObject &&
                !itemBannerInstance.croppings.preview.windowObject.closed) {
                itemBannerInstance.croppings.preview.windowObject.focus();
                return;
            }
            win = win.open('', 'preview', 'width=1200,height=1200,resizable=1,scrollbars=1');
            win.document.open();
            win.document.write('<head>');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/itembanner/jquery.Jcrop.css" media="all">');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/itembanner/edit.css" media="all">');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/boxes.css" media="all">');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/lib/jquery/jquery-1.12.0.min.js"></script>');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/local/itembanner/jquery.Jcrop.min.js"></script>');
            win.document.write('</head>');
            win.document.write('<body id="ibw_body">');
            win.document.write('<div id="ib_main_container">');
            win.document.write('<div class="ib_containers">');
            win.document.write('<img id="image_preview_grid" class="ib_crops" src="'+$(element).src+'"/>');
            win.document.write('<h4>');
            win.document.write('<button class="ib_crop_highlight" id="ib_crop_highlight_grid">test</button>');
            win.document.write(outerVariables.gridCroppingWindow);
            win.document.write('<button class="ib_crop_revert" id="ib_crop_revert_grid"></button>');
            win.document.write('</h4>');
            win.document.write('</div>');
            win.document.write('<div class="ib_containers">');
            win.document.write('<img id="image_preview_list" class="ib_crops" src="'+$(element).src+'"/>');
            win.document.write('<h4>');
            win.document.write('<button class="ib_crop_highlight" id="ib_crop_highlight_list">test</button>');
            win.document.write(outerVariables.listCroppingWindow);
            win.document.write('<button class="ib_crop_revert" id="ib_crop_revert_list"></button>');
            win.document.write('</h4>');
            win.document.write('</div>');
            win.document.write('<input type="submit" id="ib_submit" value="' + outerVariables.submitText + '"/>');
            win.document.write('</div>');
            win.document.write('</body>');
            win.document.close();
            if (!!document.documentMode || (!isIE && !!window.StyleMedia)) {
                msCallback();
            } else {
                Event.observe(win, 'load', msCallback);
            }
            var passed = {};
            itemBannerInstance.modes.forEach(function (mode) {
                passed[mode] = false;
            });
            function msCallback(){
                if ($j.isEmptyObject(itemBannerInstance.croppings.preview)) {
                    itemBannerInstance.croppings.preview = new Cropping(win, [workoutHighlightButton, workoutRevertButton]);

                } else {
                    itemBannerInstance.croppings.preview.updateWindowObject(win);
                    itemBannerInstance.modes.forEach(function (mode) {
                        if (itemBannerInstance.relCoords[mode].currentChangeStatus === true ||
                            itemBannerInstance.relCoords[mode].currentChangeStatus !== itemBannerInstance.relCoords[mode].entryChangeStatus) {
                            itemBannerInstance.croppings.preview.jcObjects[mode].actual = false;
                        }
                    });
                }
                itemBannerInstance.croppings.preview.attach();
                itemBannerInstance.modes.forEach(function (mode) {
                    itemBannerInstance.relCoords[mode].entryChangeStatus = itemBannerInstance.relCoords[mode].currentChangeStatus;
                    if (itemBannerInstance.croppings.main.jcObjects[mode].disabled === false) {
                        itemBannerInstance.croppings.main.jcObjects[mode].api.disable();
                    }
                });
                $j( ".ib_containers" ).click(function () {
                    itemBannerInstance.croppings.preview.windowObject.focus();
                });
                $j( ".ib_false_disable" ).click(function () {
                    itemBannerInstance.croppings.preview.windowObject.focus();
                });
                win.document.getElementById("ib_submit").addEventListener("click", function () {
                    itemBannerInstance.modes.forEach(function (mode) {
                        if (itemBannerInstance.relCoords[mode].currentChangeStatus === true ||
                            itemBannerInstance.relCoords[mode].currentChangeStatus !== itemBannerInstance.relCoords[mode].entryChangeStatus) {
                            itemBannerInstance.swapCoordsToFill(mode);
                            Object.keys(itemBannerInstance.croppings).forEach(function (cropping) {
                                itemBannerInstance.croppings[cropping].jcObjects[mode].actual = false;
                            });
                            itemBannerInstance.croppings.main.attach(mode);
                            itemBannerInstance.relCoords[mode].entryChangeStatus = itemBannerInstance.relCoords[mode].currentChangeStatus;
                            passed[mode] = true;
                        }
                    });
                    win.close();
                });
                var container = win.document.getElementsByTagName('div')[0];
                var widthForResize = container.offsetWidth;
                var heightForResize = container.offsetHeight;
                win.resizeTo(widthForResize + 40, heightForResize + 100);
                $j( 'button[class^="ib_crop"]' ).addClass('ib_false_disable');
            }
            Event.observe(win, 'unload', function () {
                itemBannerInstance.modes.forEach(function (mode) {
                    if (passed[mode] === false) {
                        if (itemBannerInstance.relCoords[mode].currentChangeStatus === null && itemBannerInstance.relCoords[mode].entryChangeStatus !== null) {
                            itemBannerInstance.croppings.preview.jcObjects[mode].actual = false;
                        }
                        itemBannerInstance.relCoords[mode].currentChangeStatus = itemBannerInstance.relCoords[mode].entryChangeStatus;
                        if (itemBannerInstance.croppings.main.jcObjects[mode].disabled === false) {
                            itemBannerInstance.croppings.main.jcObjects[mode].api.enable();
                        }
                    }
                    $j( 'button[class^="ib_crop"]' ).removeClass('ib_false_disable');
                });
            });
        }
    }
}

function workoutFreezingButton(cropping, mode) {
    var element = cropping.jq( "#ib_crop_enable_" +  mode);
    var writtenOn = (cropping.jcObjects[mode].disabled) ? outerVariables.frozen : outerVariables.unfrozen
    element.html(writtenOn);
    element.unbind( "click" );
    element.click(function (e) {
        e.preventDefault();
        if(element.html() === outerVariables.frozen) {
            itemBannerInstance.croppings.main.jcObjects[mode].api.enable();
            itemBannerInstance.croppings.main.jcObjects[mode].disabled = false;
            element.html(outerVariables.unfrozen);
        } else {
            itemBannerInstance.croppings.main.jcObjects[mode].api.disable();
            itemBannerInstance.croppings.main.jcObjects[mode].disabled = true;
            element.html(outerVariables.frozen);
        }
    });
}

function workoutHighlightButton(cropping, mode) {
    var element = cropping.jq( "#ib_crop_highlight_" +  mode);
    element.html(outerVariables.highlight);
    element.unbind( "mousedown" );
    element.unbind( "mouseup" );
    var img = cropping.jq( "#image_preview_" +  mode + "+ div").children("img");
    element.mousedown(function (e) {
        img.css('opacity', 0);
    });
    element.mouseup(function (e) {
        img.css('opacity', 0.2);
    });
}

function workoutRevertButton(cropping, mode) {
    var element = cropping.jq( "#ib_crop_revert_" +  mode);
    element.html(outerVariables.revert);
    element.unbind( "click" );
    var visibility = (itemBannerInstance.relCoords[mode].currentChangeStatus === null) ? 'hidden' : 'visible';
    element.css('visibility', visibility);
    element.click(function (e) {
        e.preventDefault();
        itemBannerInstance.relCoords[mode].currentChangeStatus = null;
        element.css('visibility', 'hidden');
        var temp = cropping.jcObjects[mode].coordsForSelect;
        cropping.jcObjects[mode].coordsForSelect = cropping.jcObjects[mode].coordsToFill;
        itemBannerInstance.pullFromOrigRelCoords(mode, cropping.jcObjects[mode].coordsForSelect);
        cropping.jcObjects[mode].actual = false;
        cropping.attach(mode);
        cropping.jcObjects[mode].coordsForSelect = temp;
    });
}

function extendOnclick(onclick) {
    if (typeof outerVariables !== "undefined") {
        if (!$j.isEmptyObject(itemBannerInstance.croppings.preview) &&
            !itemBannerInstance.croppings.preview.windowObject.closed) {
            itemBannerInstance.croppings.preview.windowObject.close();
        }
        itemBannerInstance.uploadRelCoords();
    }
    eval(onclick);
}