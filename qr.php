<?php
require(__DIR__ . '/phpqrcode/qrlib.php');

$id = $_GET['id'];

$server_url = 'http://donutplanet.de:1337';
$codeText = $server_url . '/a/'.$id;

QRcode::png($codeText, false, null, 10);
?>
