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
        } else if (strpos($path, '/a') === 0) {
            $token = substr($path, 3); // remove the '/a/'

            $acom = $this->handle_token($token);

            $this->render_accommodation($acom);
        } else {
            $this->render_404();
        }
    }

    function render_homepage()
    {
        echo $this->twig->render('index.html');
        exit;
    }

    function render_accommodation($accommodation)
    {
        $requests = $this->db->getRequestsForAccommodation(1);

        echo "TODO";
        exit;
    }

    function handle_token($token)
    {
        echo "TODO";
        return "";
    }

    function render_404()
    {
        echo $this->twig->render('404.html');
        exit;
    }
}

$volunteerio = new Volunteerio();
$volunteerio->serve_url($_SERVER['REQUEST_URI']);

?>