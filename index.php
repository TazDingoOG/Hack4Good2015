<?php

require_once __DIR__ . '/vendor/autoload.php';


// initiate Loader (loads the HTML stuff from the directory 'templates')
$loader = new Twig_Loader_Filesystem(__DIR__ . '/templates/');
$twig = new Twig_Environment($loader, array(
    /* <!--'cache' => '/path/to/compilation_cache', */
));

echo $twig->render('index.html', array('name' => 'Fabien'));
?>