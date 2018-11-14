var itemBannerInstance = {
    result: 'undefined',
    inputs: 'undefined',
    figureOutIsIt: function () {
        if (this.result === "undefined") {
            $j( document ).ready(function (p) {
                p.result = ($j( "#type" ).val() === 'itembanner/banner')
                p.inputs = {
                    "grid": ['x1_grid', 'y1_grid', 'x2_grid', 'y2_grid', 'w_grid', 'h_grid'],
                    "list": ['x1_list', 'y1_list', 'x2_list', 'y2_list', 'w_list', 'h_list'],
                    "img": ['w_img', 'h_img', 'src_img'],
                    "all": ['x1_grid', 'y1_grid', 'x2_grid', 'y2_grid', 'w_grid', 'h_grid', 'x1_list', 'y1_list', 'x2_list', 'y2_list', 'w_list', 'h_list', 'w_img', 'h_img', 'src_img']
                };
            }(this));
        }
        return this.result;
  }
};

$j( document ).ready(function () {
    if (itemBannerInstance.figureOutIsIt()) {
        var formJq = $j( "#edit_form");
        formJq.attr("enctype", "multipart/form-data" );
        itemBannerInstance.inputs.all.forEach(function (identifier) {
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
            win.document.write('<script>var gridAspectRatio = gridAspectRatio; var listAspectRatio = listAspectRatio;</script>');
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
            win.document.write('<form>');
            itemBannerInstance.inputs.all.forEach(function (identifier) {
                var value = document.getElementById(identifier).getAttribute('value');
                win.document.write('<input type="text" id="' + identifier + '" value="' + value + '">');
            });
            win.document.write('<input type="submit" style="margin-top: 20px; width: 300px; height: 30px;"/></form>');
            win.document.write('</div>');
            win.document.write('</body>');
            win.document.close();
            Event.observe(win, 'load', function(){
                win.document.getElementById('w_img').setAttribute(
                    'value',
                    win.document.getElementById('image_preview_list').width
                );
                win.document.getElementById('h_img').setAttribute(
                    'value',
                    win.document.getElementById('image_preview_grid').height
                );
                win.document.getElementById('src_img').setAttribute(
                    'value',
                    $(element).src
                );
                var container = win.document.getElementsByTagName('div')[0];
                var widthForResize = container.offsetWidth;
                var heightForResize = container.offsetHeight;
                win.resizeTo(widthForResize + 40, heightForResize + 100);
            });
            Event.observe(win, 'submit', function(){
                if (itemBannerInstance.figureOutIsIt()) {
                    var editForm = document.getElementById('edit_form');
                    var wGrid = win.document.getElementById(itemBannerInstance.inputs.grid[4]).value;
                    var hGrid = win.document.getElementById(itemBannerInstance.inputs.grid[5]).value;
                    var wList = win.document.getElementById(itemBannerInstance.inputs.list[4]).value;
                    var hList = win.document.getElementById(itemBannerInstance.inputs.list[5]).value;
                    if (wGrid * hGrid) {
                        itemBannerInstance.inputs.grid.forEach(scatterValues);
                    }

                    if (wList * hList) {
                        itemBannerInstance.inputs.list.forEach(scatterValues);
                    }

                    function scatterValues(identifier) {
                        var input = document.getElementById(identifier);
                        input.setAttribute('value', win.document.getElementById(identifier).value);
                        editForm.appendChild(input);
                    }

                    win.close();
                }
            });
        }
    }
}
