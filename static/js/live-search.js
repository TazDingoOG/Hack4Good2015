$("#search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#table1 tr").each(function(index) {

            $row = $(this);

            var id = $row.find("td").eq(1).text().toLowerCase();

            if (id.indexOf(value) >= 0) {
                $row.show();
            }else {
                $row.hide(200);
            }
    });
});


$("#modal_search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    var bol = false;
    $("#table_modal tr").each(function(index) {

            $row = $(this);

            var id = $row.find("td").eq(1).text().toLowerCase();

            if (id.indexOf(value) >= 0) {
                $row.show();
                bol = true;
            }else {
                $row.hide(200);
            }
    });
    if(!bol) {
        
    }

});