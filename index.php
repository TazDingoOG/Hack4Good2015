<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once('db.php');

// DATABASE //
$db = new MyDB();

$requests = $db->getRequestsForAccommodation(1);

// TEMPLATE ENGINE //
$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates/'); // initiate Loader (loads the HTML stuff from the directory 'templates')
$twig = new Twig_Environment($loader, array(
    /* <!--'cache' => '/path/to/compilation_cache', */      //TODO: enable caching later
));

echo $twig->render('index.html', array('requests' => $requests));
?>