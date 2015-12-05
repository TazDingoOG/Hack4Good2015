<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once('db.php');

// DATABASE //
$db = new MyDB();

$accommodation = 1;

$statement = $db->prepare("SELECT * FROM Accommodation a
  JOIN Request r ON a.id=r.accommodation_id
  JOIN Item i ON r.item_id=i.id WHERE a.id=:accommodation_id");
$statement->bindValue('accommodation_id', $accommodation, SQLITE3_INTEGER);
$result = $statement->execute();

$requests = $result->fetchArray();

// TEMPLATE ENGINE //
$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates/'); // initiate Loader (loads the HTML stuff from the directory 'templates')
$twig = new Twig_Environment($loader, array(
    /* <!--'cache' => '/path/to/compilation_cache', */      //TODO: enable caching later
));

echo $twig->render('index.html', array('requests' => $requests));
?>