/**
 * LIVE ITEM GENERATION
 */
function generateItemElement(item, is_editable) {
    var html = '<tr><td class="item-picture">';
    if (item['image_url'])
        html += '<img src="' + item['image_url'] + '" class="img-rounded">';
    else
        html += '<span class="glyphicon glyphicon-gift"></span>';

    html += '</td><td class="item-name">' + item['name'] + '</td>';

    if (is_editable) {
        html += '<td class="item-checkoff"> \
                    <button class="btn" data-hoverclass="btn-success" value="' + item['req_id'] + '"> \
                <span class="glyphicon glyphicon-ok"></span></button></td>';
    }
    html += '</tr>';

    return html;
}




/**
 * SEARCH
 */
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


/*
 * EXTRAS
 */

/* animate btn hovers
 eg: <div data-hoverclass="is-hovered">
 while hovering, the specified class is added to that element */
$(document.body).on('mouseenter', "[data-hoverclass]", function () {
    $(this).addClass($(this).data('hoverclass'));
});
$(document.body).on('mouseleave', "[data-hoverclass]", function () {
    $(this).removeClass($(this).data('hoverclass'));
});

$(document).ready(function () {
    $('[data-tooltip="tooltip"]').tooltip();
});