<?php

class MyDB extends SQLite3 {

    const DB_FILENAME = 'data.db'; // contains the database (sqlite3)
    const DB_DEFAULT_FILENAME = 'data_default.db'; // contains the database (sqlite3)

    function __construct()
    {
        if(!is_file(MyDB::DB_FILENAME)) {
            copy(MyDB::DB_DEFAULT_FILENAME, MyDB::DB_FILENAME) or die("Failed to copy default DB");
        }
        $this->open(MyDB::DB_FILENAME);
    }
}

?>