$(document).ready(function() {
    if ($('.table-show-deploy').length > 0) {
        setInterval(function() {
            window.location.reload();
        }, 5000);
    }
});