<?php
if (!defined('_PS_VERSION_'))
  exit;

class Mespaysans extends Module
{
    public static $moduleRoutes = array(
        'je-fais-mon-marche'=>
            array(
                'controller'    => 'mespaysans',
                'rule'          => 'je-fais-mon-marche{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mespaysans',
                    'action'    => 'jefaismonmarche',
                )
            ),
        'paniers-du-jour' => 
            array(
                'controller'    => 'mespaysans',
                'rule'          => 'les-paniers-du-{jour}{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                    'jour'          => array('regexp' => '[a-z-]+', 'param' => 'jour'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mespaysans',
                    'action'    => 'paniersjour',
                )
            ),
        'les-produits-du-jour'=>
            array(
                'controller'    => 'mespaysans',
                'rule'          => 'les-produits-du-{jour}/categorie/{categoryId}/{categoryName}{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                    'jour'          => array('regexp' => '[a-z]+', 'param' => 'jour'),
                    'categoryId'    => array('regexp' => '[0-9]+', 'param' => 'categoryId'),
                    'categoryName'  => array('regexp' => '[a-z-]+', 'param' => 'categoryName'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mespaysans',
                    'action'    => 'lesproduitsdujour',
                )
            ),
        'les-produits-du-jour-bio'=>
            array(
                'controller'    => 'mespaysans',
                'rule'          => 'les-produits-du-{jour}/categorie/{categoryId}/{categoryName}/bio{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                    'jour'          => array('regexp' => '[a-z]+', 'param' => 'jour'),
                    'categoryId'    => array('regexp' => '[0-9]+', 'param' => 'categoryId'),
                    'categoryName'  => array('regexp' => '[a-z-]+', 'param' => 'categoryName'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mespaysans',
                    'action'    => 'lesproduitsdujourbio',
                )
            ),
        'toutes-les-recettes'=>
            array(
                'controller'    => 'recettes',
                'rule'          => 'toutes-les-recettes{slashes}',
                'keywords'  => array(
                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mprecettes',
                    'action'    => 'touteslesrecettes',
                )
            ),
        'recette'=>
            array(
                'controller'    => 'recettes',
                'rule'          => 'recette/{cmsId}/{cmsRewrite}{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                    'cmsId'         => array('regexp' => '[0-9]+', 'param' => 'cmsId'),
                    'cmsRewrite'    => array('regexp' => '[a-z-]+', 'param' => 'cmsRewrite'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mprecettes',
                    'action'    => 'recetteByCmsId',
                )
            ),
        'espacePro'=>
            array(
                'controller'    => 'mespaysans',
                'rule'          => 'espace-pro{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mespaysans',
                    'action'    => 'espacePro',
                )
            ),
        'espaceProFacture'=>
            array(
                'controller'    => 'mespaysans',
                'rule'          => 'espace-pro/facture{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mespaysans',
                    'action'    => 'espaceProFacture',
                )
            ),
        'liste-paysans'=>
            array(
                'controller'    => 'home',
                'rule'          => 'mespaysans/la-liste{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mppaysans',
                    'action'    => 'listePaysans',
                )
            ),
        'chef-jesus'=>
            array(
                'controller'    => 'home',
                'rule'          => 'chef-jesus{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mppaysans',
                    'action'    => 'chefjesus',
                )
            ),
        'livraison'=>
            array(
                'controller'    => 'home',
                'rule'          => 'livraison{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mppaysans',
                    'action'    => 'livraison',
                )
            ),
        'partenaire'=>
            array(
                'controller'    => 'home',
                'rule'          => 'nos-partenaires{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mppaysans',
                    'action'    => 'partenaire',
                )
            ),
        'vinsbio'=>
            array(
                'controller'    => 'home',
                'rule'          => 'nos-vins-bio{slashes}',
                'keywords'  => array(
                    'slashes'       => array('regexp' => '/?', 'param' => 'slashes'),
                ),
                'params'    => array(
                    'fc'        => 'module',
                    'module'    => 'mppaysans',
                    'action'    => 'vinsbio',
                )
            ),
    );

    public function __construct()
    {
        $this->name = 'mespaysans';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Julien Ledieu';
        
        parent::__construct();

        $this->displayName = $this->l('Mespaysans V2');
        $this->description = $this->l('Version V2 de mespaysans');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {   
        return parent::install() && $this->registerHook('ModuleRoutes') && $this->registerHook('displayHome') && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
       return parent::uninstall();
    }

    public function hookModuleRoutes()
    {
        return self::$moduleRoutes;
    }
    
    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign(
            array(
                'noRightColumn' => true,
                'isHP'          => true,
                'hideTop'       => true,
            )
        );

        //$this->context->controller->addJS(_THEME_JS_DIR_.'_html5detect.js');
        return $this->display(__FILE__, 'home.tpl');
    }
    public function hookDisplayFooter($params)
    {
        return $this->display(__FILE__, 'footer.tpl');
    }
}