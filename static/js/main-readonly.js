$(function() {
    var table1 = $("#table1");

    // remove loading gif
    table1.parent().find('img.loading-animation').remove();

    // INITIALIZE SEARCH
    initSearch($("#search"), table1, all_requests, false, false);
});