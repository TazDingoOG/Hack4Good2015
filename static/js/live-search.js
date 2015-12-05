$("#search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#table1 tr").each(function(index) {
        if (index !== 0) {

            $row = $(this);

            var id = $row.find("td").eq(1).text().toLowerCase();

            if (id.indexOf(value) >= 0) {
                $row.show();
            }else {
                $row.hide(400);
            }
        }
    });
});


$("#search_modal").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#table_modal tr").each(function(index) {
        if (index !== 0) {

            $row = $(this);

            var id = $row.find("td").eq(1).text().toLowerCase();

            if (id.indexOf(value) >= 0) {
                $row.show();
            }else {
                $row.hide(400);
            }
        }
    });
});