
(function (jq) {
    jq( document ).ready(function (jQuery) {
        jQuery("#edit_form").attr("enctype", "multipart/form-data");
    });
    jq.noConflict(true);
})(jQuery);
