<?php 

    include('/phpqrcode/qrlib.php'); 
         
    $param = $_GET['id']; // remember to sanitize that - it is user input! 
     
    // here DB request or some processing 
    $codeText = 'http://donutplanet.de:1337/a/'.$param; 
     
    // outputs image directly into browser, as PNG stream 
    QRcode::png($codeText);

?>