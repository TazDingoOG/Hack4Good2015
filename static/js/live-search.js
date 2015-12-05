$("#search").on("keyup", function() {
    var value = $(this).val();
    console.log(value);
    $("table tr").each(function(index) {
        if (index !== 0) {

            $row = $(this);

            var id = $row.find("td").eq(1).text();

            if (id.indexOf(value) !== 0) {
                $row.hide(400);
            }else {
                $row.show();
            }
        }
    });
});