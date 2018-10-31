var isItemBannerInstance = {
  calculate: function () {
      if (this.result === 'undefined') {
          this.result = $j( document ).ready(function () {
              return ($j( "#type" ).val() === 'itembanner/banner')
          });
          return this.result;
      }
  }
};

$j( document ).ready(function () {
    if (isItemBannerInstance) {
        $j( "#edit_form").attr("enctype", "multipart/form-data" );
    }
});

function imagePreview(element){
    if($(element)){
        var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
        win.document.open();
        if(!isItemBannerInstance) {
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
            win.document.write('<div>');
            win.document.write('<img src="'+$(element).src+'" id="image_preview_grid"/>');
            win.document.write('</div>');
            win.document.write('<div>');
            win.document.write('<img src="'+$(element).src+'" id="image_preview_list"/>');
            win.document.write('</div>');
            win.document.write('<form action="http://review3.school.com/index.php/admin/widget_cropper/crop/form_key/' + FORM_KEY + '" class="coords" method="post">' +
                '<label>X1_grid <input type="text" size="4" id="x1_grid" name="x1_grid"></label>' +
                '<label>Y1_grid <input type="text" size="4" id="y1_grid" name="y1_grid"></label>' +
                '<label>X2_grid <input type="text" size="4" id="x2_grid" name="x2_grid"></label>' +
                '<label>Y2_grid <input type="text" size="4" id="y2_grid" name="y2_grid"></label>' +
                '<label>W_grid <input type="text" size="4" id="w_grid" name="w_grid"></label>' +
                '<label>H_grid <input type="text" size="4" id="h_grid" name="h_grid"></label>' +
                '<label>X1_list <input type="text" size="4" id="x1_list" name="x1_list"></label>' +
                '<label>Y1_list <input type="text" size="4" id="y1_list" name="y1_list"></label>' +
                '<label>X2_list <input type="text" size="4" id="x2_list" name="x2_list"></label>' +
                '<label>Y2_list <input type="text" size="4" id="y2_list" name="y2_list"></label>' +
                '<label>W_list <input type="text" size="4" id="w_list" name="w_list"></label>' +
                '<label>H_list <input type="text" size="4" id="h_list" name="h_list"></label>' +
                '<input type="submit">Crop</input>' +
                '</form>');
            win.document.write('</body>');
        }
        win.document.close();
        Event.observe(win, 'load', function(){
            win.resizeTo(1200, 1200);
        });
    }
}