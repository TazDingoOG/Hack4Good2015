/**
 * LIVE ITEM GENERATION
 */
function generateItemElement(item, is_suggestion, is_editable) {
    var html = '<tr><td class="item-picture">';
    if (item['image_url'])
        html += '<img src="' + item['image_url'] + '" class="img-rounded">';
    else
        html += '<span class="glyphicon glyphicon-gift"></span>';

    html += '</td><td class="item-name">' + item['name'] + '</td>';

    if (!is_suggestion) {
        if (is_editable) {
            html += '<td class="item-checkoff"> \
                        <button class="btn" data-hoverclass="btn-success" value="' + item['req_id'] + '"> \
                    <span class="glyphicon glyphicon-ok"></span></button></td>';
        }
    } else {
        if (is_editable) {
            html += '<td class="item-checkoff"> \
                <button type="button" class="btn" data-hoverclass="btn-info" \
                    data-desc="{{ item.name }} hinzuf&uuml;gen" \
                    value="{{ item.item_id }}"> \
                <span class="glyphicon glyphicon-plus"></span></button></td>';
        } else {
            html += '<td class="item-checkoff"> \
                <b>Bereits hinzugefügt</b>\
                </button></td>';
        }
    }

    return html + '</tr>';
}

function initSearch(search, table, items, is_suggestions, is_editable) {
    updateSearch = function() {
        var needle = search.val().toLowerCase(); // the string to search for

        // clean old ones, TODO: nice search animation (old items fading away, new ones coming in)
        table.find("tr").remove();

        // show new ones
        for (var i in items) {
            var item = items[i];

            if (needle != "") { // if a needle is entered, check if the item matches it
                if (item['name'].toLowerCase().indexOf(needle) == -1)
                    continue;
            } else if(is_suggestions && item['already_added']) // empty search - don't display added items as suggestions
                continue;

            if(is_suggestions)
                is_editable = !item['already_added']; // when dealing with suggestions, they are editable only when not already added

            table.append(generateItemElement(item, is_suggestions, is_editable)); // generate element
        }
    };

    updateSearch(); // execute once to initialize
    search.on("input", updateSearch);
}

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