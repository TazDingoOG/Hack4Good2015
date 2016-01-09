// this is a child prototype of RequestList, it's kindof like a child class...
// explained here: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Introduction_to_Object-Oriented_JavaScript#Inheritance
// it's pretty fucking complicated, but simplifies the process of adding functionality to the item lists. (imho)
var modalList;
SuggestionList = function ($search, $table, itemList) {
    // call the parent constructor
    RequestList.call(this, $search, $table, itemList);
    this.isEditable = false;

    var self = this; // see RequestList for explanation

    self.getItemList = function() {
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
};
// Create a Student.prototype object that inherits from Person.prototype.
SuggestionList.prototype = Object.create(RequestList.prototype);
// Set the "constructor" property to refer to Student
SuggestionList.prototype.constructor = SuggestionList;


// generate main items
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
 * MODAL FOR ADDING ITEMS
 */
function handleApiResult(result) {
    try {
        var json = jQuery.parseJSON(result);

        if (!json['success']) {
            fail(json['error'] || "Unknown error (check api result manually)");
        }

        if (json['requests'])
            Data.requests = json['requests'];
        else
            console.error("no 'requests' found in API result, can't update!");
        if (json['items'])
            Data.items = json['items'];
        else
            console.error("no 'items' found in API result, can't update!");

        // update lists
        updateLists();
    } catch (err) {
        fail(err);
    }
}

/** Remove items via checkoff-button **/
var table1 = $("#table1");
table1.on('click', '.item-checkoff button', function () {
    var request_id = $(this).val();

    $.post('/api_update', {
        action: 'delete',
        clean_acom_name: clean_acom_name, // we got that from the little javascript inserted into the body by php
        request_id: request_id
    }).success(function (data) {
        handleApiResult(data);
    }).fail(function (err) {
        fail(err);
    });

    // TODO: add item to list in bg
    // TODO: remove item from list of items that one could add
    // -> maybe move it from the modal to the background list
});


searchModal = $('#myModal');
searchModal.on('shown.bs.modal', function () { // focus search
    $('#floating_button').blur();
    $('#modal_search').focus();
    $("#table-new-elem").hide();
});

/** Modal -> Add items via '+' **/
searchModal.on('click', '.item-checkoff button', function () {
    var item_id = $(this).val();
    var item_name = $(this).parents("tr").children(".item-name").text(); // only needed when creating a new item, because id will be -1

    console.log()
    $.post('/api_update', {
        action: 'add',
        clean_acom_name: clean_acom_name, // we got that from the little javascript inserted into the body by php
        item_id: item_id,
        item_name: item_name
    }).success(function (data) {
        handleApiResult(data);
    }).fail(function (err) {
        fail(err);
    });

    // TODO: add item to list in bg
    // TODO: remove item from list of items that one could add
    // -> maybe move it from the modal to the background list
});
