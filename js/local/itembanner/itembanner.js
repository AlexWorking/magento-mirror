
$j( document ).ready(function () {
    var instanceType = $j( "#type" ).val();
    if (instanceType == 'itembanner/banner') {
        $j( "#edit_form").attr("enctype", "multipart/form-data" );
    }
});

function imagePreview(element){
    if($(element)){
        var instanceType = $j( "#type" ).val();
        var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
        win.document.open();
        if(instanceType != 'itembanner/banner') {
            win.document.write('<body style="padding:0; margin:0">');
            win.document.write('<img src="'+$(element).src+'" id="image_preview"/>');
            win.document.write('</body>');
        } else {
            win.document.write('<head>');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/lib/jquery/jquery-1.12.0.min.js"></script>');
            win.document.write('<script type="text/javascript" src="http://review3.school.com/js/local/itembanner/jquery.Jcrop.min.js"></script>');
            win.document.write('<script language="Javascript"  src="http://review3.school.com/js/local/itembanner/cropper.js"></script>');
            win.document.write('<link rel="stylesheet" type="text/css" href="http://review3.school.com/skin/adminhtml/default/default/itembanner/jquery.Jcrop.css" media="all">');
            win.document.write('</head>');
            win.document.write('<body style="padding:0; margin:0">');
            win.document.write('<img src="'+$(element).src+'" id="image_preview"/>');
            win.document.write('<form action="http://review3.school.com/index.php/admin/widget_cropper/crop/form_key/LNTKyRfLSrg8IkhX" class="coords" method="post">' +
                '<label>X1 <input type="text" size="4" id="x1" name="x1"></label>' +
                '<label>Y1 <input type="text" size="4" id="y1" name="y1"></label>' +
                '<label>X2 <input type="text" size="4" id="x2" name="x2"></label>' +
                '<label>Y2 <input type="text" size="4" id="y2" name="y2"></label>' +
                '<label>W <input type="text" size="4" id="w" name="w"></label>' +
                '<label>H <input type="text" size="4" id="h" name="h"></label>' +
                '<input type="submit">Crop</input>' +
                '</form>');
            win.document.write('</body>');
        }
        win.document.close();
        Event.observe(win, 'load', function(){
            var img = win.document.getElementById('image_preview');
            win.resizeTo(800, 800);
        });
    }
}