var App = _.extend(App || {}, {
    Model: {},
    Collection: {},
    View: {}
});

$(document).on("click", "a:not([data-bypass])", function(evt) {
    // Get the anchor href and protcol
    var href = $(this).attr("href");
    var protocol = this.protocol + "//";

    // Ensure the protocol is not part of URL, meaning its relative.
    if (href && href.slice(0, protocol.length) !== protocol &&
        href.indexOf("javascript:") !== 0) {
        // Stop the default event to ensure the link will not cause a page
        // refresh.
        evt.preventDefault();

        // `Backbone.history.navigate` is sufficient for all Routers and will
        // trigger the correct events.  The Router's internal `navigate` method
        // calls this anyways.
        Backbone.history.navigate(href, true);
    }
});

ZeroClipboard.config( { swfPath: "/bundles/rocketjiracs/js/vendor/ZeroClipboard.swf" } );
