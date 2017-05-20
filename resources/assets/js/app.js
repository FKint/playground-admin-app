/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


window.formatPriceWithoutSign = function (val) {
    return parseFloat(val).toFixed(2);
};
window.formatPrice = function (val) {
    return "€ " + formatPriceWithoutSign(val);
};