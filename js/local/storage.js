/**
 * Created by light on 5/19/2018.
 */
var renderStorage = function(key, jsonValue, lifeTime) {
    var d = new Date();
    var current = d.getTime();
    d.setTime(current - 1);
    document.cookie = key + "=; expires=" + d.toUTCString() + "; path=/";
    d.setTime(current + lifeTime);
    document.cookie = key + "=stored; expires=" + d.toUTCString() + "; path=/";
    localStorage.setItem(key, JSON.stringify(jsonValue));
    setTimeout(function() {
        localStorage.removeItem(key);
    }, lifeTime);
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
               renderStorage("viewed_commodities", viewedList, 3600000);
           }
       });
};
