/**
 * Store that data we got from php in a explictly declared variable
 * -> only use these variables, that way it's easier to change the way the data is gathered later.
 */
var Data = {
    requests: all_requests,
    items: all_items,
    clean_acom_name: clean_acom_name
};

function RequestList($search, $table, itemList) {
    this.$search = $search;
    this.$table = $table;
    this.isEditable = false;


    var self = this; // when the functions/methods are called from eg. an event handler, 'this' is something different, so we use self

    self.getItemList = function() {
        return Data.requests;
    };

    self.initList = function () {
        self.updateList(); // execute once to initialize
        self.$search.on("input", self.updateList);
    };
    self.updateList = function () {
        var searchTerm = self.$search.val().toLowerCase();
        var itemList = self.getItemList();

        // clean old ones, TODO: nice search animation (old items fading away, new ones coming in) ?
        self.$table.find("tr").remove();

        // show new ones
        for (var i = 0, len = itemList.length; i < len; i++) {
            var item = itemList[i];
            if (!self.shouldDisplayItem(item, searchTerm))
                continue;

            self.$table.append(self.generateElement(item)); // generate element
        }
    };

    self.generateElement = function (item) {
        return generateItemElement(item, false, self.isEditable);
    };

    self.shouldDisplayItem = function (item, searchTerm) {
        if (searchTerm)
            return item['name'].toLowerCase().indexOf(searchTerm) > -1; // if a searchTerm is entered, check if the item matches it
        return true; // no search -> display all items
    };
}
var mainList = new RequestList($("#search"), $("#table1"), Data.requests);
// the modalList will be defined in main-editable.js

/**
 * FUNCTIONS FOR ITEM GENERATION
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
                    data-desc="' + item['name'] + ' hinzuf&uuml;gen" \
                    value="' + item['item_id'] + '"> \
                <span class="glyphicon glyphicon-plus"></span></button></td>';
        } else {
            html += '<td class="item-checkoff"> \
                <b>Bereits hinzugefügt</b>\
                </button></td>';
        }
    }

    return html + '</tr>';
}

function updateLists() {
    console.log("updating list(s)...");
    mainList.updateList();
    if (modalList) {
        modalList.updateList();
    }
}


function getRequest(request_id) {
    for (var i = 0, len = Data.requests.length; i < len; i++) {
        var req = Data.requests[i];
        if (req.req_id == request_id)
            return request_id;
    }
    return null;
}
function getItem(item_id) {
    for (var i = 0, len = Data.items.length; i < len; i++) {
        var item = Data.items[i];
        if (item['item_id'] == item_id)
            return item_id;
    }
    return null;
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
    show_alert('danger', "Fehler", "Die Daten konnten nicht mit dem Server synchronisiert werden! Bitte versuche, die <a href='javascript:window.reload()'>Seite neu zu laden.</a>");
    console.error(err);
}

// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function (from, to) {
    var rest = this.slice((to || from) + 1 || this.length);
    this.length = from < 0 ? this.length + from : from;
    return this.push.apply(this, rest);
};


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