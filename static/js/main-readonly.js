
// generate items
$(function() {
    $("#table_content").find('img.loading-animation').remove();  // remove loading gif
    var table1 = $("#table1");

    for(var i in all_requests) {
        var item = all_requests[i];
        var html = generateItemElement(item, false);
        table1.append(html);
    }
});