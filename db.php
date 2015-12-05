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

        $requests = array();
        while ($req = $result->fetchArray(SQLITE3_ASSOC)) { // collect all to one array
            array_push($requests, $req);
        }



        return $requests;
    }
}

?>