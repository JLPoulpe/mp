<?php
require_once(dirname(__FILE__).'/../config/config.inc.php');
require_once(dirname(__FILE__).'/../init.php');

$mail = new Mail();

$files = array('000028.pdf', 'Plan_Quai_Chartrons.pdf');
//$mail->mpSendMailFile('spessanhafoucaud@gmail.com', 'Mme Foucaud', 'mespaysans.com : A propos de votre commande', 'retrait.html', array('gender'=>'Mme', 'lastname'=>'Foucaud'), $files);