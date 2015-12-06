<?php

class MyDB extends SQLite3
{
    const DB_FILENAME = "data/data.db"; // contains the database (sqlite3)
    const DEFAULT_IMAGE = 'default.png';

    function __construct()
    {
        $this->open(MyDB::DB_FILENAME);
    }

	public function getAccommodationList()
	{
		$result = $this->query("SELECT * FROM Accommodation");
		return self::fetchAll($result);
	}

    public function register($name, $cleanName, $email, $telnr, $authtoken, $addr, $plz, $city)
    {
        $statement = $this->prepare("INSERT INTO Accommodation
        (name, clean_name, email, telnr, authtoken, addr, plz, city) VALUES
        (:name, :cleanName, :email, :telnr, :auttoken, :addr, :plz, :city)");
        $statement->bindValue('name', $name, SQLITE3_TEXT);
        $statement->bindValue('cleanName', $cleanName, SQLITE3_TEXT);
        $statement->bindValue('email', $email, SQLITE3_TEXT);
        $statement->bindValue('telnr', $telnr, SQLITE3_TEXT);
        $statement->bindValue('authtoken', $authtoken, SQLITE3_TEXT);
        $statement->bindValue('addr', $addr, SQLITE3_TEXT);
        $statement->bindValue('plz', $plz, SQLITE3_TEXT);
        $statement->bindValue('city', $city, SQLITE3_TEXT);
        $statement->execute();
    }

    public function getRequestsForAccommodation($accom_id)
    {
        $statement = $this->prepare("SELECT * FROM Request
  NATURAL JOIN Item WHERE accom_id=:accom_id");
        $statement->bindValue('accom_id', $accom_id, SQLITE3_INTEGER);
        $result = $statement->execute();

        return self::fetchAll($result);
    }

    public function getAccommodationFromCleanName($cn)
    {
        $statement = $this->prepare("SELECT * FROM Accommodation
            WHERE clean_name = :cn");
        $statement->bindValue('cn', $cn, SQLITE3_TEXT);
        $r = $statement->execute();

        return self::singleResult($r);
    }

    public function getAccommodationFromToken($token)
    {
        $statement = $this->prepare("SELECT * FROM Accommodation WHERE authtoken = :token");
        $statement->bindValue('token', $token, SQLITE3_TEXT);
        $r = $statement->execute();

        return self::singleResult($r);
    }

    public function getItemFromId($id)
    {
        $statement = $this->prepare("SELECT * FROM Item WHERE item_id = :id");
        $statement->bindValue('id', $id, SQLITE3_TEXT);
        $r = $statement->execute();

        return self::singleResult($r);
    }

    public function getSuggestions($acom) //TODO: better suggestions
    {
        $stmt = $this->prepare(
            "SELECT * FROM
  (SELECT item_id FROM Item
EXCEPT
SELECT item_id FROM Request
  NATURAL JOIN Item
  WHERE accom_id=:accom_id )
NATURAL JOIN Item"); // I'm glad that you asked... That are the first 5 items that are not yet added ;)
        $stmt->bindValue('accom_id', $acom['accom_id']);
        $result = $stmt->execute();

        return self::fetchAll($result);
    }

    public function addRequest($accom_id, $item_id)
    {
        $stmt = $this->prepare("INSERT INTO Request
('accom_id', 'item_id')
VALUES (:accom_id, :item_id)");
        $stmt->bindValue('accom_id', $accom_id);
        $stmt->bindValue('item_id', $item_id);
        $stmt->execute();

        if ($this->changes() > 0)
            return $this->lastInsertRowID(); // return the change count
        else
            return -1; // insert failed
    }

    public function removeRequest($request_id)
    {
        $stmt = $this->prepare("DELETE FROM Request WHERE req_id=:request_id");
        $stmt->bindValue('request_id', $request_id);
        $stmt->execute();
        return $this->changes();
    }

    public function createItem($name)
    {
        $stmt = $this->prepare("INSERT INTO ITEM (name) VALUES (:name)");
        $stmt->bindValue('name', $name);
        $stmt->execute();

        if ($this->changes() > 0)
            return $this->lastInsertRowID(); // return the change count
        else
            return -1; // insert failed
    }

    public static function fetchAll($result)
    {
        $requests = array();
        while ($req = $result->fetchArray()) { // collect all to one array
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
}

?>
