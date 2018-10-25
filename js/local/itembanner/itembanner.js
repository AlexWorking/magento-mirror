
$j( document ).ready(function () {
    var instanceType = $j( "#type" ).val();
    if (instanceType == 'itembanner/banner') {
        $j( "#edit_form").attr("enctype", "multipart/form-data" );
        //$j('#options_fieldsetf52c35a35d6a9f4c920e65f06998a7c7_image_image').Jcrop();
    }
});

function imagePreview(element){
    if($(element)){
        var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
        win.document.open();
        win.document.write('<body style="padding:0; margin:0">');
        win.document.write('<img src="'+$(element).src+'" id="image_preview"/>');
        win.document.write('</body>');
        win.document.close();
        Event.observe(win, 'load', function(){
            var img = win.document.getElementById('image_preview');
            win.resizeTo(800, 800);
        });
    }
}