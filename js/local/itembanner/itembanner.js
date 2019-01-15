var itemBannerInstance = {
    isIt: undefined,
    coordIdentifiers: undefined,
    imageRelatedJqElements: undefined,
    modes: [],
    relCordsInputIds: {},
    popupInputIds: {},
    relCoords: {},
    aspectRatios: {},
    croppings: {},
    buttonWorkouts: {},
    init: function () {
        var ibi = this;
        ibi.coordIdentifiers = ['x', 'y', 'x2', 'y2', 'w', 'h'];
        ibi.popupInputIds = {
            title: outerVariables.instanceHtmlIdPrefix + '_title',
            description: outerVariables.instanceHtmlIdPrefix + '_description'
        };
        ibi.imageRelatedJqElements = {
            preview: $j( "#" + outerVariables.instanceHtmlIdPrefix + "_image_image" ).parent(),
            file: $j( "#" + outerVariables.instanceHtmlIdPrefix + "_image" ),
            delete: $j( "#" + outerVariables.instanceHtmlIdPrefix + "_image_delete" ),
            containers: $j( ".ib_containers" )
        };
        ibi.modes = ['grid', 'list'];
        ibi.modes.forEach(function (mode) {
            ibi.relCordsInputIds[mode] = outerVariables.instanceHtmlIdPrefix + '_rel_coords_' + mode;
            ibi.relCoords[mode] = {
                original: JSON.parse($j( "#" + ibi.relCordsInputIds[mode] ).val()),
                aCoords: [],
                bCoords: [],
                forPost: 'aCoords',
                currentChangeStatus: null,
                entryChangeStatus:null
            };
            ibi.aspectRatios[mode] = {
                orig: parseFloat($j( "#" + outerVariables.instanceHtmlIdPrefix + "_orig_aspect_ratio_" + mode).val()),
                config: outerVariables[mode + 'AspectRatio']
            }
        });
        ibi.croppings.main = {};
        ibi.croppings.preview = {};
        ibi.buttonWorkouts = {
            freezing: function(cropping, mode) {
            var element = cropping.jq( "#ib_crop_enable_" +  mode),
                writtenOn = (cropping.jcObjects[mode].disabled) ? outerVariables.frozen : outerVariables.unfrozen;
            element.html(writtenOn);
            element.off( "click" );
            element.on('click', {c: cropping, m: mode, e: element}, freezingAction);
            return element;
            },
            highlight: function(cropping, mode) {
                var element = cropping.jq( "#ib_crop_highlight_" +  mode);
                element.html(outerVariables.highlight);
                element.off( "mousedown" );
                element.off( "mouseup" );
                element.on('mousedown', {c: cropping, m: mode, o: 0, a: 'disable'}, highlightAction);
                element.on('mouseup', {c: cropping, m: mode, o: cropping.jcObjects[mode].bgOpacity, a: 'enable'}, highlightAction);
                return element;
            },
            revert: function(cropping, mode) {
                var element = cropping.jq( "#ib_crop_revert_" +  mode);
                element.html(outerVariables.revert);
                element.off( "click" );
                var visibility = (ibi.relCoords[mode].currentChangeStatus === null) ? 'hidden' : 'visible';
                element.css('visibility', visibility);
                if (cropping.jcObjects[mode].disabled === true)
                    element.attr('disabled', 'disabled');
                element.on('click', {c: cropping, m: mode, e:element}, revertAction);
                return element;
            }
        }
    },
    pullFromOrigRelCoords: function (mode, toCoords) {
        var ibi = this;
        var modes = (mode && mode !== false) ? [mode] : ibi.modes;
        modes.forEach(function (mode) {
            if (typeof toCoords === "undefined") {
                ibi.relCoords[mode].aCoords = [];
                ibi.relCoords[mode].bCoords = [];
                ibi.relCoords[mode].original.forEach(function (val, ind) {
                    ibi.relCoords[mode].aCoords[ind] = val;
                    ibi.relCoords[mode].bCoords[ind] = val;
                });
            } else {
                ibi.relCoords[mode][toCoords] = [];
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
            $j( "#" + ibi.relCordsInputIds[mode] ).attr(
                'value',
                JSON.stringify(ibi.relCoords[mode][arrayToUpload])
            );
        });
    },
    swapCoordsToFill: function (mode) {
        this.relCoords[mode].forPost = (this.relCoords[mode].forPost === 'aCoords') ? 'bCoords' : 'aCoords';
        this.croppings.preview.jcObjects[mode].coordsToFill = this.croppings.main.jcObjects[mode].coordsToFill;
        this.croppings.main.jcObjects[mode].coordsToFill = this.relCoords[mode].forPost;
        this.croppings.main.jcObjects[mode].coordsForSelect = this.relCoords[mode].forPost;
        this.croppings.preview.jcObjects[mode].coordsForSelect = this.relCoords[mode].forPost;
    },
    adaptAspectRatio: function (mode, type) {
        var number = this.aspectRatios[mode][type];
        if (typeof number !== "undefined") {
            return 1 / number;
        }
        return null;
    }
};

$j( document ).ready(function () {
    itemBannerInstance.isIt = ($j("#type").val() === 'itembanner/banner');
    if (itemBannerInstance.isIt) {
        var formJq = $j( "#edit_form");
        formJq.attr("enctype", "multipart/form-data" );
        $j( ".scalable.save" ).each(function () {
            var onclick = $j(this).attr('onclick');
            onclick = 'extendOnclick("' + onclick + '")';
            $j(this).attr('onclick', onclick);
        });

        if (typeof outerVariables !== "undefined") {
            itemBannerInstance.init();
            $j( "#widget_instace_tabs_properties_section" ).one('click', function () {
                itemBannerInstance.pullFromOrigRelCoords();
                itemBannerInstance.croppings.main = new Cropping(false, ['freezing', 'revert']);
                itemBannerInstance.croppings.main.workoutButtons();
                itemBannerInstance.croppings.main.manage();
                $j( ".content-header-floating").css('z-index', 601);
            });
            itemBannerInstance.imageRelatedJqElements.file.on('change', {e: itemBannerInstance.imageRelatedJqElements.file}, saveDialogOne);
            $j( window ).unload(closePreviewWindowIfOpened);
            $j( "#" + outerVariables.instanceHtmlIdPrefix + "_link" ).addClass('validate-url');
            $j( "#" + outerVariables.instanceHtmlIdPrefix + "_position_in_grid" ).addClass('validate-digits');
            $j( "#" + outerVariables.instanceHtmlIdPrefix + "_position_in_list" ).addClass('validate-digits');
            Validation.addAllThese([
                ['validate-inner-text-length', 'The text here is not allowed to have more than 300 characters', function(v, elm) {
                    var reMax = new RegExp(/^maximum-length-[0-9]+$/);
                    var result = true;
                    var iframe = document.getElementById(outerVariables.instanceHtmlIdPrefix + '_description_ifr');
                    var wyz = (iframe === null) ? iframe : iframe.contentWindow.document.getElementById('tinymce');
                    $w(elm.className).each(function(name, index) {
                        if (name.match(reMax) && result) {
                            var length = name.split('-')[2];
                            if (wyz === null) {
                                var text = elm.value.stripTags();
                                result = (text.length <= length)
                            } else {
                                result = (wyz.innerText.length <= length)
                            }
                        }
                    });
                    return result;
                }]
            ]);

        }
    }
});

function Cropping(currentWindow, buttons) {

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

    this.calculateSelection = function (mode, fromRelCoordsOf) {
        var coords = itemBannerInstance.relCoords[mode][fromRelCoordsOf];
        if (coords[4] > 0 && coords[5] > 0) {
            var selectionArray = [];
            var dimension = 'width';
            for (var i = 0; i < 4; i++) {
                selectionArray.push(coords[i] * p.image[dimension]);
                dimension = (dimension === 'width') ? 'height' : 'width';
            }
        }
        return selectionArray;
    };

    this.onTrueSelect = function (mode) {
        p.jq( "#ib_crop_revert_" +  mode).css('visibility', 'visible');
        itemBannerInstance.relCoords[mode].currentChangeStatus = true;
    };

    this.workoutButtons = function (hereMode) {
        var hereModes = (!hereMode) ? modes : [hereMode];
        hereModes.forEach(function (mode) {
            buttons.forEach(function (button) {
                var jqObject =itemBannerInstance.buttonWorkouts[button](p, mode);
                p.jcObjects[mode].buttons[button] = {
                    jqObject: jqObject,
                    attrDisabled: jqObject.attr('disabled'),
                    pseudoDisabled: false
                };
            });
        });
    };

    this.manage = function (noAttach, hereMode, callback) {
        var hereModes = (!hereMode) ? modes : [hereMode];
        hereModes.forEach(function (mode) {
            if (p.jcObjects[mode].isSelectionActual === false) {
                p.jcObjects[mode].manageSelection();
            }
            if (noAttach !== true) {
                p.jq( p.jcObjects[mode].imageDomId ).Jcrop(
                    p.jcObjects[mode],
                    function () {
                        p.jcObjects[mode].api = this;
                    }
                );
            }
            if (typeof p.jcObjects[mode].selection !== "undefined") {
                p.jcObjects[mode].api.animateTo(p.jcObjects[mode].selection, afterActions);
                if (itemBannerInstance.relCoords[mode].currentChangeStatus !== null) {
                    p.jq( "#ib_crop_revert_" +  mode).css('visibility', 'visible');
                }
            } else {
                p.jcObjects[mode].api.release();
                afterActions();
            }

            function afterActions() {
                if (p.jcObjects[mode].disabled === true) {
                    p.jcObjects[mode].api.disable();
                }
                var onClickElement = p.jq( p.jcObjects[mode].imageDomId ).next( "div.jcrop-holder" );
                if (itemBannerInstance.aspectRatios[mode].orig !== itemBannerInstance.aspectRatios[mode].config) {
                    onClickElement.on('mouseup', {c: p, m: mode, o: onClickElement}, adjustAspectRatio)
                } else {
                    onClickElement.off('mouseup', adjustAspectRatio)
                }
                p.jcObjects[mode].isSelectionActual = true;
                if (itemBannerInstance.relCoords[mode].currentChangeStatus === true) {
                    itemBannerInstance.relCoords[mode].currentChangeStatus = false;
                }
                if (callback) {
                    callback();
                }
            }
        });
    };

    modes.forEach(function (mode) {
        p.jcObjects[mode] = {
            onSelect: function (c) {
                if (c[coordIdentifiers[4]] * c[coordIdentifiers[5]] > p.image.minSquare) {
                    if (p.jcObjects[mode].isSelectionActual === true) {
                        p.onTrueSelect(mode);
                        var dimension = 'width';
                        for (var i = 0; i < 6; i++) {
                            itemBannerInstance.relCoords[mode][p.jcObjects[mode].coordsToFill][i] = c[coordIdentifiers[i]] / p.image[dimension];
                            dimension = (dimension === 'width') ? 'height' : 'width';
                        }
                    }
                } else {
                    p.windowObject.alert('The cropping square is not large enough!');
                    p.jcObjects[mode].api.release();
                }
            },
            onRelease: function () {
                if (p.jcObjects[mode].isSelectionActual === true) {
                    p.onTrueSelect(mode);
                    itemBannerInstance.relCoords[mode][p.jcObjects[mode].coordsToFill] = [];
                }
            },
            manageSelection: function () {
                var selectionArray = p.calculateSelection(mode, p.jcObjects[mode].coordsForSelect);
                if (selectionArray) {
                    p.jcObjects[mode].selection = selectionArray;
                } else {
                    delete p.jcObjects[mode].selection;
                }
            },
            toggleButtonsPseudoDisable: function () {
                var buttonsProp = p.jcObjects[mode].buttons;
                Object.keys(buttonsProp).forEach(function (buttonName) {
                    if (buttonsProp[buttonName].pseudoDisabled === false) {
                        buttonsProp[buttonName].jqObject.off('click').on('click', function (event) {
                            event.preventDefault();
                        }).addClass('ib_pseudo_disable');
                        if(buttonsProp[buttonName].attrDisabled) {
                            buttonsProp[buttonName].jqObject.removeAttr('disabled');
                        }
                        buttonsProp[buttonName].pseudoDisabled = true;
                    } else {
                        buttonsProp[buttonName].jqObject.off('click').on(
                            'click',
                            {c: itemBannerInstance.croppings.main, m: mode, e: buttonsProp[buttonName].jqObject},
                            eval(buttonName + 'Action')
                        ).removeClass('ib_pseudo_disable');
                        if(buttonsProp[buttonName].attrDisabled) {
                            buttonsProp[buttonName].jqObject.attr('disabled', 'disabled');
                        }
                        buttonsProp[buttonName].pseudoDisabled = false;
                    }
                });

                return p.jcObjects[mode];
            },
            toggleApiDisable: function (fulfill) {
                if (p.jcObjects[mode].disabled === false) {
                    p.jcObjects[mode].api[fulfill]();
                }
            },
            bgColor: 'transparent',
            bgOpacity: .2,
            aspectRatio: itemBannerInstance.adaptAspectRatio(mode, 'orig'),
            selection: undefined,
            coordsForSelect: 'aCoords',
            coordsToFill: (p.isMainWindowCopping) ? 'aCoords' : 'bCoords',
            disabled: p.isMainWindowCopping,
            api: undefined,
            buttons: {},
            imageDomId: "#image_preview_" + mode,
            isSelectionActual: false
        };
    });
}

function imagePreview(element){
    if($(element)){
        var win = window;
        if(!itemBannerInstance.isIt) {
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
            else if (itemBannerInstance.imageRelatedJqElements.file.val() !== '') {
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
            win.document.write('<input type="submit" id="ib_cancel" value="' + outerVariables.cancelText + '" autofocus="autofocus"/>');
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
                    itemBannerInstance.croppings.preview = new Cropping(win, ['highlight', 'revert']);
                    itemBannerInstance.croppings.preview.workoutButtons();
                    itemBannerInstance.modes.forEach(function (mode) {
                        itemBannerInstance.croppings.preview.jcObjects[mode].aspectRatio = itemBannerInstance.croppings.main.jcObjects[mode].aspectRatio
                    })
                } else {
                    itemBannerInstance.croppings.preview.updateWindowObject(win);
                    itemBannerInstance.modes.forEach(function (mode) {
                        if (itemBannerInstance.relCoords[mode].currentChangeStatus === true ||
                            itemBannerInstance.relCoords[mode].currentChangeStatus !== itemBannerInstance.relCoords[mode].entryChangeStatus) {
                            itemBannerInstance.croppings.preview.jcObjects[mode].isSelectionActual = false;
                        }
                        itemBannerInstance.croppings.preview.workoutButtons(mode);
                        itemBannerInstance.croppings.preview.jcObjects[mode].aspectRatio = itemBannerInstance.croppings.main.jcObjects[mode].aspectRatio
                    });
                }
                itemBannerInstance.modes.forEach(function (mode) {
                    itemBannerInstance.croppings.preview.manage(false, mode, function () {
                        itemBannerInstance.relCoords[mode].entryChangeStatus = itemBannerInstance.relCoords[mode].currentChangeStatus;
                        itemBannerInstance.croppings.main.jcObjects[mode].toggleButtonsPseudoDisable().toggleApiDisable('disable');
                    });
                });
                var container = win.document.getElementsByTagName('div')[0];
                var widthForResize = container.offsetWidth;
                var heightForResize = container.offsetHeight;
                win.resizeTo(widthForResize + 40, heightForResize + 100);
                Object.keys(itemBannerInstance.imageRelatedJqElements).forEach(function (element) {
                    itemBannerInstance.imageRelatedJqElements[element].on('click', bringPreviewWindowForward);
                });
                win.document.getElementById("ib_submit").addEventListener("click", function () {
                    win.close();
                    itemBannerInstance.modes.forEach(function (mode) {
                        if (itemBannerInstance.relCoords[mode].currentChangeStatus === true ||
                            itemBannerInstance.relCoords[mode].currentChangeStatus !== itemBannerInstance.relCoords[mode].entryChangeStatus) {
                            itemBannerInstance.swapCoordsToFill(mode);
                            Object.keys(itemBannerInstance.croppings).forEach(function (cropping) {
                                itemBannerInstance.croppings[cropping].jcObjects[mode].isSelectionActual = false;
                            });
                            itemBannerInstance.croppings.main.jcObjects[mode].aspectRatio = itemBannerInstance.croppings.preview.jcObjects[mode].aspectRatio;
                            itemBannerInstance.croppings.main.manage(true, mode, function () {
                                itemBannerInstance.relCoords[mode].entryChangeStatus = itemBannerInstance.relCoords[mode].currentChangeStatus;
                                passed[mode] = true;
                            });
                        }
                    });
                });
                win.document.getElementById("ib_cancel").addEventListener("click", function () {
                    win.close();
                });
            }
            Event.observe(win, 'unload', function () {
                itemBannerInstance.modes.forEach(function (mode) {
                    if (passed[mode] === false) {
                        if (itemBannerInstance.relCoords[mode].currentChangeStatus === null && itemBannerInstance.relCoords[mode].entryChangeStatus !== null) {
                            itemBannerInstance.croppings.preview.jcObjects[mode].isSelectionActual = false;
                        }
                        itemBannerInstance.relCoords[mode].currentChangeStatus = itemBannerInstance.relCoords[mode].entryChangeStatus;
                        itemBannerInstance.croppings.main.jcObjects[mode].toggleApiDisable('enable');
                    }
                    itemBannerInstance.croppings.main.jcObjects[mode].toggleButtonsPseudoDisable();
                    var returnVisibility = (itemBannerInstance.relCoords[mode].currentChangeStatus === null) ? 'hidden' : 'visible';
                    itemBannerInstance.croppings.main.jcObjects[mode].buttons.revert.jqObject.css('visibility', returnVisibility);
                });
                Object.keys(itemBannerInstance.imageRelatedJqElements).forEach(function (element) {
                    itemBannerInstance.imageRelatedJqElements[element].off('click', bringPreviewWindowForward);
                });
            });
        }
    }
}

function freezingAction(event) {
    event.preventDefault();
    var cropping = event.data.c,
        mode = event.data.m,
        element = event.data.e,
        revertButton = cropping.jq( "#ib_crop_revert_" +  mode);
    if(element.html() === outerVariables.frozen) {
        cropping.jcObjects[mode].api.enable();
        cropping.jcObjects[mode].disabled = false;
        cropping.jcObjects[mode].buttons.revert.attrDisabled = false;
        element.html(outerVariables.unfrozen);
        revertButton.removeAttr('disabled');
    } else {
        cropping.jcObjects[mode].api.disable();
        cropping.jcObjects[mode].disabled = true;
        cropping.jcObjects[mode].buttons.revert.attrDisabled = true;
        element.html(outerVariables.frozen);
        revertButton.attr('disabled', 'disabled');
    }
}

function highlightAction(event) {
    event.preventDefault();
    var cropping = event.data.c,
        mode = event.data.m;
    if (itemBannerInstance.relCoords[mode][cropping.jcObjects[mode].coordsToFill].length > 0) {
        var opacity = event.data.o,
            action = event.data.a,
            img = cropping.jq( "#image_preview_" +  mode + "+ div").children("img");
        img.css('opacity', opacity);
        cropping.jcObjects[mode].api[action]();
    }
}

function revertAction (event) {
    event.preventDefault();
    var cropping = event.data.c,
        mode = event.data.m,
        element = event.data.e;
    itemBannerInstance.relCoords[mode].currentChangeStatus = null;
    element.css('visibility', 'hidden');
    var temp = cropping.jcObjects[mode].coordsForSelect;
    cropping.jcObjects[mode].coordsForSelect = cropping.jcObjects[mode].coordsToFill;
    itemBannerInstance.pullFromOrigRelCoords(mode, cropping.jcObjects[mode].coordsForSelect);
    cropping.jcObjects[mode].aspectRatio = itemBannerInstance.adaptAspectRatio(mode, 'orig');
    cropping.jcObjects[mode].isSelectionActual = false;
    cropping.manage(true, mode, function () {
        cropping.jcObjects[mode].coordsForSelect = temp;
    });
}

function bringPreviewWindowForward(event) {
    event.preventDefault();
    itemBannerInstance.croppings.preview.windowObject.focus();
}

function saveDialogOne(event) {
    var element = event.data.e;
    if (element.val() === '') {
        itemBannerInstance.modes.forEach(function (mode) {
            itemBannerInstance.croppings.main.jcObjects[mode].toggleButtonsPseudoDisable().toggleApiDisable('enable');
        });
        Object.keys(itemBannerInstance.imageRelatedJqElements).forEach(function (element) {
            if (element === 'file') {
                return;
            }
            itemBannerInstance.imageRelatedJqElements[element].off('click', saveDialogTwo);
        });
        $j("span.ui-dialog-title").text('Change Image?');
        return;
    }
    var dialogDiv = $j( "#save_dialog" );
    if (dialogDiv.length === 0) {
        dialogDiv = $j( "<div/>", {
            id: 'save_dialog'
        });
    }
    dialogDiv.attr('title', 'Change Image?')
        .html('Would You like to proceed with the new image?(The instance will be saved!)');
    dialogDiv.dialog({
        create: function (event, ui) {
            $j( ".ui-dialog" ).css('z-index', 602);
        },
        open: function (event, ui) {
            $j("body").css({ overflow: 'hidden' })
        },
        beforeClose: function(event, ui) {
            $j("body").css({ overflow: 'inherit' })
        },
        close: cancelDialog,
        autoOpen: true,
        modal: true,
        buttons: [{
            text: "Ok",
            icon: "ui-icon-circle-check",
            click: function () {
                $j( "#" + outerVariables.instanceHtmlIdPrefix + "_is_active" ).val(0);
                saveAndContinueEdit();
                $j( this ).dialog( "close" );
            }
        },
        {
            text: "Cancel",
            icon: "ui-icon-cancel",
            click: function () {
                $j( this ).dialog( "close" );
            }
        }]
    });

    function cancelDialog() {
        itemBannerInstance.modes.forEach(function (mode) {
            itemBannerInstance.croppings.main.jcObjects[mode].toggleButtonsPseudoDisable().toggleApiDisable('disable');
        });
        Object.keys(itemBannerInstance.imageRelatedJqElements).forEach(function (element) {
            if (element === 'file') {
                return;
            }
            itemBannerInstance.imageRelatedJqElements[element].on('click', {e: dialogDiv}, saveDialogTwo);
        });
    }
}

function saveDialogTwo(event) {
    event.preventDefault();
    var dialogDiv = event.data.e;
    dialogDiv.html('Please chose the Image You would like to precede to cropping with');
    $j("span.ui-dialog-title").text('Chose Image');
    dialogDiv.dialog({
        width: "auto",
        close: undefined,
        buttons: [{
            text: "Current Image!",
            icon: "ui-icon-circle-check",
            click: function () {
                itemBannerInstance.imageRelatedJqElements.file.val('');
                $j("span.ui-dialog-title").text('Change Image?');
                itemBannerInstance.modes.forEach(function (mode) {
                    itemBannerInstance.croppings.main.jcObjects[mode].toggleButtonsPseudoDisable().toggleApiDisable('enable');
                });
                Object.keys(itemBannerInstance.imageRelatedJqElements).forEach(function (element) {
                    if (element === 'file') {
                        return;
                    }
                    itemBannerInstance.imageRelatedJqElements[element].off('click', saveDialogTwo);
                });
                $j( this ).dialog( "close" );
            }
        },
        {
            text: "New Image",
            icon: "ui-icon-circle-check",
            click: function () {
                saveAndContinueEdit();
                $j( this ).dialog( "close" );
            }
        },
        {
            text: "Neither of two",
            icon: "ui-icon-cancel",
            click: function () {
                $j( this ).dialog( "close" );
                $j("span.ui-dialog-title").text('Cropping Banned!');
                dialogDiv.html("Please notice!<br/>In order to fulfill any cropping You will first need to chose between the images.");
                dialogDiv.dialog({
                    close: undefined,
                    buttons: [{
                        text: "Ok",
                        icon: "ui-icon-circle-check",
                        click: function () {
                            $j( this ).dialog( "close" );
                        }
                    }]
                });
            }
        }]
    });
}

function adjustAspectRatio(event) {
    var cropping = event.data.c;
    var mode = event.data.m;
    var onclickElement = event.data.o;
    var jcObject = cropping.jcObjects[mode];
    if (jcObject.disabled === false) {
        jcObject.isSelectionActual = false;
        jcObject.aspectRatio = itemBannerInstance.adaptAspectRatio(mode, 'config');
        itemBannerInstance.relCoords[mode].currentChangeStatus = true;
        cropping.manage(true, mode, function () {
            onclickElement.off('mouseup', adjustAspectRatio);
            cropping.windowObject.alert('Please notice!\nThe aspect ratio for this mode has been changed in configuration!');
        });
    }
}

function closePreviewWindowIfOpened() {
    if (!$j.isEmptyObject(itemBannerInstance.croppings.preview) &&
        !itemBannerInstance.croppings.preview.windowObject.closed) {
        itemBannerInstance.croppings.preview.windowObject.close();
    }
}

function extendOnclick(onclick) {
    if(!$j.isEmptyObject(itemBannerInstance.croppings.main)) {
        if ($j( "#" + outerVariables.instanceHtmlIdPrefix + "_is_active" ).val() === '1') {
            var areForPostCoordsEmpty = false;
            var errorMessage = 'The banner can not be activatet because';
            itemBannerInstance.modes.forEach(function (mode) {
                var forPostCoords = itemBannerInstance.relCoords[mode].forPost;
                if (itemBannerInstance.relCoords[mode][forPostCoords].length === 0) {
                    errorMessage = errorMessage  + '\nThe ' + mode + ' mode cropping is not defined' + ',';
                    areForPostCoordsEmpty = true;
                }
                $j( "#" + outerVariables.instanceHtmlIdPrefix + "_orig_aspect_ratio_" + mode).val(itemBannerInstance.aspectRatios[mode].config);
            });
            if (areForPostCoordsEmpty) {
                errorMessage = errorMessage.slice(0, -1) + '.';
                alert(errorMessage);
                $j( "#" + outerVariables.instanceHtmlIdPrefix + "_is_active" ).val('0');
                return;
            }
        }
        closePreviewWindowIfOpened();
        itemBannerInstance.uploadRelCoords();
    }
    eval(onclick);
}