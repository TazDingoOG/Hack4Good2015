<?php
require_once __DIR__ . '/vendor/autoload.php'; // composer autoloader

class Volunteerio
{
    const COOKIE_NAME = 'acom_token';
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
        } else if ($path == '/api_update') {
            $this->handle_api($_POST);
        } else if (strpos($path, '/a') === 0) {
            $token = substr($path, 3); // remove the '/a/'

            $this->handle_token($token);
        } else {
            $this->render_404();
        }
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
        $acom = $this->db->getAccommodationFromCleanName($acom_name);
        if ($acom === false) {
            $this->render_404();
            return;
        }
        $requests = $this->db->getRequestsForAccommodation($acom['id']);

        echo $this->twig->render('list.html.twig', array(
            'clean_acom_name' => $acom['clean_name'],
            'requests' => $requests,
        ));
    }

    function handle_token($token)
    {
        $acom = $this->db->getAccommodationFromToken($token);
        if ($acom === false) {
            $this->render_invalid_token();
        } else {
            setcookie(self::COOKIE_NAME,
                $token,
                time() + 60 * 60 * 24 * 30, // TODO: other cookie expiration ?
                '/'
            );
            header('Location:/unterkunft/' . $acom['clean_name']);
        }
    }

    function handle_api($post)
    {
        if (!array_key_exists('action', $post)) {
            die("Error: no api action given");
        }
        if (!array_key_exists('clean_acom_name', $post)) { // no accommodation name given
            die("Error: no api accommodation name given");
        }
        if (!array_key_exists('item_id', $post)) { // no accommodation name given
            die("Error: no api item given");
        }

        $acom = $this->db->getAccommodationFromCleanName($post['clean_acom_name']);
        if ($acom === false) {
            die("Error: invalid accommodation " . $post['clean_acom_name']);
        }

        $token_acom = $this->db->getAccommodationFromToken(@$_COOKIE[self::COOKIE_NAME]);
        if (!$token_acom || $token_acom['id'] !== $acom['id']) {
            setcookie(self::COOKIE_NAME, null, -1, '/'); // remove cookie, so the message won't come again
            die("Error: invalid acom_token - Login again? (" . @$_COOKIE[self::COOKIE_NAME] . ")");
        }

        $item = $this->db->getItemFromId($post['item_id']);
        if (!$item) {
            die("Error: invalid api item " . $post['item_id']);
        }

        if ($post['action'] == 'add') {
            echo "success";
        }
    }

    function render_invalid_token()
    {
        echo $this->twig->render('token_403.html.twig');
    }

    function render_404()
    {
        echo $this->twig->render('404.html.twig', array(
            'request_uri' => $_SERVER['REQUEST_URI']
        ));
    }
}

$volunteerio = new Volunteerio();
$volunteerio->serve_url($_SERVER['REQUEST_URI']);
exit;

?>