// generate main items
$(function() {
    var table1 = $("#table1");
    var table_modal = $("#table_modal");

    // remove loading gifs
    table1.parent().find('img.loading-animation').remove();
    table_modal.parent().find('img.loading-animation').remove();

    // INITIALIZE SEARCH
    initSearch($("#search"), table1, all_requests, false);
    initSearch($("#modal_search"), table_modal, all_items, true);
});

/*
 * MODAL FOR ADDING ITEMS
 */

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


// focus search
$('#myModal').on('shown.bs.modal', function () {
    $('#floating_button').blur();
    $('#modal_search').focus();
    $("#table-new-elem").hide();
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
