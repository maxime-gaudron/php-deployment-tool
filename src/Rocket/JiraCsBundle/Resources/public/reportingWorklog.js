var authorsList = [];

$('.week').each(function () {
    var author = $(this).data('author');

    if (authorsList.indexOf(author) === -1) {
        authorsList.push(author);
    }
});

authorsList.forEach(function (author) {
    $('#authors').append($('<option></option>').attr('value', author).text(author));
})

$(document).ready(function () {
    $('#authors').chosen().change(function () {
        var selectedAuthors = $(this).val();

        $('.week').each(function () {
            if (selectedAuthors.indexOf($(this).data('author')) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
});