<?php

require_once '/vendor/autoload.php';


// initiate Loader (loads the HTML stuff from the directory 'templates')
$loader = new Twig_Loader_Filesystem('templates/');
$twig = new Twig_Environment($loader, array(
    /* <!--'cache' => '/path/to/compilation_cache', */
));

echo $twig->render('index.html', array('name' => 'Fabien'));
?>