<?php
if (!defined('_PS_VERSION_'))
  exit;

class Mphome extends Module
{
    private $isMobile = false;
    private $idf = false;
    
    public static $moduleRoutes = array(
        'statisticMail'=>
            array(
                'controller'    => 'home',
                'rule'          => 'images/{id}/{datenews}/pixel',
                'keywords'  => array(
                    'id'        => array('regexp' => '[0-9]+', 'param' => 'option'),
                    'datenews'  => array('regexp' => '[0-9]+', 'param' => 'jour'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'statisticMail',
                )
            ),
        'informations' => 
            array(
                'controller'    => 'home',
                'rule'          => 'informations/{action}{slashes}',
                'keywords'  => array(
                    'action'    => array('regexp' => '[a-z]+', 'param' => 'action'),
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                )
            ),
        'market-produit' => 
            array(
                'controller'    => 'home',
                'rule'          => 'marches-locaux/{city}/{cp}/{idMarket}/{nameMarket}/jour/{jour}{slashes}',
                'keywords'  => array(
                    'city'          => array('regexp' => '[a-z-]+', 'param' => 'city'),
                    'cp'            => array('regexp' => '[0-9]+', 'param' => 'cp'),
                    'jour'          => array('regexp' => '[a-z]+', 'param' => 'jour'),
                    'nameMarket'    => array('regexp' => '[a-z-]+', 'param' => 'nameMarket'),
                    'idMarket'      => array('regexp' => '[0-9]+', 'param' => 'idMarket'),
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'marketDetail',
                )
            ),
        'market-day' => 
            array(
                'controller'    => 'home',
                'rule'          => 'marches-locaux/jour/{jour}{slashes}',
                'keywords'  => array(
                    'jour'          => array('regexp' => '[a-z]+', 'param' => 'jour'),
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'marketDay',
                )
            ),
        'partenaires-produit' => 
            array(
                'controller'    => 'home',
                'rule'          => 'marches-locaux/{city}/{cp}/{idMarket}/{nameMarket}/jour/{jour}/{category}{slashes}',
                'keywords'  => array(
                    'city'          => array('regexp' => '[a-z-]+', 'param' => 'city'),
                    'cp'            => array('regexp' => '[0-9]+', 'param' => 'cp'),
                    'jour'          => array('regexp' => '[a-z]+', 'param' => 'jour'),
                    'nameMarket'    => array('regexp' => '[a-z-]+', 'param' => 'nameMarket'),
                    'idMarket'      => array('regexp' => '[0-9]+', 'param' => 'idMarket'),
                    'category'       => array('regexp' => '[a-z-]+', 'param' => 'category'),
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'marketcategory',
                )
            ),
        'partenaires-produit-petit-creux' => 
            array(
                'controller'    => 'home',
                'rule'          => 'marches-locaux/{city}/{cp}/{idMarket}/{nameMarket}/jour/{jour}/{category}/petit-creux{slashes}',
                'keywords'  => array(
                    'city'          => array('regexp' => '[a-z-]+', 'param' => 'city'),
                    'cp'            => array('regexp' => '[0-9]+', 'param' => 'cp'),
                    'jour'          => array('regexp' => '[a-z]+', 'param' => 'jour'),
                    'nameMarket'    => array('regexp' => '[a-z-]+', 'param' => 'nameMarket'),
                    'idMarket'      => array('regexp' => '[0-9]+', 'param' => 'idMarket'),
                    'category'       => array('regexp' => '[a-z-]+', 'param' => 'category'),
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'marketcategory',
                    'option'    => 'petit-creux',
                )
            ),
        'category' => 
            array(
                'controller'    => 'home',
                'rule'          => 'partenaires/{nameCategory}{slashes}',
                'keywords'  => array(
                    'nameCategory'  => array('regexp' => '[a-z-]+', 'param' => 'nameCategory'),
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'fullnature',
                )
            ),
        'course-rapide' => 
            array(
                'controller'    => 'home',
                'rule'          => 'un-petit-creux{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'unpetitcreux',
                )
            ),
        'carte' => 
            array(
                'controller'    => 'gmap',
                'rule'          => 'marches-locaux/carte{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'carte',
                )
            ),
        'paniers' => 
            array(
                'controller'    => 'home',
                'rule'          => 'paniers/semaine{numDep}{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                    'numDep'    => array('regexp' => '[/a-z]?', 'param' => 'numDep'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'paniersemaine',
                )
            ),
        'paniers-jour' => 
            array(
                'controller'    => 'home',
                'rule'          => 'paniers/jour/{jour}{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                    'jour'  => array('regexp' => '[a-z-]+', 'param' => 'jour'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'paniersjour',
                )
            ),
        'marchesemaine' => 
            array(
                'controller'    => 'home',
                'rule'          => 'marches-locaux/semaine{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'marchesemaine'
                )
            ),
        'panierrecette' => 
            array(
                'controller'    => 'home',
                'rule'          => 'paniers/recettes{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'paniersrecette'
                )
            ),
        'paysans' =>
            array(
                'controller'    => 'home',
                'rule'          => 'paysans/liste{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'paysans'
                )
            ),
        'nous-rejoindre' => 
            array(
                'controller'    => 'home',
                'rule'          => 'paysans/nousrejoindre{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'nousrejoindre'
                )
            ),
        'liste-villes' => 
            array(
                'controller'    => 'gmap',
                'rule'          => 'livraison/villes{slashes}',
                'keywords'  => array(
                    'slashes'   => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'listeVilles'
                )
            ),
        'liste-villes-dep' => 
            array(
                'controller'    => 'gmap',
                'rule'          => 'livraison/villes/{nomDep}{slashes}',
                'keywords'  => array(
                    'slashes'   => array('regexp' => '/?', 'param' => 'slashes'),
                    'nomDep'    => array('regexp' => '[a-z]+', 'param' => 'nomDep'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'listeVilles'
                )
            ),
        'paniers-dep' => 
            array(
                'controller'    => 'home',
                'rule'          => 'paniers/semaine/{nomDep}{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                    'nomDep'    => array('regexp' => '[a-z]+', 'param' => 'nomDep'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mphome',
                    'action'    => 'paniersemaine',
                )
            ),
        
    );

    public function __construct()
    {
        $this->name = 'mphome';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Julien Ledieu';
        
        parent::__construct();

        $this->displayName = $this->l('Home - Mes Paysans');
        $this->description = $this->l('Home mespaysans.com');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {   // && $this->registerHook('displayLeftColumn')
        return parent::install() && $this->createTables() && $this->registerHook('ModuleRoutes') && $this->registerHook('header') && $this->registerHook('displayHome') && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
       return parent::uninstall() && $this->deleteTables();
    }

    protected function createTables()
    {
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'newslettermarche` (
                `id_newmarche` int NOT NULL AUTO_INCREMENT,
                `cp` varchar(5) NOT NULL,
                `email` varchar(128) NOT NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_newmarche`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
        
        $res .= (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'newSupplier` (
                `id_newsupplier` int NOT NULL AUTO_INCREMENT,
                `nom` varchar(128) NOT NULL,
                `prenom` varchar(128) NOT NULL,
                `tel` varchar(14) NOT NULL,
                `email` varchar(128) NOT NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_newsupplier`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
        
        $res .= (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'ville_livraison` (
            `code_postal` int(11) NOT NULL,
            `name` varchar(30) COLLATE utf8_bin NOT NULL,
            `code_departement` int(11) NOT NULL,
            PRIMARY KEY (`code_postal`),
            KEY `code_departement` (`code_departement`)
           ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
        
        $res .= (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'departement` (
            `id_departement` int(11) NOT NULL,
            `name` varchar(30) COLLATE utf8_bin NOT NULL,
            PRIMARY KEY (`id_departement`)
           ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
        
        return $res;
    }
    
    protected function deleteTables() {
       return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'newslettermarche`;
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'newSupplier`;
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'departement_livraison`;
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'region`;
        '); 
    }

    public function hookModuleRoutes()
    {
        return self::$moduleRoutes;
    }
    
    private function initializeParams() {
        $mobileDetect = new MobileDetect();
        if($mobileDetect->isMobile()) {
            $this->isMobile = true;
        }
        $now = new DateTime();
        $today = $now->format('N');
        if($today==7 || $today==1) {
            $cartDay = 2;
        } else {
            $cartDay = $today+1;
        }
        $isLogged = $this->context->customer->isLogged();
        $firstname = '';
        $lastname = '';
        if($isLogged) {
            $firstname = $this->context->customer->firstname;
            $lastname = $this->context->customer->lastname;
        }
        $showPopUpCP = false;
        $cookieLoc = filter_input(INPUT_COOKIE, 'loc');
        if($cookieLoc) {
            switch(strtolower($cookieLoc)) {
                case DepartementLivraison::IDF:
                    $this->idf = true;
                    break;
                default:
                    $this->idf = false;
            }
        } else {
            $isCp = Tools::getIsset('cp');
            if($isCp) {
                $cp = (int) Tools::getValue('cp');
                $isIdf = MPTools::isIDF($cp);
                setcookie('loc', NULL, -1);
                if($isIdf) {
                    $this->idf = true;
                    setcookie('loc', DepartementLivraison::IDF, time()+60*60*24*360, '/');
                } else {
                    setcookie('loc', DepartementLivraison::AQUITAINE, time()+60*60*24*360, '/');
                }
            } else {
                $showPopUpCP = true;
            }
        }

        $this->_loadRegion();

        //$breadCrumb = new breadcrumb(Tools::getValue('controller'), '', $this->idf);
        $dateT = new DateTime();
        $matin = true;
        if($dateT->format('a')=='pm') {
            $dateT->add(new DateInterval('P1D'));
            $matin = false;
        }
        $day = strtolower(MPTools::$listJour[$dateT->format('w')]);
        $showPetitCreux = true;
        if(($day=='dimanche' && $dateT->format('a')=='pm') || ($day=='lundi' && $dateT->format('a')=='am')) {
            $showPetitCreux = false;
        }

        $listAssign = array(
            'isMobile'          => $this->isMobile,
            //'breadcrumb'        => $breadCrumb->getMenuFromController(),
            'cartDay'           => $cartDay,
            'rightColumn'       => false,
            'isLogged'          => $isLogged,
            'firstname'         => $firstname,
            'lastname'          => $lastname,
            'matin'             => $matin,
            'showPetitCreux'    => $showPetitCreux,
            'showPopUpCP'       => $showPopUpCP,
        );

        $controller = Tools::getValue('controller');
        $action = Tools::getValue('action');
        $customeTitle = ($controller=='home' && $action=='unpetitcreux');
        if($customeTitle) {
            $listAssign['meta_title'] = 'mespaysans.com - Un petit creux, mais pas le temps de faire à manger ?';
        }
        if($this->idf) {
            $product = new Product();
            $listPanier = $product->getProductsByIdSupplier(Supplier::MESPAYSANS_IDF);
            $listAssign['listPanierIdf'] = $listPanier;
        }
        $this->context->smarty->assign(
                $listAssign
        );
    }
    
    private function _loadRegion() {
        $listRegion = MPTools::$listRegionName;
        $default = 'aquitaine';
        if($this->idf) {
            $default = 'idf';
        }
        $this->context->smarty->assign(
            array(
                'listRegion'    => $listRegion,
                'default'       => $default,
            )
        );
    }

    public function hookHeader($params)
    {
        $this->initializeParams();
    }
    
    /*public function hookDisplayLeftColumn($param)
    {
        $dateT = new DateTime();
        $matin = true;
        if($dateT->format('a')=='pm') {
            $dateT->add(new DateInterval('P1D'));
            $matin = false;
        }
        $day = strtolower(MPTools::$listJour[$dateT->format('w')]);
        $market = new Market();
        $nbMarket = $market->getNbMarketFromDayAndSupplier($day, array(Category::CATEGORY_ROTISSEUR, Category::CATEGORY_ROTISSEUR_BIO, Category::CATEGORY_TRAITEUR, Category::CATEGORY_PATISSIER, Category::CATEGORY_PATISSIER_BIO, Category::CATEGORY_VINS_BIO, Category::CATEGORY_FROMAGER, Category::CATEGORY_BOULANGER, Category::CATEGORY_BOULANGER_BIO));
        $this->context->smarty->assign(
            array(
                'nbMarket'  => $nbMarket[0],
            )
        );
        return $this->display(__FILE__, 'left.tpl');
    }*/
    
    public function hookDisplayHome($params)
    {
        $this->context->controller->addJS(_THEME_JS_DIR_.'_html5detect.js');
        if($this->idf) {
            $this->context->smarty->assign(
                array(
                    'rightColumn'       => false,
                    'leftColumn'        => false,
                )
            );
            return $this->display(__FILE__, 'home-95.tpl');
        } else {
            $this->context->smarty->assign(
                array(
                    'rightColumn'    => false,
                )
            );
            return $this->display(__FILE__, 'home.tpl');
        }
    }

    public function hookDisplayFooter($params) {
        return $this->display(__FILE__, 'footer.tpl');
    }
}