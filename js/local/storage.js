/**
 * Created by light on 5/19/2018.
 */
var renderStorage = function(jsonValue, lifeTime) {
    if (jsonValue === undefined && lifeTime === undefined) {
        localStorage.removeItem('viewed_commodities');
    } else {
        localStorage.setItem('viewed_commodities', JSON.stringify(jsonValue));
        setTimeout(function() {
            localStorage.removeItem('viewed_commodities');
        }, lifeTime);
    }
    createDestroy();
};

var ajaxGotViewed = function() {
       jQuery.ajax({
           url: "/viewedcommodities/storage/gather",
           type: "GET",
           success: function (response) {
               var parsed = JSON.parse(response);
               alert(response);
               var viewedList = (parsed['products_info'] !== []) ? parsed['products_info'] : {};
               var expiry = parsed['expiry'];
               renderStorage(viewedList, expiry);
           }
       });
};


var createDestroy = function (cookieVal) {
    if (cookieVal !== undefined) {
        document.cookie = "viewed_commodities=" + cookieVal + "; path=/";
    } else {
        var d = new Date();
        d.setTime(d.getTime() - 1000);
        document.cookie = "viewed_commodities; expires=" + d.toUTCString() + "; path=/";
    }
};