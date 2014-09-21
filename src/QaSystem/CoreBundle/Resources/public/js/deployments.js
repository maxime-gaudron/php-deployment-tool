$(document).ready(function() {
    if ($('.table-show-deploy').length > 0) {
        setInterval(function() {
            //window.location.reload();
        }, 5000);
    }

    $('.deployment-output > div span').addClass('hide');
    $('.deployment-output > div span:nth-last-child(-n+10)').removeClass('hide');
    $('.deployment-output > div').removeClass('hide');

    $('.btn-view-all').on('click', function() {
        $('.deployment-output > div span').removeClass('hide');
    });
});