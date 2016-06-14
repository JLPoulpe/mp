<?php
require(dirname(dirname(dirname(__FILE__))).'/config/config.inc.php');
include_once(_PS_MODULE_DIR_.'mpfacture/models/Facture.php');
include_once(_PS_MODULE_DIR_.'mpfacture/models/FactureDto.php');
$method = Tools::getValue('method');
switch($method) {
    case 'searchByCp' :
        $cp = (int) Tools::getValue('cp');
        $dep = Tools::getValue('dep');
        if(!empty($cp)) {
            echo MPTools::isCpEnableForDelivry($cp, $dep);
        } else {
            echo false;
        }
        break;
    case 'isCpEnable' :
        $cp = Tools::getValue('cp');
        if(MPTools::isCp($cp)) {
            echo MPTools::isCpEnable($cp);
        } else {
            echo MPException::$stringCpIsWront;
        }
        break;
    case 'changeRegion':
        $numDep = Tools::getValue('numDep');
        echo MPTools::changeCookie($numDep);
        break;
    case 'addAddress' :
        $service = new Service();
        echo $service->addAddress(Tools::getValue('customerId'), Tools::getValue('firstname'), Tools::getValue('name'), Tools::getValue('tel'), Tools::getValue('mobile'), Tools::getValue('address'), Tools::getValue('address2'), Tools::getValue('cp'), Tools::getValue('city'), Tools::getValue('infos'), Tools::getValue('addressName'));
        break;
    case 'addCookieBio' :
        $bio = Tools::getValue('bio');
        $cookie = new MPCookie();
        $cookie->writeCookie('bio', $bio, 365);
        echo true;
        break;
    case 'orderToPay' :
        $params = Tools::getValue('params');
        $facture = new Facture();
        $result = $facture->ordersToPay($params);
        echo $result;
        break;
   /* case 'searchByGeo' :
        $lat = Tools::getValue('lat');
        $lng = Tools::getValue('lng');
        if($lat && $lng) {
            echo MPTools::getAdresse($lat, $lng);
        } else {
            echo false;
        }
        break;*/
    default:
        echo false;
}