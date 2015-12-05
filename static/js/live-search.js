$("#search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    console.log(value);
    $("table tr").each(function(index) {
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