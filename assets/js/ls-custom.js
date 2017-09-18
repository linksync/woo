window.getParameterByName = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};

jQuery(document).ready(function($){
    /**
     * Tool tip initialization
     */
    $( '#ls-main-wrapper' ).tooltip();
});


jQuery(window).load(function() {
    // Animate loader off screen
    jQuery(".se-pre-con").fadeOut(1000);
});
