/**
 * Created by light on 5/19/2018.
 */
var renderStorage = function(jsonValue, lifeTime) {
    if (jsonValue === undefined || lifeTime === undefined) {
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
               var viewedList = {};
               if (response !== "[]") {
                   viewedList = JSON.parse(response);
               }
               renderStorage(viewedList, 3600000);
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