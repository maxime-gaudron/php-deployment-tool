$(document).ready(function() {
    $('.chosen-select').chosen({
        "search_contains": true
    });

    $('.chosen-select-deselect').chosen({
        search_contains: true,
        allow_single_deselect: true
    });

    $('.deployment-date').each(function() {
        var $this = $(this);
        var date = $this.data('date');

        if (date.length > 0) {
            $this.text(moment(date).fromNow());
        }
    });
});
