Data.items = all_items;
Data.clean_acom_name = clean_acom_name;

/**
 * this whole SuggestionList thing is a child prototype of RequestList(from main.js), it's kind of like a child class...
 * explained here: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Introduction_to_Object-Oriented_JavaScript#Inheritance
 *
 * it's pretty complicated, but simplifies the process of adding functionality to the item lists. (imho)
 */
var modalList; // declare variable for later
SuggestionList = function ($search, $table, itemList) {
    // call the parent constructor
    RequestList.call(this, $search, $table, itemList);
    this.isEditable = false;

    var self = this; // see RequestList for explanation

    self.getItemList = function () {
        return Data.items;
    };

    self.shouldDisplayItem = function (item, searchTerm) {
        if (searchTerm != "") {
            return item['name'].toLowerCase().indexOf(searchTerm) > -1; // if a searchTerm is entered, check if the item matches it
        } else
            return !item['already_added']; // empty search - don't display added items as suggestions
    };

    self.generateElement = function (item) {
        var editable = !item['already_added']; // suggestions they are only editable(=add-able) when not already added
        return generateItemElement(item, true, editable);
    };

    self.parent_updateList = self.updateList; // save overridden function
    self.updateList = function () {
        self.parent_updateList(); // call parent function
        var searchTerm = self.$search.val();

        /**
         * We add the item with id=-1 to the list, and then add the .new-item class,
         * and btn-success to the button
         */
        var tbody = self.$table.find('tbody') || self.$table;
        if (searchTerm.length) {
            // check if that item already exists
            for (var i = 0, len = Data.items.length; i < len; i++) {
                if (Data.items[i]['name'].toLowerCase() == searchTerm.toLowerCase())
                    return;
            }

            var newHtml = generateItemElement({
                item_id: -1,
                name: searchTerm
                //TODO: icon url for new-item ?
            }, true, true);

            var newItem = $(newHtml);
            newItem.addClass('new-item')
                .find('button').addClass('btn-success');

            tbody.append(newItem);
        }
    };
};
// Create a Student.prototype object that inherits from Person.prototype.
SuggestionList.prototype = Object.create(RequestList.prototype);
// Set the "constructor" property to refer to Student
SuggestionList.prototype.constructor = SuggestionList;
// those two statements are explained in the link above, too...


/**
 * Init lists and remove loading animations when page is ready.
 */
$(function () {
    var table_modal = $("#table_modal");

    // remove loading gifs
    $("#table1").parent().find('img.loading-animation').remove();
    table_modal.parent().find('img.loading-animation').remove();

    // INITIALIZE LISTS
    mainList.isEditable = true; // mainList was defined in main.js
    mainList.initList();

    modalList = new SuggestionList($("#modal_search"), table_modal, Data.items);
    modalList.initList();
});

/*
 * Handle the result from an API call:
 * - show error if needed
 * - update data from api result
 * - update item lists
 */
function handleApiResult(result) {
    try {
        var json = jQuery.parseJSON(result);

        if (!json['success']) {
            fail(json['error'] || "Unknown error (check api result manually)");
        }

        if (json['requests'])
            Data.requests = json['requests'];
        else {
            console.error("no 'requests' found in API result, can't update!");
            return;
        }

        if (json['items'])
            Data.items = json['items'];
        else {
            console.error("no 'items' found in API result, can't update!");
            return;
        }

        // update lists
        updateLists();
    } catch (err) {
        fail(err);
    }
}

function setButtonLoading($btn, flag) {
    if (flag) {
        $btn.children().hide();
        $btn.append('<img class="loading-animation" src="/static/img/loading-animation.gif">');
    } else {
        $btn.children('.loading-animation').remove();
        $btn.children().show();
    }
}

/** Remove items via checkoff-button **/
var table1 = $("#table1");
table1.on('click', '.item-checkoff .button-checkoff', function () {
    $this = $(this);
    // show loading animation in button
    setButtonLoading($this, true);

    var request_id = $this.val();

    $.post('/api_update', {
        action: 'delete',
        clean_acom_name: Data.clean_acom_name, // we got that from the little javascript inserted into the body by php
        request_id: request_id
    }).done(function (data) {
        handleApiResult(data);
    }).fail(function (err) {
        fail(err);
    }).always(function () {
        setButtonLoading($this, false);
    });
});

$editModal = $('#editModal');

table1.on('click', '.item-checkoff .button-edit',function () {
    //find content description
    var description = $(this).parents("tr").children(".item-name-desc").children(".item-description");
    var titel = $(this).parents("tr").children(".item-name-desc").children(".item-name");
    var id = $(this).parent("td").children(".button-checkoff").val();
    var img = $(this).parents("tr").children(".item-picture");

    $editModal.modal('show');
    $editModal.find('#item-description-edit').val(description.text());
    $editModal.find('.modal-header').html('<div class="item-picture" style="float: left">' + img.html() + '</div><h4>' + titel.text() + '</h4>');
    $editModal.find('#submit-edit').val(id);
});

$('#submit-edit').on('click', function () {
    $this = $(this);
    setButtonLoading($this, true);

    var newDescription = $editModal.find('#item-description-edit').val();
    var item_id = $editModal.find('#submit-edit').val();

    $.post('/api_update', {
        action: 'update_desc',
        clean_acom_name: Data.clean_acom_name, // we got that from the little javascript inserted into the body by php
        request_id: item_id,
        new_desc: newDescription
    }).done(function (data) {
        handleApiResult(data);
    }).fail(function (err) {
        fail(err);
    }).always(function () {
        setButtonLoading($this, false);
        $editModal.modal('hide');
    });
});


/** focus search on modal open**/
$searchModal = $('#myModal');
$searchModal.on('shown.bs.modal', function () { // focus search
    $('#floating_button').blur();
    $searchModal.find('#modal_search').focus();
});

/** Modal -> Add items via '+' **/
$searchModal.on('click', '.item-checkoff button', function () {
    $this = $(this);
    // show loading animation in button
    setButtonLoading($this, true);

    var item_id = $this.val();
    var item_name = $this.parents("tr").children(".item-name").text(); // only needed when creating a new item, because id will be -1

    $.post('/api_update', {
        action: 'add',
        clean_acom_name: Data.clean_acom_name, // we got that from the little javascript inserted into the body by php
        item_id: item_id,
        item_name: item_name
    }).done(function (data) {
        handleApiResult(data);
    }).fail(function (err) {
        fail(err);
    }).always(function () {
        setButtonLoading($this, false);
    });
});


//unused in the moment
var maxLength = 100;
function calculateRemainingSpaces(text) {
    return maxLength - text.length;
}

$(document).on('keydown', '#textarea-input', function () {
    var count = calculateRemainingSpaces($(this).val());
    if(count < 0) {
        $('#countingTextarea').css('color','#ff8888');
    } else {
        $('#countingTextarea').css('color','#cccccc');
    }
    $('#countingTextarea').text(count);
});
