Mage.Cookies.clear = function(name) {
    if(Mage.Cookies.get(name)){
        document.cookie = name + "=" +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/; domain=" + document.domain;
    }
};

var potokyViewedProducts = {

    renderStorage: function(jsonValue, expires) {
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
        Mage.Cookies.clear('viewed_products');
    },

    ajaxGotViewed: function(asyncr, lifetime) {
           jQuery.ajax({
               url: "/viewedproducts/storage/gather",
               type: "POST",
               async: asyncr,
               data: {lifetime: lifetime},
               success: function (response) {
                   var parsed = JSON.parse(response);
                   potokyViewedProducts.renderStorage(parsed.products_info, parsed.expiry);
               },
               error: function () {
                   document.cookie = "viewed_products=fail; path=/";
               }
           });
    },

    storageContent: function(asyncr, lifetime) {
        var viewedList = sessionStorage.getItem('viewed_products');
        var cookieVal = Mage.Cookies.get('viewed_products');
        if (viewedList && !cookieVal) {
            setTimeout(function() {
                sessionStorage.removeItem('viewed_products');
            }, lifetime * 1000);
            return (viewedList && viewedList !== "[]") ? viewedList : Object();
        }
        if (!cookieVal) {
            Mage.Cookies.set('viewed_products', 'reset');
        }
        if (cookieVal !== 'clear') {
            potokyViewedProducts.ajaxGotViewed(asyncr, lifetime);
        } else {
            potokyViewedProducts.renderStorage();
        }
        viewedList = sessionStorage.getItem('viewed_products');
        return (viewedList && viewedList !== "[]") ? viewedList : Object();
    }
};
