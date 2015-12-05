<?php

class MyDB extends SQLite3 {

    const DB_FILENAME = 'data.db'; // contains the database (sqlite3)
    const DB_STRUCTURE = 'db_structure.sql'; // contains the structure as 'CREATE IF NOT EXISTS' statements

    function __construct()
    {
        $this->open(MyDB::DB_FILENAME);

        // create default values, if they dont exist
        $this->exec(file_get_contents(MyDB::DB_STRUCTURE));
}
}

?>