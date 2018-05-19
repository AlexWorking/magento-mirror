/**
 * Created by light on 5/19/2018.
 */
var renderStorage = function(key, jsonValue, lifeTime) {
    var expires = Date.now() + lifeTime;
    document.cookie = "viewedcommodities=stored; expires=" + expires + "; path=/";
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
               viewedList = JSON.parse(response);
               renderStorage("viewedCommodities", viewedList, 3600000);
           }
       });
};