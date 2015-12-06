$("#search").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    var bol = false;
    $("#table1 tr").each(function (index) {

        $row = $(this);

        var id = $row.find("td").eq(1).text().toLowerCase();

        if (id.indexOf(value) >= 0) {
            $row.show();
            bol = true;
        } else {
            $row.hide(200);
        }
    });
    if(!bol) {
        $("#table_content").hide(400);
    } else {
        $("#table_content").show(400);
    }
});


$("#search_modal").on("keyup", function () {
    var org_value = $(this).val();
    var value = value.toLowerCase()
    var bol = false;
    $("#table_modal tr").each(function (index) {

        $row = $(this);

        var id = $row.find("td").eq(1).text().toLowerCase();

        if (id.indexOf(value) >= 0) {
            $row.show();
            bol = true;
        } else {
            $row.hide(200);
        }
    });
    if(!bol) {
        $('#table_modal tr:last').after('<tr>'
            +'<td class="item-picture">'
               + '<span class="glyphicon glyphicon-gift"></span>'
           + '</td>'
           + '<td class="item-name"> ' + org_value + ' </td>'
            + '<td class="item-checkoff"><button type="button" class="btn btn-success" data-desc="' + org_value + ' hinzuf&uuml;gen" value="-1">'
               + '<span class="glyphicon glyphicon-plus"></span>'
            + '</button>'
           + '</td>'
       + '</tr>');
    } 

});


/** Remove items via 'x' **/
$("#table1").on('click', '.item-checkoff button', function () {
    var request_id = $(this).val();

    function fail(err) {
        alert(err);
    }

    $.post('/api_update', {
        action: 'delete',
        clean_acom_name: clean_acom_name, // we got that from the little javascript inserted into the body by php
        request_id: request_id
    }).success(function (data) {
        if (data == "success") {
            console.log("success: " + data);
            $("#table1 .item-checkoff button[value="+request_id+"]")
                .parents("tr").hide(400); // remove entry in modal
            // TODO: add to list in background
        } else {
            fail(data);
        }
    }).fail(function (err) {
        fail(err);
    });

    // TODO: add item to list in bg
    // TODO: remove item from list of items that one could add
    // -> maybe move it from the modal to the background list
});

/** Modal -> Add items via '+' **/
$("#table_modal").on('click', '.item-checkoff button', function () {
    var item_id = $(this).val();
    var item_name = $(this).parents("tr").children(".item-name").text(); // only needed when creating a new item, because id will be -1

    function fail(err) {
        alert(err);
    }

    $.post('/api_update', {
        action: 'add',
        clean_acom_name: clean_acom_name, // we got that from the little javascript inserted into the body by php
        item_id: item_id,
        item_name: item_name
    }).success(function (data) {
        if (data == "success") {
            console.log("success: " + data);
            $("#table_modal .item-checkoff button[value=" + item_id + "]")
                .parents("tr").hide(400); // remove entry in modal
            // TODO: add to list in background
        } else {
            fail(data);
        }
    }).fail(function (err) {
        fail(err);
    });

    // TODO: add item to list in bg
    // TODO: remove item from list of items that one could add
    // -> maybe move it from the modal to the background list
});
