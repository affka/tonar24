$(function() {
    $('.nav-tabs').on('click', 'a', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
});