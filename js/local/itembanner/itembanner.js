var itemBannerInstance = {
    result: 'undefined',
    inputs: 'undefined',
    figureOutIsIt: function () {
        if (this.result === "undefined") {
            $j( document ).ready(function (p) {
                p.result = ($j( "#type" ).val() === 'itembanner/banner');
                p.inputs = {
                    baseArray: ['x1', 'y1', 'x2', 'y2', 'w', 'h'],
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
    }
});

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
            win = win.open('', 'preview', 'width=1200,height=1200,resizable=1,scrollbars=1');
            win.document.open();
            win.document.write('<head>');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/itembanner/jquery.Jcrop.css" media="all">');
            var gridInputs = JSON.stringify(itemBannerInstance.getFormatedIdentifiers('grid', true));
            var listInputs = JSON.stringify(itemBannerInstance.getFormatedIdentifiers('list', true));
            win.document.write('<script>var gridAspectRatio = ' + gridAspectRatio +'; var listAspectRatio = ' + listAspectRatio +';</script>');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/lib/jquery/jquery-1.12.0.min.js"></script>');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/local/itembanner/jquery.Jcrop.min.js"></script>');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/local/itembanner/cropper.js"></script>');
            win.document.write('</head>');
            win.document.write('<body id="body" style="background-color: aliceblue; padding:0; margin:0;">');
            win.document.write('<div style="text-align: center; width: 1200px; height: auto; margin: 10px;">');
            win.document.write('<div style="width: 49.5%; margin-right: 1%; border: solid black 1px; float: left; box-sizing: border-box;">');
            win.document.write('<img style="width: 100%;" src="'+$(element).src+'" id="image_preview_grid"/>');
            win.document.write('<h4>' + gridCroppingWindow + '</h4>');
            win.document.write('</div>');
            win.document.write('<div style="width: 49.5%; border: solid black 1px; float: left; box-sizing: border-box;">');
            win.document.write('<img style="width: 100%;" src="'+$(element).src+'" id="image_preview_list"/>');
            win.document.write('<h4>' + listCroppingWindow + '</h4>');
            win.document.write('</div>');
            win.document.write('<form id="preview_form">');
            gridInputs = itemBannerInstance.getFormatedIdentifiers('grid');
            listInputs = itemBannerInstance.getFormatedIdentifiers('list');
            var inputsToManage = gridInputs.concat(listInputs);
            inputsToManage.forEach(function (identifier) {
                var value = document.getElementById(identifier).getAttribute('value');
                win.document.write('<input type="text" id="' + identifier + '" value="' + value + '">');
            });
            win.document.write('<input type="submit" style="margin-top: 20px; width: 300px; height: 30px;"/></form>');
            win.document.write('</div>');
            win.document.write('</body>');
            win.document.close();
            Event.observe(win, 'load', function(){
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
            });
            Event.observe(win, 'submit', function(){
                var wGrid = win.document.getElementById(gridInputs[4]).value;
                var hGrid = win.document.getElementById(gridInputs[5]).value;
                var wList = win.document.getElementById(listInputs[4]).value;
                var hList = win.document.getElementById(listInputs[5]).value;

                if (wGrid * hGrid) {
                    gridInputs.forEach(scatterValues);
                } else {
                    gridInputs.forEach(annulValues);
                }

                if (wList * hList) {
                    listInputs.forEach(scatterValues);
                } else {
                    listInputs.forEach(annulValues);
                }

                Object.keys(itemBannerInstance.inputs.img).forEach(scatterValues);

                function scatterValues(identifier) {
                    var input = document.getElementById(identifier);
                    input.setAttribute('value', win.document.getElementById(identifier).value);
                }

                function annulValues(identifier) {
                    var input = document.getElementById(identifier);
                    input.setAttribute('value', null);
                }

                win.close();
            });
        }
    }
}
