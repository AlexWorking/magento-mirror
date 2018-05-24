/**
 * Created by light on 5/19/2018.
 */
var renderStorage = function(jsonValue, expires) {
    if (jsonValue === undefined && expires === undefined) {
        localStorage.removeItem('viewed_commodities');
    } else {
        localStorage.setItem('viewed_commodities', JSON.stringify(jsonValue));
        setTimeout(function() {
            localStorage.removeItem('viewed_commodities');
        }, expires - Date.now());
    }
    cookieDestroy();
};

var ajaxGotViewed = function() {
       jQuery.ajax({
           url: "/viewedcommodities/storage/gather",
           type: "GET",
           success: function (response) {
               var parsed = JSON.parse(response);
               renderStorage(parsed.products_info, parsed.expiry);
           }
       });
};


var cookieDestroy = function (cookieVal) {
    var d = new Date();
    d.setTime(d.getTime() - 1000000);
    document.cookie = "viewed_commodities=; expires=" + d.toUTCString() + "; path=/";
};