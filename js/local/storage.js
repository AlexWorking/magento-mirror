/**
 * Created by light on 5/19/2018.
 */
var renderStorage = function(key, jsonValue, lifeTime) {
    localStorage.setItem(key, JSON.stringify(jsonValue));
    setTimeout(function() {
        localStorage.removeItem(key);
    }, lifeTime);
}
