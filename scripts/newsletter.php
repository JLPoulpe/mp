<?php
require_once(dirname(__FILE__).'/../config/config.inc.php');
require_once(dirname(__FILE__).'/../init.php');

$customer = new Customer();
$listCustomer = $customer->getCustomersForNewsletter();
$mail = new Mail();

/*$customerDto = new CustomerDto();
$customerDto->setIdCustomer(1);
$customerDto->setFirstname('Anthony');
$customerDto->setLastname('Ledieu');
$customerDto->setEmail('aledieu@mespaysans.com');
$listCustomer[0] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setIdCustomer(2);
$customerDto->setFirstname('Julien');
$customerDto->setLastname('Ledieu');
$customerDto->setEmail('jledieu@mespaysans.com');
$listCustomer[1] = $customerDto;
$customerDto = new CustomerDto();
$customerDto->setIdCustomer(3);
$customerDto->setFirstname('Julien');
$customerDto->setLastname('Ledieu');
$customerDto->setEmail('julien.ledieu@gmail.com');
$listCustomer[2] = $customerDto;*/

$cmpt = 0;
foreach($listCustomer as $customerDto) {
    $templateVars = array(
        'firstname'     => htmlentities($customerDto->getFirstname()),
        'lastname'      => htmlentities($customerDto->getLastname()),
        'id'            => htmlentities($customerDto->getIdCustomer()),
    );
    //echo $customerDto->getEmail() . "\n";
    $mail->mpSendMail($customerDto->getEmail(), $customerDto->getEmail(), 'mespaysans.com : la newsletter de Mai', 'newsletter.html', $templateVars);
    $cmpt++;
}
echo 'Nombre de mails envoyés : ' . $cmpt;
