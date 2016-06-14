<?php
require_once(dirname(__FILE__).'/../config/config.inc.php');
require_once(dirname(__FILE__).'/../init.php');
$date = new DateTime();

// ENVOI PRODUCTEUR
$afterTomorrow = clone($date);
$afterTomorrow->add(new DateInterval('P2D'));
$cart = new Cart();
$listSupplier = $cart->getByCartStatus(Cart::PRODUIT_EN_ATTENTE_VALIDATION, $date->format('Y-m-d'), $afterTomorrow->format('Y-m-d'));

$market = new Market();
$cacheMarket = array();
$mail = new Mail();
$supplier = new Supplier();
$cacheSupplier = array();
$cmpt = 0;
foreach($listSupplier as $cartDto) {
    if(!in_array($cartDto->getIdMarket(), array_keys($cacheMarket))) {
        $cacheMarket[$cartDto->getIdMarket()] = $market->getMarketFromId($cartDto->getIdMarket(), false, false, false);
    }
    if(!in_array($cartDto->getIdSupplier(), array_keys($cacheSupplier))) {
        $cacheSupplier[$cartDto->getIdSupplier()] = $supplier->getAllInfo($cartDto->getIdSupplier());
        $templateVars = array(
            'firstname'     => htmlentities($cacheSupplier[$cartDto->getIdSupplier()]->getFirstname()),
            'lastname'      => htmlentities($cacheSupplier[$cartDto->getIdSupplier()]->getLastname()),
            'marketname'    => htmlentities($cacheMarket[$cartDto->getIdMarket()]->getName()),
        );
        $emailTo = $cacheSupplier[$cartDto->getIdSupplier()]->getEmail();
        $emailTo = 'jledieu@mespaysans.com';
        $mail->mpSendMail($emailTo, htmlentities($cacheSupplier[$cartDto->getIdSupplier()]->getFirstname()) . ' ' . htmlentities($cacheSupplier[$cartDto->getIdSupplier()]->getLastname()), 'Des commandes sont prêtes à être validées', 'recap-producteur.html', $templateVars);
        echo $cacheSupplier[$cartDto->getIdSupplier()]->getEmail() .' '. $cacheSupplier[$cartDto->getIdSupplier()]->getFirstname() . "\n";
        $cmpt++;
    }
}
echo $date->format('y-m-d H:i:s')  . ' : ' . $cmpt . ' mails envoyés' . "\n";