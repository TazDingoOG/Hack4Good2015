<?php

class MyDB extends SQLite3 {

    const DB_FILENAME = 'data.db'; // contains the database (sqlite3)
    
    function __construct()
    {
        $this->open(MyDB::DB_FILENAME);
    }
}

?>