<?php
class mphomegmapModuleFrontController extends ModuleFrontController
{
    private $isMobile;
    private $breadcrumb;
    private $nomDepartement;
    
    public function __construct()
    {
        parent::__construct();
	$this->context  = Context::getContext();
        $this->action   = Tools::getValue('action');
        $this->breadcrumb = new breadcrumb(Tools::getValue('controller'), $this->action);
        $mobileDetect = new MobileDetect();
        $this->isMobile = $mobileDetect->isMobile();
    }

    public function initContent()
    {
        try {
            parent::initContent();
            if(MPTools::getCookie('loc')==DepartementLivraison::IDF){
                if($this->action=='paniersemaine') {
                    $this->action = 'paniersIDF';
                } elseif (!in_array($this->action, array('accueil', 'paniersemaine', 'zonelivraison', 'listeVilles'))) {
                    header('Location:/');
                }
            }
            $this->{$this->action}();
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    public function carte()
    {
        $market = new Market();
        $listMarket = $market->getAllMarkets('ml.name, m.id_market, m.address, m.postal_code, m.city, m.open_at, m.close_at, m.lundi, m.mardi, m.mercredi, m.jeudi, m.vendredi, m.samedi, m.dimanche, m.latitude, m.longitude', 'ORDER BY m.latitude, m.longitude', false, false, false);
        $listMarketGMapContent = '';
        $contentString = array();
        foreach ($listMarket as $key=>$marketDto) {
            if ($marketDto->getLatitude()!='' && $marketDto->getLongitude()!='') {
                $listJourOuvert = array();
                if($marketDto->getLundi()) {
                    $listJourOuvert[] = 'Lundi';
                }
                if($marketDto->getMardi()) {
                    $listJourOuvert[] = 'Mardi';
                }
                if($marketDto->getMercredi()) {
                    $listJourOuvert[] = 'Mercredi';
                }
                if($marketDto->getJeudi()) {
                    $listJourOuvert[] = 'Jeudi';
                }
                if($marketDto->getVendredi()) {
                    $listJourOuvert[] = 'Vendredi';
                }
                if($marketDto->getSamedi()) {
                    $listJourOuvert[] = 'Samedi';
                }
                if($marketDto->getDimanche()) {
                    $listJourOuvert[] = 'Dimanche';
                }
                if(isset($contentString[$marketDto->getLatitude().''.$marketDto->getLongitude()])) {
                    $contentString[$marketDto->getLatitude().''.$marketDto->getLongitude()] .= '<br /><br /><b>' . addslashes($marketDto->getName()) . '</b>'
                        . '<br />' . $marketDto->getAddress() . ' ' . $marketDto->getPostalCode() . ' ' . $marketDto->getCity() . ''
                        . '<br />Ouverture le : <b>'. implode(',', $listJourOuvert) . '</b> de ' . $marketDto->getOpenAt() . ' à ' . $marketDto->getCloseAt();
                } else {
                    $contentString[$marketDto->getLatitude().''.$marketDto->getLongitude()] = '<b>' . addslashes($marketDto->getName()) . '</b>'
                            . '<br />' . $marketDto->getAddress() . ' ' . $marketDto->getPostalCode() . ' ' . $marketDto->getCity() . ''
                            . '<br />Ouverture le <b>: '. implode(',', $listJourOuvert) . '</b> de ' . $marketDto->getOpenAt() . ' à ' . $marketDto->getCloseAt();
                }
            }
        }
        foreach ($listMarket as $key=>$marketDto) {
            if ($marketDto->getLatitude()!='' && $marketDto->getLongitude()!='') {
                $listMarketGMapContent .= "\n" . '
                    var marker_' . $key . ' = new google.maps.Marker({
                        map: map,
                        position: new google.maps.LatLng(\'' . $marketDto->getLatitude() . '\', \'' . $marketDto->getLongitude() . '\'),
                        icon: monIconPerso,
                    });
                    var contentString_' . $key . ' = \'' . $contentString[$marketDto->getLatitude().''.$marketDto->getLongitude()] . '\';
                    var infowindow_' . $key . ' = new google.maps.InfoWindow({
                        content: contentString_' . $key . '
                    });
                    google.maps.event.addListener(marker_' . $key . ', \'click\', function () {
                        infowindow_' . $key . '.open(map, marker_' . $key . ');
                    });';
            }
        }
        $this->context->smarty->assign(
               array(
                   'meta_title' => 'Localisez vos marchés - mespaysans.com',
                   'rightColumn'            => false,
                   'breadcrumb'             => $this->breadcrumb->getMenu(),
                   'listMarketGMapContent'  => $listMarketGMapContent,
               )
           );
        $this->setTemplate('carte.tpl');
    }
    
    public function listeVilles() {
        $latlng = MPTools::getLatLngFromDepartement(MPTools::getCookie('loc'));
        
        $this->context->smarty->assign(
               array(
                   'meta_title'     => 'Zone de livraison - mespaysans.com',
                   'rightColumn'    => false,
                   'breadcrumb'     => $this->breadcrumb->getMenu(),
                   'lat'            => $latlng['lat'],
                   'lng'            => $latlng['lng'],
                   'zoom'           => $latlng['zoom'],
                   'radius'         => $latlng['radius'],
                   'dep'            => MPTools::getCookie('loc'),
               )
           );
        $this->setTemplate('listeVilles.tpl');
    }
}
