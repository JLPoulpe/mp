<?php
class mphomehomeModuleFrontController extends ModuleFrontController
{
    private $isMobile;
    private $breadcrumb;
    protected $context;
    private $action;
    private $city;
    private $cp;
    private $jour;
    private $idMarket;
    private $nameMarket;
    private $nameCategory;
    private $idSupplier;
    private $option;
    private $category;

    public function __construct()
    {
        parent::__construct();
        $this->context  = Context::getContext();
        $this->action   = Tools::getValue('action');
        $this->city   = Tools::getValue('city');
        $this->cp   = Tools::getValue('cp');
        $this->jour   = Tools::getValue('jour');
        $this->idMarket   = Tools::getValue('idMarket');
        $this->nameMarket   = Tools::getValue('nameMarket');
        $this->nameCategory   = Tools::getValue('nameCategory');
        $this->idSupplier   = Tools::getValue('idSupplier');
        $this->option   = Tools::getValue('option');
        $this->category = Tools::getValue('category');
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
                } elseif (!in_array($this->action, array('marketDetail', 'marchesemaine', 'accueil', 'paniersemaine', 'zonelivraison', 'notredemarche','mentionlegales','cgu','quisommesnous','quisontils','questceagriculture','cookies','nousrejoindre'))) {
                    header('Location:/');
                }
            }
            $this->{$this->action}();
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    public function marketDetail() {
        $loc = filter_input(INPUT_COOKIE, 'loc');
        $market = new Market();
        $marketDto = $market->getMarketFromId($this->idMarket, false, true, true, false, $loc);
        $listOtherMarket = $market->getListMarketFromDay($this->jour, $loc);
        $date = MPTools::getNextDateFromDay($this->jour);
        $dateFinCommande = MPTools::getLimitCommandeDateFromDay($this->jour, false);
        $this->context->smarty->assign(
               array(
                   'meta_title'         => $marketDto->getName() . ' (' . $marketDto->getCity() . ') - mespaysans.com',
                   'controller'         => 1,
                   'marketDto'          => $marketDto,
                   'date'               => MPTools::$listJour[$date->format('w')] . $date->format(' d') . ' ' . MPTools::$listMois[$date->format('n')-1] . $date->format(' Y'),
                   'dateFinCommande'    => MPTools::$listJour[$dateFinCommande->format('w')] . $dateFinCommande->format(' d') . ' ' . MPTools::$listMois[$dateFinCommande->format('n')-1] . $dateFinCommande->format(' Y'),
                   'datewithdrawal'     => $date->format('Y-m-d'),
                   'jour'               => $this->jour,
                   'isMobile'           => $this->isMobile,
                   'cp'                 => $this->cp,
                   'urlcity'            => $this->city,
                   'city'               => ucfirst($this->city),
                   'breadcrumb'         => $this->breadcrumb->getMenu(),
                   'rightColumn'        => true,
                   'listOtherMarket'    => $listOtherMarket,
               )
           );
        if($loc==DepartementLivraison::AQUITAINE) {
            $this->setTemplate('marketdetail.tpl');
        } else {
            $this->setTemplate('marketdetailIDF.tpl');
        }
    }

    public function marketcategory() {
        $listIdSupplier = $idCategory = array();
        switch ($this->category) {
            case 'vins-bio' :
                $titleName = 'Les Vins Bio';
                $listIdSupplier = array(28, 41);
                $idCategory = array(Category::CATEGORY_VINS_BIO);
                break;
            case 'rotisserie' :
                $titleName = 'Les rotisseries';
                $idCategory = array(Category::CATEGORY_ROTISSEUR, Category::CATEGORY_ROTISSEUR_BIO);
                break;
            case 'traiteur' :
                $titleName = 'Les traiteurs';
                $idCategory = array(Category::CATEGORY_TRAITEUR);
                break;
            case 'patisserie' :
                $titleName = 'Les patisseries';
                $idCategory = array(Category::CATEGORY_PATISSIER, Category::CATEGORY_PATISSIER_BIO);
                break;
            case 'boulangerie' :
                $titleName = 'Les boulangeries';
                $idCategory = array(Category::CATEGORY_BOULANGER, Category::CATEGORY_BOULANGER_BIO);
                break;
            case 'fromagerie' :
                $titleName = 'Les fromageries';
                $idCategory = array(Category::CATEGORY_FROMAGER);
                break;
        }
        $market = new Market();
        $marketDto = $market->getMarketFromIdAndIdSupplierAndIdCategory($this->idMarket, $listIdSupplier, $idCategory, false, true, true);
        $nbProduct = 0;
        $metaTitle = $titleName;
        if($marketDto) {
            $metaTitle = $titleName . ' - ' . $marketDto->getName() . ' (' . $marketDto->getCity() . ') - mespaysans.com';
            $listProduit = $marketDto->getListProduits();
            $nbProduct = count($listProduit);
        }
        $useDelay = true;
        $dateNow = new DateTime();
        if($dateNow->format('a')=='am') {
            $joursOK[] = strtolower(MPTools::$listJour[$dateNow->format('w')]);
        }
        $joursOK[] = strtolower(MPTools::$listJour[$dateNow->format('w')+1]);
        if($this->option=='petit-creux' && in_array($this->jour, $joursOK)) {
            $useDelay = false;
        }

        $date = MPTools::getNextDateFromDay($this->jour, $useDelay);
        $this->context->smarty->assign(
               array(
                   'meta_title'     => $metaTitle,
                   'controller'     => 1,
                   'marketDto'      => $marketDto,
                   'nbProduct'      => $nbProduct,
                   'date'           => MPTools::$listJour[$date->format('w')] . $date->format(' d') . ' ' . MPTools::$listMois[$date->format('n')-1] . $date->format(' Y'),
                   'datewithdrawal' => $date->format('Y-m-d'),
                   'jour'           => $this->jour,
                   'isMobile'       => $this->isMobile,
                   'cp'             => $this->cp,
                   'urlcity'        => $this->city,
                   'city'           => ucfirst($this->city),
                   'breadcrumb'     => $this->breadcrumb->getMenu(),
                   'rightColumn'    => true,
               )
           );
        
        $this->setTemplate('marketdetailpetitcreux.tpl');
    }
    
    public function fullnature() {
        $market = new Market();
        $listMarketDto = $market->getListMarketFromListSupplier(array(28,41));
        $listMarketDtoSort = array();
        foreach($listMarketDto as $marketDto) {
            $listMarketDtoSort[$marketDto->getNextDateOpenWithFormat('Ymd')][] = $marketDto;
        }
        ksort($listMarketDtoSort);
        $this->context->smarty->assign(
               array(
                   'meta_title'          => 'Vos Vins Bio - mespaysans.com',
                   'nameCategory'       => $this->nameCategory,
                   'idSupplier'         => $this->idSupplier,
                   'breadcrumb'         => $this->breadcrumb->getMenu(),
                   'rightColumn'        => false,
                   'listMarketDto'      => $listMarketDtoSort,
               )
           );
        $this->setTemplate('partenaires.tpl');
    }

    public function marketDay() {
        $market = new Market();
        $listMarketDto = $market->getListMarketFromDay($this->jour);
        $this->context->smarty->assign(
               array(
                   'meta_title'         => 'les marchés locaux du ' . $this->jour . ' - mespaysans.com',
                   'breadcrumb'         => $this->breadcrumb->getMenu(),
                   'rightColumn'        => false,
                   'listMarketDto'      => $listMarketDto,
                   'jour'               => $this->jour,
               )
           );
        $this->setTemplate('marketday.tpl');
    }

    public function unpetitcreux() {
        $dateT = new DateTime();
        $matin = true;
        if($dateT->format('a')=='pm') {
            $dateT->add(new DateInterval('P1D'));
            $matin = false;
        }
        $day = strtolower(MPTools::$listJour[$dateT->format('w')]);
        $market = new Market();
        $nbMarket = $market->getNbMarketFromDayAndSupplier($day, array(Category::CATEGORY_ROTISSEUR, Category::CATEGORY_ROTISSEUR_BIO, Category::CATEGORY_TRAITEUR, Category::CATEGORY_PATISSIER, Category::CATEGORY_PATISSIER_BIO, Category::CATEGORY_VINS_BIO, Category::CATEGORY_FROMAGER, Category::CATEGORY_BOULANGER, Category::CATEGORY_BOULANGER_BIO));
        if($nbMarket>0) {
            $listMarketRotisserie = $market->getMarketFromDayAndSupplier($day, array(Category::CATEGORY_ROTISSEUR, Category::CATEGORY_ROTISSEUR_BIO));
            $listMarketTraiteur = $market->getMarketFromDayAndSupplier($day, array(Category::CATEGORY_TRAITEUR));
            $listMarketPatisserie = $market->getMarketFromDayAndSupplier($day, array(Category::CATEGORY_PATISSIER, Category::CATEGORY_PATISSIER_BIO));
            $listMarketVinsBio = $market->getMarketFromDayAndSupplier($day, array(Category::CATEGORY_VINS_BIO));
            $listMarketFromager = $market->getMarketFromDayAndSupplier($day, array(Category::CATEGORY_FROMAGER));
            $listMarketBoulanger = $market->getMarketFromDayAndSupplier($day, array(Category::CATEGORY_BOULANGER, Category::CATEGORY_BOULANGER_BIO));
            $this->context->smarty->assign(
                   array(
                       'meta_title'             => 'Un petit creux - mespaysans.com',
                       'listMarketRotisserie'   => $listMarketRotisserie,
                       'listMarketTraiteur'     => $listMarketTraiteur,
                       'listMarketPatisserie'   => $listMarketPatisserie,
                       'listMarketVinsBio'      => $listMarketVinsBio,
                       'listMarketFromager'     => $listMarketFromager,
                       'listMarketBoulanger'    => $listMarketBoulanger,
                       'breadcrumb'             => $this->breadcrumb->getMenu(),
                       'rightColumn'            => false,
                   )
               );
        } else {
            $this->context->smarty->assign(
                array(
                    'meta_title'    => 'Un petit creux - mespaysans.com',
                    'nbMarket'      => $nbMarket,
                    'matin'         => $matin,
                    'breadcrumb'    => $this->breadcrumb->getMenu(),
                    'rightColumn'   => false,
                )
            );
        }
        $this->setTemplate('unpetitcreux.tpl');
    }

    public function paniersemaine() {
        $product = new Product();
        $listPanier = $product->getDistinctProductByCategoryAndSupplier(Category::CATEGORY_PANIER, Supplier::getMPByRegion());
        $listDays = array();
        foreach($listPanier as $productDto) {
            $date = MPTools::getNextDateFromDay($productDto->getReference());
            $listDays[$date->format('dmY')]['jour'] = $productDto->getReference();
            $listDays[$date->format('dmY')]['img'] = $productDto->getImgPath('panier');
            $listDays[$date->format('dmY')]['date'] = $date->format('d/m/Y');
        }
        ksort($listDays);
        $this->context->smarty->assign(
               array(
                   'meta_title'     => 'Les paniers de vos marchés - mespaysans.com',
                   'listDays'       => $listDays,
                   'breadcrumb'     => $this->breadcrumb->getMenu(),
                   'rightColumn'    => false,
               )
           );
        $this->setTemplate('paniersemaine.tpl');
    }

    public function paniersIDF() {
        $product = new Product();
        $listPanier = $product->getProductByCategoryAndSupplier(Category::CATEGORY_PANIER, Supplier::getMPByRegion());
        $aPanier = $aPanierSemaine = array();
        foreach($listPanier as $productDto) {
            $price = $productDto->getUnitPrice();
            $reduction = 0;
            $reductionCost = 0;
            $priceReduction = 0;
            if($productDto->getOnSale()) {
                $reduction = 1;
                $reductionCost = number_format($productDto->getSpecificPrice()*$productDto->getReduction(), 2, ',', ' ');
                $priceReduction = number_format($price+$productDto->getSpecificPrice()*$productDto->getReduction(), 2, ',', ' ');
            }
            $reference = $productDto->getReference();
            $tmp = array(
                'id'  => $productDto->getIdProduct(),
                'name'  => $productDto->getProductName(),
                'price' => $price,
                'reduction' => $reduction,
                'reductionCost' => $reductionCost,
                'priceReduction' => $priceReduction,
                'description'  => $productDto->getDescription(),
                'image' => $productDto->getImgPath(),
            );
            if($reference=='semaine') {
                $listeDate = MPTools::getListDays(false, 7, 6);
                $tmp['listeDate'] = $listeDate;
                $tmp['date'] = current(array_keys($listeDate));
                $aPanierSemaine[] = $tmp;
            } else {
                $listeDate = MPTools::getListDays(false, 7, 1);
                $tmp['listeDate'] = $listeDate;
                $tmp['date'] = current(array_keys($listeDate));
                $aPanier[] = $tmp;
            }
        }
        $this->context->smarty->assign(
               array(
                   'meta_title'         => 'Les paniers de vos marchés - mespaysans.com',
                   'aPanier'            => $aPanier,
                   'aPanierSemaine'     => $aPanierSemaine,
                   'nameCategory'       => $this->nameCategory,
                   'idSupplier'         => $this->idSupplier,
                   'breadcrumb'         => $this->breadcrumb->getMenu(),
                   'rightColumn'        => true,
                   'jour'               => $this->jour,
               )
           );
        $this->setTemplate('paniersIDF.tpl');
    }
    
    public function paniersrecette() {
        $product = new Product();
        $listPanier = $product->getProduct2ByCategoryAndSupplier(Category::CATEGORY_RECETTE, Supplier::getMPByRegion());
        $aPanier = array();
        foreach($listPanier as $productDto) {
            $price = $productDto->getUnitPrice();
            $listImage = $productDto->getListImage('large_default');
            $tmp = array(
                'id'  => $productDto->getIdProduct(),
                'name'  => $productDto->getProductName(),
                'price' => $price,
                'description'  => $productDto->getDescription(),
                'image' => $listImage[0],
            );
            if(isset($listImage[1])) {
                $tmp['recette'] = $listImage[1];
            } else {
                $tmp['recette'] = $listImage[0];
            }
            $listeDate = MPTools::getListDays();
            $tmp['listeDate'] = $listeDate;
            $tmp['date'] = current(array_keys($listeDate));
            $aPanier[] = $tmp;
        }
        $this->context->smarty->assign(
               array(
                   'meta_title'         => 'Les recettes pour bébé - mespaysans.com',
                   'aPanier'            => $aPanier,
                   'nameCategory'       => $this->nameCategory,
                   'idSupplier'         => $this->idSupplier,
                   'breadcrumb'         => $this->breadcrumb->getMenu(),
                   'rightColumn'        => true,
                   'jour'               => $this->jour,
               )
           );
        $this->setTemplate('panier-recette.tpl');
    }
    
    public function paniersjour() {
        $product = new Product();
        $listPanier = $product->getProductByCategoryAndDay(Category::CATEGORY_PANIER, $this->jour);
        $aPanier = array();
        foreach($listPanier as $productDto) {
            $date = MPTools::getNextDateFromDay($productDto->getReference());
            $price = $productDto->getUnitPrice();
            $reduction = 0;
            $reductionCost = 0;
            $priceReduction = 0;
            if($productDto->getOnSale()) {
                $reduction = 1;
                $reductionCost = number_format($productDto->getSpecificPrice()*$productDto->getReduction(), 2, ',', ' ');
                $priceReduction = number_format($price+$productDto->getSpecificPrice()*$productDto->getReduction(), 2, ',', ' ');
            }
            $tmp = array(
                'id'  => $productDto->getIdProduct(),
                'name'  => $productDto->getProductName(),
                'price' => $price,
                'reduction' => $reduction,
                'reductionCost' => $reductionCost,
                'priceReduction' => $priceReduction,
                'description'  => $productDto->getDescription(),
                'image' => $productDto->getImgPath(),
                'date'  => $date->format('d/m/y'),
                'dateWithDrawal' => $date->format('Y-m-d'),
            );
            $aPanier[] = $tmp;
        }
        $this->context->smarty->assign(
               array(
                   'meta_title'         => 'Vos paniers du ' . $this->jour . ' - mespaysans.com',
                   'aPanier'            => $aPanier,
                   'nameCategory'       => $this->nameCategory,
                   'idSupplier'         => $this->idSupplier,
                   'breadcrumb'         => $this->breadcrumb->getMenu(),
                   'rightColumn'        => true,
                   'jour'               => $this->jour
               )
           );
        $this->setTemplate('paniersjour.tpl');
    }

    public function marchesemaine() {
        $loc = filter_input(INPUT_COOKIE, 'loc');
        $now        = new DateTime();
        $nextDay    = clone($now);
        $nextDay->modify('+1 day');
        $listJour = array();
        $i=1;
        do {
            $nextDay->modify('+1 day');
            $theDay = clone($nextDay);
            if ($theDay->format('w')!=1) {
                $listJour[$nextDay->format('Ymd')]['displayDate'] = MPTools::$listJour[$theDay->format('w')] . '<br />' . $theDay->format('d') . ' ' . MPTools::$listMoisDiffere[$theDay->format('n')];
                $listJour[$nextDay->format('Ymd')]['jour'] = strtolower(MPTools::$listJour[$theDay->format('w')]);
            }
            $i++;
        } while($i<8);
        $market = new Market();
        $listMarket = $market->getAllMarkets('ml.name, m.id_market, m.address, m.postal_code, m.city, m.open_at, m.close_at, m.lundi, m.mardi, m.mercredi, m.jeudi, m.vendredi, m.samedi, m.dimanche', 'ORDER BY open_at, m.city', false, false, false, $loc);
        foreach($listMarket as $marketDto) {
            $listJour = $this->setAvailableDayForMarket($listJour, $marketDto);
        }
        $this->context->smarty->assign(
               array(
                   'meta_title' => 'Vos marchés de la semaine - mespaysans.com',
                   'breadcrumb' => $this->breadcrumb->getMenu(),
                   'rightColumn'=> false,
                   'listJour'   => $listJour,
                   'loc'        => $loc,
               )
           );
        if($loc==DepartementLivraison::AQUITAINE) {
            $this->setTemplate('marchesemaine.tpl');
        } else {
            $this->setTemplate('marchesemaineIDF.tpl');
        }
    }
    
    public function paysans() {
        $supplier = new Supplier();
        $listSupplier = $supplier->getFromDepartementMarket(DepartementLivraison::GIRONDE);
        $listSupplierWithDate = array();
        $market = new Market();
        foreach($listSupplier as $key=>$supplierDto) {
            $listMarket = $market->getMarketFromIdSupplier($supplierDto->getIdSupplier());
            $listSupplierWithDate[$supplierDto->getMetaDescription()][$key]['supplierDto'] = $supplierDto;
            $listSupplierWithDate[$supplierDto->getMetaDescription()][$key]['marketsDto'] = $listMarket;
        }
        ksort($listSupplierWithDate);
        $this->context->smarty->assign(
               array(
                   'meta_title'             => 'Vos paysans - mespaysans.com',
                   'breadcrumb'             => $this->breadcrumb->getMenu(),
                   'rightColumn'            => false,
                   'listSupplierWithDate'   => $listSupplierWithDate,
               )
           );
        $this->setTemplate('paysans.tpl');
    }

    private function setAvailableDayForMarket($array, $marketDto) {
        if($marketDto->getMardi()) {
            $date = MPTools::getNextDateFromDay('mardi');
            $array[$date->format('Ymd')]['marketDto'][] = $marketDto;
        }
        if($marketDto->getMercredi()) {
            $date = MPTools::getNextDateFromDay('mercredi');
            $array[$date->format('Ymd')]['marketDto'][] = $marketDto;
        }
        if($marketDto->getJeudi()) {
            $date = MPTools::getNextDateFromDay('jeudi');
            $array[$date->format('Ymd')]['marketDto'][] = $marketDto;
        }
        if($marketDto->getVendredi()) {
            $date = MPTools::getNextDateFromDay('vendredi');
            $array[$date->format('Ymd')]['marketDto'][] = $marketDto;
        }
        if($marketDto->getSamedi()) {
            $date = MPTools::getNextDateFromDay('samedi');
            $array[$date->format('Ymd')]['marketDto'][] = $marketDto;
        }
        if($marketDto->getDimanche()) {
            $date = MPTools::getNextDateFromDay('dimanche');
            $array[$date->format('Ymd')]['marketDto'][] = $marketDto;
        }
        
        return $array;
    }
    
    public function mentionlegales() {
        $cms = new CMS(10);
        $this->context->smarty->assign(
            array(
                'hideTop'       => true,
                'meta_title'    => 'Mentions légales - mespaysans.com',
                'noRightColumn' => true,
                'content'       => $cms->content[2],
            )
        );
        $this->setTemplate('mentionlegales.tpl');
    }
    
    public function cgu() {
        $cms = new CMS(3);
        $this->context->smarty->assign(
            array(
                'hideTop'       => true,
                'meta_title'    => 'Conditions générales d\'utilisations - mespaysans.com',
                'noRightColumn' => true,
                'content'       => $cms->content[2],
                
            )
        );
        $this->setTemplate('cgu.tpl');
    }

    public function quisontils() {
        $cms = new CMS(8);
        $this->context->smarty->assign(
            array(
                'hideTop'       => true,
                'meta_title'    => 'Qui sont-ils ? - mespaysans.com',
                'noRightColumn' => true,
                'content'       => $cms->content[2],
            )
        );
        $this->setTemplate('quisontils.tpl');
    }
    
    public function questceagriculture() {
        $cms = new CMS(9);
        $this->context->smarty->assign(
            array(
                'hideTop'       => true,
                'meta_title'    => 'Qu\'est-ce que l\'agriculture ? - mespaysans.com',
                'noRightColumn' => true,
                'content'       => $cms->content[2],
            )
        );
        $this->setTemplate('questceagriculture.tpl');
    }

    public function notredemarche() {
        $cms = new CMS(7);
        $this->context->smarty->assign(
            array(
                'hideTop'       => true,
                'meta_title'    => 'Notre démarche - mespaysans.com',
                'noRightColumn' => true,
                'content'       => $cms->content[2],
            )
        );
        $this->setTemplate('notredemarche.tpl');
    }
    
    public function cookies() {
        $this->setTemplate('cookies.tpl');
    }
    
    public function nousrejoindre() {
        $err = $listAssign = array();
        if(Tools::getIsset('form') && Tools::getValue('form')=='sent') {
            if(Tools::getIsset('nom')) {
                $nom = Tools::getValue('nom');
                if(!MPTools::isName($nom)) {
                    $nom='';
                    $err['nom'] = 'invalide';
                }
            } else {
                $err['nom'] = 'needed';
            }
            if(Tools::getIsset('prenom')) {
                $prenom = Tools::getValue('prenom');
                if(!MPTools::isName($prenom)) {
                    $prenom='';
                    $err['prenom'] = 'invalide';
                }
            } else {
                $err['prenom'] = 'needed';
            }
            if(Tools::getIsset('tel')) {
                $tel = Tools::getValue('tel');
                if(!MPTools::isTel($tel)) {
                    $tel = '';
                    $err['tel'] = 'invalide';
                }
            } else {
                $err['tel'] = 'needed';
            }
            if(Tools::getIsset('email')) {
                $email = Tools::getValue('email');
                if(!MPTools::isEmail($email)) {
                    $email='';
                    $err['email'] = 'invalide';
                }
            } else {
                $err['email'] = 'needed';
            }
            
            $ok = '';
            if(empty($err)) {
                $newSupplier = new NewSupplier();
                $ok = $newSupplier->add($email, $prenom, $nom, $tel);
            }
            
            $listAssign = array(
                   'err'        => $err,
                   'nom'        => $nom,
                   'prenom'     => $prenom,
                   'tel'        => $tel,
                   'email'      => $email,
                   'ok'         => $ok,
               );
        }
        $listAssign['meta_title'] = 'Vous êtes producteur ou artisan ? - mespaysans.com';
        $this->context->smarty->assign(
               $listAssign
            );
        $this->setTemplate('nousrejoindre.tpl');
    }
    
    public function statisticMail() {
        $idUser = $this->option;
        $dateNews = $this->jour;
        $statisticsNews = new statisticsNews();
        $statisticsNews->addOpen($idUser, $dateNews);

        header('Content-Type: image/png');
        echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
    }
}
