<?php

class MyDB extends SQLite3
{

    const DB_FILENAME = 'data.db'; // contains the database (sqlite3)
    const DEFAULT_IMAGE = 'default.png';

    function __construct()
    {
        $this->open(MyDB::DB_FILENAME);
    }

    public function getRequestsForAccommodation($acc_id)
    {

        $statement = $this->prepare("SELECT * FROM Accommodation a
  JOIN Request r ON a.id=r.accommodation_id
  JOIN Item i ON r.item_id=i.id WHERE a.id=:accommodation_id");
        $statement->bindValue('accommodation_id', $acc_id, SQLITE3_INTEGER);
        $result = $statement->execute();

        return self::fetchAll($result);
    }

    public function getAccommodationFromCleanName($cn)
    {
        $statement = $this->prepare("SELECT * FROM Accommodation a
            WHERE a.clean_name = :cn");
        $statement->bindValue('cn', $cn, SQLITE3_TEXT);
        $r = $statement->execute();

        return self::singleResult($r);
    }

    public function getAccommodationFromToken($token)
    {
        $statement = $this->prepare("SELECT * FROM Accommodation a WHERE a.authtoken = :token");
        $statement->bindValue('token', $token, SQLITE3_TEXT);
        $r = $statement->execute();

        return self::singleResult($r);
    }

    public function getItemFromId($id)
    {
        $statement = $this->prepare("SELECT * FROM Item i WHERE i.id = :id");
        $statement->bindValue('id', $id, SQLITE3_TEXT);
        $r = $statement->execute();

        return self::singleResult($r);
    }

    public static function fetchAll($result)
    {
        $requests = array();
        while ($req = $result->fetchArray(SQLITE3_ASSOC)) { // collect all to one array
            array_push($requests, $req);
        }
        return $requests;
    }

    public static function singleResult($r)
    {
        $results = self::fetchAll($r);
        if (count($results) != 1) {
            if (count($results) > 1) {
                echo("Should not happen: more than one result!");
            }
            return false;
        }
        return $results[0];
    }

    public function getSuggestions($acom) //TODO: better suggestions
    {
        $stmt = $this->prepare("SELECT * FROM
  (SELECT id FROM Item
EXCEPT
SELECT i.id FROM Accommodation a
  JOIN Request r ON a.id=r.accommodation_id
  JOIN Item i ON r.item_id=i.id WHERE a.id=:acom_id LIMIT 5)
NATURAL JOIN Item
"); // I'm glad that you asked... That are the first 5 items that are not yet added ;)
        $stmt->bindValue('acom_id', $acom['id']);
        $result = $stmt->execute();

        return self::fetchAll($result);
    }

    public function addRequest($acom_id, $item_id)
    {
        $stmt = $this->prepare("INSERT INTO Request
('accommodation_id', 'item_id')
VALUES (:acom_id, :item_id)"); // I'm glad that you asked... That are the first 5 items that are not yet added ;)
        $stmt->bindValue('acom_id', $acom_id);
        $stmt->bindValue('item_id', $item_id);
        $result = $stmt->execute();
        return $result;
    }
}

?>