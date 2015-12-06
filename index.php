<?php
require_once __DIR__ . '/vendor/autoload.php'; // composer autoloader
require_once __DIR__ . '/phpqrcode/qrlib.php';

class TheOneThatWorks // name for now
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
        } else if (strpos($path, '/unterkunft/') === 0) {
            $acom_name = substr($path, 12); // remove the '/unterkunft/'

            $this->renderAccommodationFromName($acom_name);
        } else if ($path == '/register') {
            $this->render_registration();
        } else if ($path == '/api_update') {
            $this->handle_api($_POST);
        } else if (strpos($path, '/a/') === 0) {
            $token = substr($path, 3); // remove the '/a/'

            $this->handle_token($token);
		} else if (strpos($path, '/qr/') === 0) {
            $token = substr($path, 4);
			$this->qr($token);
        } else if (strpos($path, '/print/') === 0) {
            $token = substr($path, 7);
            $this->print_qr($token);
        } else {
            $this->render_404();
        }
    }

	//this function creates the qr code for a given id as a png image
	function qr($id)
	{
		$codeText = $_SERVER['SERVER_NAME'] . '/a/' . $id;

		QRcode::png($codeText, false, null, 10);
	}

	//this function is responsible for creating the printable page that
	//containsthe qr code, not the qr code itself
    function print_qr($id)
    {
        //check if the id is benevolent
        if (!preg_match("/^[a-z]*-[a-z]*-[a-z]*$/", $id)) {
            $this->render_invalid_token();
            exit;
        }

        $accommodation = $this->db->getAccommodationFromToken($id);

        if (!$accommodation) {
            $this->render_invalid_token();
            exit;
        }

        $template = array();
        $template['unterkunft'] = $accommodation['name'];
        $template['readonly_list'] = $_SERVER['SERVER_NAME'] . '/unterkunft/' . $accommodation['clean_name'];
        $template['qr_src'] = '/qr/' . $id;
        $template['list_url'] = $_SERVER['SERVER_NAME'] . '/a/' . $id;

        echo $this->twig->render('print.html.twig', $template);
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

    function render_accommodation($acom)
    {

        $requests = $this->db->getRequestsForAccommodation($acom['accom_id']);
        $suggestions = $this->db->getSuggestions($acom);

        echo $this->twig->render('list.html.twig', array(
            'clean_acom_name' => $acom['clean_name'],
            'requests' => $requests,
            'suggestions' => $suggestions,
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
            //header('Location:/unterkunft/' . $acom['clean_name']);
            $this->render_accommodation($acom);
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

        $acom = $this->db->getAccommodationFromCleanName($post['clean_acom_name']);
        if ($acom === false) {
            die("Error: invalid accommodation " . $post['clean_acom_name']);
        }

        $token_acom = $this->db->getAccommodationFromToken(@$_COOKIE[self::COOKIE_NAME]);
        if (!$token_acom || $token_acom['accom_id'] !== $acom['accom_id']) {
            setcookie(self::COOKIE_NAME, null, -1, '/'); // remove cookie, so the message won't come again
            die("Error: invalid acom_token - Login again? (" . @$_COOKIE[self::COOKIE_NAME] . ")");
        }

        if ($post['action'] == 'add') {
            if (!array_key_exists('item_id', $post)) { // no accommodation name given
                die("Error: no api item given");
            }

            if ($post['item_id'] == -1) { // CREATE new item
                if (!array_key_exists('item_name', $post)) { // no accommodation name given
                    die("Error: no item name given");
                }
                $new_name = $post['item_name'];
                if (strlen($new_name) > 50) { // max length
                    die("Error: name too long");
                }

                $item_id = $this->db->createItem($new_name);
                if (!$item_id) {
                    die("Error: create item failed");
                }
            } else {
                $item = $this->db->getItemFromId($post['item_id']);
                if (!$item) {
                    die("Error: invalid api item " . $post['item_id']);
                }
                $item_id = $item['item_id'];
            }

                //TODO: handle duplicates
                if ($this->db->addRequest($acom['accom_id'], $item_id) > 0) {
                    echo "success";
                } else {
                    die("Error: insert failed");
                }
        } else if ($post['action'] == 'delete') {
            if (!array_key_exists('request_id', $post)) { // no accommodation name given
                die("Error: no api request_id given");
            }
            $item = $this->db->getItemFromId($post['request_id']);
            if (!$item) {
                die("Error: invalid api request_id " . $post['request_id']);
            }

            if ($this->db->removeRequest($post['request_id']) > 0) {
                echo "success";
            } else {
                die("Error: delete failed");
            }
        } else {
            die("Error: unknown api action " . $post['action']);
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

    private function renderAccommodationFromName($acom_name)
    {
        $acom = $this->db->getAccommodationFromCleanName($acom_name);
        if ($acom === false) {
            $this->render_404();
        } else {
            $this->render_accommodation($acom);
        }
    }
}

$totw = new TheOneThatWorks();
$totw->serve_url($_SERVER['REQUEST_URI']);
exit;

?>