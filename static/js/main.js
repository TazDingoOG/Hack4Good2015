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
            $row.hide(400);
        }
    });
    if (!bol) {
        $("#table_content").hide(400);
    } else {
        $("#table_content").show(400);
    }
});


$("#modal_search").on("keyup", function () {
    var org_value = $(this).val();
    var value = $(this).val().toLowerCase()
    var bol = false;
    $("#table_modal tr").each(function (index) {
        var row = $(this);

        var id = row.find("td").eq(1).text().toLowerCase();

        if (id.indexOf(value) >= 0) {
            row.show();
            if (id == value) {
                bol = true;
            }
        } else {
            row.hide(200);
        }
    });
    if (value.length > 0) {
        if (!bol) {
            $("#table-new-elem").show();
            $("#item-row-content").html(org_value);
        } else {
            $("#table-new-elem").hide();
        }
    } else {
        $("#table-new-elem").hide();
    }
});


/** animate btn hovers **/
$(document.body).on('mouseenter', "[data-hoverclass]", function () {
    $(this).addClass($(this).data('hoverclass'));
});
$(document.body).on('mouseleave', "[data-hoverclass]", function () {
    $(this).removeClass($(this).data('hoverclass'));
});

/** shows a bootstrap alert at the top of #main_container
 *
 * @param type - danger|info|warning|success
 * @param strong - Something like 'Error:' to display bold before the actual message
 * @param msg - the actual message
 */
function show_alert(type, strong, msg) {
    var alert = $('<div class="alert alert-' + type + '" style="display: none;">'
        + '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
        + (strong ? '<strong>' + strong + '</strong> ' : '')
        + msg
        + '</div>');
    alert.prependTo("#main_container").fadeIn('slow', function () { // manually fade in, and add the fade classes after
        $(this).addClass("fade in");                                // fading finished (they are needed for fading out)
    });                                                             // - I did not get it to work in a better way :/
}

function fail(err) {
    show_alert('danger', "Fehler", "Die Daten konnten nicht mit dem Server synchronisiert werden! Bitte versuche, die Seite neu zu laden.");
    console.log(err);
    //window.location.reload(); // for now, just reload (we dont have sync)
}

/** Remove items via 'x' **/
$("#table1").on('click', '.item-checkoff button', function () {
    var request_id = $(this).val();

    $.post('/api_update', {
        action: 'delete',
        clean_acom_name: clean_acom_name, // we got that from the little javascript inserted into the body by php
        request_id: request_id
    }).success(function (data) {
        if (data == "success") {
            console.log("success: " + data);
            $("#table1 .item-checkoff button[value=" + request_id + "]")
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
$("#myModal").on('click', '.item-checkoff button', function () {
    var item_id = $(this).val();
    var item_name = $(this).parents("tr").children(".item-name").text(); // only needed when creating a new item, because id will be -1

    $.post('/api_update', {
        action: 'add',
        clean_acom_name: clean_acom_name, // we got that from the little javascript inserted into the body by php
        item_id: item_id,
        item_name: item_name
    }).success(function (data) {
        // data should look like this: 'success[ID]'
        var successRegex = /^success\[(\d+)\]$/;
        var match = successRegex.exec(data);

        if (match) {
            var new_id = match[1];
            console.log("success: " + new_id);

            $("#myModal .item-checkoff button[value=" + item_id + "]")
                .parents("tr").hide(400, function () { // remove entry in modal
                var me = $(this);
                me.detach();
                me.find('#item-row-content').removeAttr('id');

                // convert to main list format
                me.removeClass("new-item")
                var btn = me.find(".item-checkoff button");
                btn.attr('class', 'btn');
                btn.val(new_id);
                btn.attr('data-hoverclass', 'btn-success');
                btn.find('span').attr('class', 'glyphicon glyphicon-ok');
                me.appendTo("#table1 tbody").show(400);
            });
            $('#new-item-table tr:first').after('<tr id="row-content"><td class="item-picture"><span class="glyphicon glyphicon-gift"></span></td><td class="item-name" id="item-row-content">Element hinzugefügt..</td><td class="item-checkoff"><button type="button" class="btn btn-success" data-desc="neues Item hinzuf&uuml;gen" value="-1"><span class="glyphicon glyphicon-plus"></span></button></td></tr>');
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
