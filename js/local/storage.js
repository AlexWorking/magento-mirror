
var renderStorage = function(jsonValue, expires) {
    if (jsonValue === undefined && expires === undefined) {
        sessionStorage.removeItem('viewed_products');
    } else {
        sessionStorage.setItem('viewed_products', JSON.stringify(jsonValue));
        setTimeout(function() {
            sessionStorage.removeItem('viewed_products');
        }, expires - Date.now());
    }
    var d = new Date();
    d.setTime(d.getTime() - 1000000);
    document.cookie = "viewed_products=; expires=" + d.toUTCString() + "; path=/; domain=" + document.domain;
};

var ajaxGotViewed = function(asyncr, lifetime) {
       jQuery.ajax({
           url: "/viewedproducts/storage/gather",
           type: "POST",
           async: asyncr,
           data: {lifetime: lifetime},
           success: function (response) {
               var parsed = JSON.parse(response);
               renderStorage(parsed.products_info, parsed.expiry);
           },
           error: function () {
               document.cookie = "viewed_products=fail; path=/";
           }
       });
};

var storageContent = function(asyncr, lifetime) {
    var viewedList = sessionStorage.getItem('viewed_products');
    var cookieVal = Mage.Cookies.get('viewed_products');
    if (viewedList && !cookieVal) {
        return (viewedList && viewedList !== "[]") ? viewedList : {};
    }
    if (!cookieVal) {
        Mage.Cookies.set('viewed_products', 'reset');
    }
    if (cookieVal !== 'clear') {
        ajaxGotViewed(asyncr, lifetime);
    } else {
        renderStorage();
    }
    viewedList = sessionStorage.getItem('viewed_products');
    return (viewedList && viewedList !== "[]") ? viewedList : {};
};
