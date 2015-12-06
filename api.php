<?php

require_once('db.php');

function handle_api(MyDB $db, $post)
{
    if (!array_key_exists('action', $post)) {
        die("Error: no api action given");
    }
    if (!array_key_exists('clean_acom_name', $post)) { // no accommodation name given
        die("Error: no api accommodation name given");
    }

    $acom = $db->getAccommodationFromCleanName($post['clean_acom_name']);
    if ($acom === false) {
        die("Error: invalid accommodation " . $post['clean_acom_name']);
    }

    $token_acom = $db->getAccommodationFromToken(@$_COOKIE[Inventeerio::COOKIE_NAME]);
    if (!$token_acom || $token_acom['accom_id'] !== $acom['accom_id']) {
        setcookie(Inventeerio::COOKIE_NAME, null, -1, '/'); // remove cookie, so the message won't come again
        die("Error: invalid acom_token - Login again? (" . @$_COOKIE[Inventeerio::COOKIE_NAME] . ")");
    }

    if ($post['action'] == 'add') {
        if (!array_key_exists('item_id', $post)) { // no accommodation name given
            die("Error: no api item given");
        }

        if ($post['item_id'] == -1) { // CREATE new item
            if (!array_key_exists('item_name', $post)) { // no accommodation name given
                die("Error: no item name given");
            }
            $new_name = $post['item_name'];
            if (strlen($new_name) > 50) { // max length
                die("Error: name too long");
            }

            $item_id = $db->createItem($new_name);
            if ($item_id == -1) {
                die("Error: create item failed");
            }
        } else {
            $item = $db->getItemFromId($post['item_id']);
            if (!$item) {
                die("Error: invalid api item " . $post['item_id']);
            }
            $item_id = $item['item_id'];
        }

        //TODO: handle duplicates
        $new_id = $db->addRequest($acom['accom_id'], $item_id);
        if ($new_id >= 0) {
            echo "success[$new_id]";
        } else {
            die("Error: insert failed");
        }
    } else if ($post['action'] == 'delete') {
        if (!array_key_exists('request_id', $post)) { // no accommodation name given
            die("Error: no api request_id given");
        }
        $item = $db->getItemFromId($post['request_id']);
        if (!$item) {
            die("Error: invalid api request_id " . $post['request_id']);
        }

        if ($db->removeRequest($post['request_id']) > 0) {
            echo "success";
        } else {
            die("Error: delete failed");
        }
    } else {
        die("Error: unknown api action " . $post['action']);
    }
}

?>