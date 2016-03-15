<?php

require_once('db.php');

/**
 * for exceptions thrown from the API (which should be sent to the client)
 */
class ApiException extends \Exception
{
}

/**
 * Handle a call to the API, and print out a JSON array as result.
 * (which should be the only thing to print out)
 *
 * @param Inventeerio $main
 * @param MyDB $db
 * @param $post - POST data
 */
function handle_api(Inventeerio $main, MyDB $db, $post)
{
    $result = array(
        "success" => false,
    );
    try {
        if (!array_key_exists('action', $post))
            throw new ApiException("no api action given");
        if (!array_key_exists('clean_acom_name', $post)) // no accommodation name given
            throw new ApiException("no api accommodation name given");

        $acom = $db->getAccommodationFromCleanName($post['clean_acom_name']);
        if ($acom === false)
            throw new ApiException("invalid accommodation " . $post['clean_acom_name']);

        $token_acom = $db->getAccommodationFromToken(@$_COOKIE[Inventeerio::COOKIE_NAME]);
        if (!$token_acom || $token_acom['accom_id'] !== $acom['accom_id']) {
            setcookie(Inventeerio::COOKIE_NAME, null, -1, '/'); // remove cookie, so the message won't come again
            throw new ApiException("invalid acom_token - Login again? (" . @$_COOKIE[Inventeerio::COOKIE_NAME] . ")");
        }

        // action=ADD
        if ($post['action'] == 'add') {
            if (!array_key_exists('item_id', $post)) // no accommodation name given
                throw new ApiException("no api item given");

            if ($post['item_id'] == -1) { // id=-1 -> CREATE new item
                if (!array_key_exists('item_name', $post))  // no accommodation name given
                    throw new ApiException("no item name given");

                $new_name = $post['item_name'];
                if (strlen($new_name) > 50) // max length
                    throw new ApiException("name too long");

                $item_id = $db->createItem($new_name);
                if ($item_id == -1)
                    throw new ApiException("create item failed");
            } else { // add existent item
                $item = $db->getItemFromId($post['item_id']);
                if (!$item)
                    throw new ApiException("invalid api item " . $post['item_id']);
                $item_id = $item['item_id'];
            }

            //TODO: handle duplicates
            $new_id = $db->addRequest($acom['accom_id'], $item_id);
            if ($new_id >= 0)
                $result['success'] = true;
            else
                throw new ApiException("insert failed");


            // action=DELETE
        } else if ($post['action'] == 'delete') {
            if (!array_key_exists('request_id', $post)) // no accommodation name given
                throw new ApiException("no api request_id given");

            $request = $db->getRequestFromId($post['request_id']);
            if (!$request)
                throw new ApiException("invalid api request_id " . $post['request_id']);

            if ($db->removeRequest($post['request_id']) > 0) {
                $result['success'] = true;
            } else
                throw new ApiException("delete failed");
        } else if ($post['action'] == 'update_desc') {
            if (!array_key_exists('request_id', $post)) // no accommodation name given
                throw new ApiException("no api request_id given");
            if (!array_key_exists('new_desc', $post)) // no accommodation name given
                throw new ApiException("no 'new_desc' given");

            $request = $db->getRequestFromId($post['request_id']);
            if (!$request)
                throw new ApiException("invalid api request_id " . $post['request_id']);

            $request['description'] = $post['new_desc'];
            if ($db->updateRequest($request) > 0) {
                $result['success'] = true;
            } else
                throw new ApiException("update_desc failed");
        } else {
            throw new ApiException("unknown api action: " . $post['action']);
        }


        // add new data to result
        $requests = $main->getAllRequests($acom['accom_id'], true);
        $items = $main->getAllItems(true);
        Utils::generateAlreadyAddedProp($items, $requests);

        $result['requests'] = $requests;
        $result['items'] = $items;


    } catch (APIException $e) {
        $result['error'] = $e->getMessage();
    }

    echo json_encode($result);
}

?>