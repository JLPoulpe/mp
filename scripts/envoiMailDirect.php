<?php
require_once(dirname(__FILE__).'/../config/config.inc.php');
require_once(dirname(__FILE__).'/../init.php');

$mail = new Mail();

$listCustomer = array();
$customerDto = new CustomerDto();
$customerDto = new CustomerDto();
$customerDto->setGender('M.');
$customerDto->setLastname('Ledieu');
$customerDto->setEmail('aledieu@mespaysans.com');
$listCustomer[] = $customerDto;
/*$customerDto = new CustomerDto();
$customerDto->setGender('M');
$customerDto->setLastname('Beaussart');
$customerDto->setEmail('thebobo24@hotmail.com');
$listCustomer[] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setGender('Mme');
$customerDto->setLastname('Hilloou');
$customerDto->setEmail('karine.hilloou@wanadoo.fr');
$listCustomer[] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setGender('Mme');
$customerDto->setLastname('Brandizi');
$customerDto->setEmail('jean.brandizi@wanadoo.fr');
$listCustomer[] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setGender('Mme');
$customerDto->setLastname('Marchais');
$customerDto->setEmail('cecile.marchais@live.fr');
$listCustomer[] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setGender('Mme');
$customerDto->setLastname('Parrot');
$customerDto->setEmail('sabine.parot@free.fr');
$listCustomer[] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setGender('Mme');
$customerDto->setLastname('Bordes');
$customerDto->setEmail('bordes.stephanie@orange.fr');
$listCustomer[] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setGender('Mme');
$customerDto->setLastname('Marticorena');
$customerDto->setEmail('m.marticorena@devexport.com');
$listCustomer[] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setGender('Mme');
$customerDto->setLastname('CHEREL');
$customerDto->setEmail('l.cherel@hotmail.com');
$listCustomer[] = $customerDto;*/
foreach ($listCustomer as $customerDto) {
    echo "envoi ok : " . $customerDto->getEmail() . "<br />\n";
    $templateVars = array(
        'lastname'  => $customerDto->getLastname(),
        'gender'    => $customerDto->getGender(),
    );
    //$mail->mpSendMail($customerDto->getEmail(), $customerDto->getGender() . ' ' . $customerDto->getLastname(), 'mespaysans.com : Votre avis nous intéresse', 'commentaireprestation.html', $templateVars);
    $mail->mpSendMail($customerDto->getEmail(), $customerDto->getGender() . ' ' . $customerDto->getLastname(), 'mespaysans.com : Idées de recettes avec les produits de votre panier', 'idee_recette.html', $templateVars);
}