<?php
require_once __DIR__ . '/vendor/autoload.php'; // composer autoloader

class Volunteerio
{
    public $twig;
    public $db;

    function __construct()
    {
        // DATABASE //
        require_once('db.php');
        $this->db = new MyDB();

        // TEMPLATE ENGINE //
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/templates/'); // initiate Loader (loads the HTML stuff from the directory 'templates')
        $this->twig = new Twig_Environment($loader, array(
            /* <!--'cache' => '/path/to/compilation_cache', */      //TODO: enable caching later
        ));
    }

    /** serve the site for that specific url */
    function serve_url($url)
    {
        // parse URL
        $path = parse_url($url, PHP_URL_PATH);

        // -> /unterkunft/NAME-DER-UNTERKUNFT
        if ($path == "/") {
            $this->render_homepage();
        } else if (strpos($path, '/unterkunft') === 0) {
            $acom = substr($path, 12); // remove the '/unterkunft/'

            $this->render_accommodation($acom);
        } else if ($path == '/register') {
            $this->render_registration();
        } else if (strpos($path, '/a') === 0) {
            $token = substr($path, 3); // remove the '/a/'

            $this->handle_token($token);
        } else {
            $this->render_404();
        }
        exit;
    }

    function render_homepage()
    {
        echo $this->twig->render('index.html.twig');
        exit;
    }

    function render_registration()
    {
        echo $this->twig->render('registration_formular.html.twig');
        exit;
    }

    function render_accommodation($acom_name)
    {
        $requests = $this->db->getAccommodationFromCleanName($acom_name);
        if($requests === false) {
            $this->render_404();
            return;
        }

        echo $this->twig->render('list.html.twig', array('requests' => $requests));
    }

    function handle_token($token)
    {
        $acom = $this->db->getAccommodationFromToken($token);
        if($acom === false) {
            $this->render_invalid_token();
        } else {
            setcookie('acom_token', $token);
            header('Location:/unterkunft/'.MyDB::getAcomIdentifier($acom));
        }
    }

    function render_invalid_token()
    {
        echo $this->twig->render('token_403.html.twig');
    }

    function render_404()
    {
        echo $this->twig->render('404.html.twig');
    }
}

$volunteerio = new Volunteerio();
$volunteerio->serve_url($_SERVER['REQUEST_URI']);

?>