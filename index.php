<?php
require_once __DIR__ . '/vendor/autoload.php'; // composer autoloader
require_once __DIR__ . '/phpqrcode/qrlib.php';
require_once 'utils.php';

class Inventeerio // name for now
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
        } else if (strpos($path, '/csv/') === 0) {
            $acom_name = substr($path, 5); //remove '/csv/'
            if (empty($acom_name)) {
                $this->renderCSVOverview();
            } else {
                $this->renderCSVFromName($acom_name);
            }
        } else if ($path == '/register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handle_registration_post();
            } else { //GET
            $this->render_registration();
            }
        } else if ($path == '/api_update') {
            require_once("api.php");
            handle_api($this->db, $_POST);
        } else if ($path == '/liste') {
            $this->render_liste();
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

    function getHostString()
    {
        $host = "http://" . $_SERVER['SERVER_NAME'];
        if ($_SERVER['SERVER_PORT'] != "80") {
            $host .= ":" . $_SERVER['SERVER_PORT'];
        }
        return $host;
    }

	//this function creates the qr code for a given id as a png image
	function qr($id)
	{
		$codeText = $this->getHostString() . '/a/' . $id;

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
        $template['readonly_list'] = $this->getHostString() . '/unterkunft/' . $accommodation['clean_name'];
        $template['qr_src'] = '/qr/' . $id;
        $template['list_url'] = $this->getHostString() . '/a/' . $id;

        echo $this->twig->render('print.html.twig', $template);
    }

    function render_homepage()
    {
        echo $this->twig->render('demo_home.html.twig');
        exit;
    }

	private function generateRandomString($length = 10) {
		$characters = 'abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

    function handle_registration_post()
    {
        $name = $_POST['name'];
        $cleanName = Utils::generateCleanName($name);
        $email = $_POST['email'];
        $telnr = $_POST['phone_number'];
        $authtoken = $this->generateRandomString(4) . '-' . $this->generateRandomString(4) . '-' . $this->generateRandomString(4);
        $addr = $_POST['street_number'];
        $plz = $_POST['zip'];
        $city = $_POST['city'];
        $this->db->register($name, $cleanName, $email, $telnr, $authtoken, $addr, $plz, $city);
        header('Location: /print/'.$authtoken);
    }

    function render_registration()
    {
        echo $this->twig->render('registration_formular.html.twig');
        exit;
    }

    function render_liste()
    {
        echo $this->twig->render('liste.html.twig', array(
            'accommodations' => $this->db->getAccommodationList()
        ));
        exit;
    }

    function render_accommodation($acom, $editable = false)
    {
        if ($editable) {
            $token_acom = $this->db->getAccommodationFromToken(@$_COOKIE[self::COOKIE_NAME]);
            if (!$token_acom || $token_acom['accom_id'] !== $acom['accom_id']) {
                setcookie(Inventeerio::COOKIE_NAME, null, -1, '/'); // remove cookie, so the message won't come again
                die("Error: invalid acom_token - Login again? (" . @$_COOKIE[Inventeerio::COOKIE_NAME] . ")");
            }
        }

        $requests = $this->db->getRequestsForAccommodation($acom['accom_id']);
        $suggestions = $this->db->getSuggestions($acom);

        echo $this->twig->render('detail.html.twig', array(
            'editable' => $editable,
            'acom_name' => $acom['name'],
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
            $_COOKIE[self::COOKIE_NAME] = $token;

            $this->render_accommodation($acom, true);
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

    function renderCSVOverview()
    {
        /*
		echo "<ul>";
		$result = $this->db->getAccommodationList();
		foreach ($result as $row) {
			echo "<li><a href=\"/csv/" . $row['clean_name'] . "\">" . $row['name'] . "</a></li>";
		}
		echo "</ul>";
         */
    }

    function renderCSVFromName($acom_name)
    {
        $acom = $this->db->getAccommodationFromCleanName($acom_name);
        if ($acom === false) {
            $this->render_404();
        } else {
			$requests = $this->db->getRequestsForAccommodation($acom['accom_id']);
			$items = array();
			foreach ($requests as $request) {
				$items[] = $request['name'];
			}
			//name,adresse,plz,telnr,verantwortlicher,annahmezeitraum,website,anz helfer, gueltigkeit in stunden
			$first = array($acom['name'],$acom['addr'],$acom['plz'],$acom['telnr'],$acom['email'],"Das hier sind eventuell noch Testdaten. Bitte nur aus dem Haus gehen, wenn ihr wirklich sicher seid, dass hier eine Unterkunft ist","","","","12",join('|',$items));
			echo join(',',$first);
        }
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

$main = new Inventeerio();
$main->serve_url($_SERVER['REQUEST_URI']);

?>