var isItemBannerInstance = {
    result: 'undefined',
    figureOut: function () {
        if (this.result === 'undefined') {
            $j( document ).ready(function (p) {
                p.result = ($j( "#type" ).val() === 'itembanner/banner')
            }(this));
        }
        return this.result;
  }
};

$j( document ).ready(function () {
    if (isItemBannerInstance.figureOut) {
        $j( "#edit_form").attr("enctype", "multipart/form-data" );
    }
});

function imagePreview(element){
    if($(element)){
        var win = window;
        if(!isItemBannerInstance.figureOut) {
            win = win.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
            win.document.open();
            win.document.write('<body style="padding:0;margin:0"><img src="'+$(element).src+'" id="image_preview"/></body>');
            win.document.close();
            Event.observe(win, 'load', function(){
                var img = win.document.getElementById('image_preview');
                win.resizeTo(img.width+40, img.height+80);
            });
        } else {
            win = win.open('', 'preview', 'width=1200,height=1200,resizable=no,scrollbars=1');
            win.document.open();
            win.document.write('<head>');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/itembanner/jquery.Jcrop.css" media="all">');
            win.document.write('<style>* {box-sizing: border-box;}</style>');
            win.document.write('</head>');
            win.document.write('<body id="body" style="background-color: aliceblue; padding:0; margin:0;">');
            win.document.write('<div style=" text-align: center; width: 1200px; height: auto; margin: 10px;">');
            win.document.write('<div style="width: 49.5%; margin-right: 1%; border: solid black 1px; float: left;">');
            win.document.write('<img style="width: 100%;" src="'+$(element).src+'" id="image_preview_grid"/>');
            win.document.write('<h4>Crop for Grid Mode</h4>');
            win.document.write('</div>');
            win.document.write('<div style="width: 49.5%; border: solid black 1px; float: left;">');
            win.document.write('<img style="width: 100%;" src="'+$(element).src+'" id="image_preview_list"/>');
            win.document.write('<h4>Crop for List Mode</h4>');
            win.document.write('</div>');
            win.document.write('<form action="http://review3.school.com/index.php/admin/widget_cropper/crop/form_key/' + FORM_KEY + '" class="coords" method="post">' +
                '<input type="hidden" size="4" id="x1_grid" name="x1_grid"></label>' +
                '<input type="hidden" size="4" id="y1_grid" name="y1_grid"></label>' +
                '<input type="hidden" size="4" id="x2_grid" name="x2_grid"></label>' +
                '<input type="hidden" size="4" id="y2_grid" name="y2_grid"></label>' +
                '<input type="hidden" size="4" id="w_grid" name="w_grid"></label>' +
                '<input type="hidden" size="4" id="h_grid" name="h_grid"></label>' +
                '<input type="hidden" size="4" id="x1_list" name="x1_list"></label>' +
                '<input type="hidden" size="4" id="y1_list" name="y1_list"></label>' +
                '<input type="hidden" size="4" id="x2_list" name="x2_list"></label>' +
                '<input type="hidden" size="4" id="y2_list" name="y2_list"></label>' +
                '<input type="hidden" size="4" id="w_list" name="w_list"></label>' +
                '<input type="hidden" size="4" id="h_list" name="h_list"></label>' +
                '<input type="submit" style="margin-top: 20px; width: 300px; height: 30px;"/>' +
                '</form>');
            win.document.write('</div>');
            win.document.write('</body>');
            win.document.close();
            Event.observe(win, 'load', function(){
                var body = win.document.getElementById('body');
                var scriptJq = win.document.createElement('script');
                scriptJq.setAttribute('type', 'text/javascript');
                scriptJq.setAttribute('src', 'http://review3.school.com/js/lib/jquery/jquery-1.12.0.min.js');
                body.appendChild(scriptJq);
                var scriptJcrop = win.document.createElement('script');
                scriptJcrop.setAttribute('type', 'text/javascript');
                scriptJcrop.setAttribute('src', 'http://review3.school.com/js/local/itembanner/jquery.Jcrop.min.js');
                body.appendChild(scriptJcrop);
                var scriptCropper = win.document.createElement('script');
                scriptCropper.setAttribute('type', 'text/javascript');
                scriptCropper.setAttribute('src', 'http://review3.school.com/js/local/itembanner/cropper.js');
                body.appendChild(scriptCropper);
                var container = win.document.getElementsByTagName('div')[0];
                var widthForResize = container.offsetWidth;
                var heightForResize = container.offsetHeight;
                win.resizeTo(widthForResize + 40, heightForResize + 100);
            });
            Event.observe(win, 'unload', function(){
               win.close();
            });
        }
    }
}
