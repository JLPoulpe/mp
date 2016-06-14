<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class MpMarket extends Module
{
    public static $moduleRoutes = array(
        'market-liste' => array(
                                'controller'    => 'market',
                                'rule'          => '{action}-de-vous{slashes}',
                                'keywords'  => array(
                                    'action'    => array('regexp' => '[a-z]+', 'param' => 'action'),
                                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                                ),
                                'params'    => array(
                                    'fc'        => 'module',
                                    'module'    => 'mpmarket',
                                    'action'    => 'autour'
                                )
                            ),
    );
    
    public function __construct()
    {
        $this->name = 'mpmarket';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->bootstrap = true;
        $this->author = 'Julien Ledieu';

        parent::__construct();

        $this->displayName = $this->l('Marchés');
        $this->description = $this->l('Gestion des marchés');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('ModuleRoutes') && $this->createTables();
    }
    
    public function hookModuleRoutes()
    {
        return self::$moduleRoutes;
    }
    
    /**
    * Creates tables
    */
    protected function createTables()
    {
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'market` (
                `id_market` int(11) NOT NULL AUTO_INCREMENT,
                `address` varchar(255) DEFAULT NULL,
                `postal_code` int(11) DEFAULT NULL,
                `city` varchar(100) DEFAULT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                `active` tinyint(4) NOT NULL,
                `open_at` char(4) NOT NULL,
                `close_at` char(4) NOT NULL,
                `lundi` tinyint(1) NOT NULL DEFAULT \'0\',
                `mardi` tinyint(1) NOT NULL DEFAULT \'0\',
                `mercredi` tinyint(1) NOT NULL DEFAULT \'0\',
                `jeudi` tinyint(1) NOT NULL DEFAULT \'0\',
                `samedi` tinyint(1) NOT NULL DEFAULT \'0\',
                `vendredi` tinyint(1) NOT NULL DEFAULT \'0\',
                `dimanche` tinyint(1) NOT NULL DEFAULT \'0\',
                `latitude` varchar(50) DEFAULT NULL,
                `longitude` varchar(50) DEFAULT NULL,
                PRIMARY KEY (`id_market`),
                KEY `idadresse_idx` (`address`)
              ) ENGINE='._MYSQL_ENGINE_.' AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
        ');

        $res &= (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'market_supplier` (
                id_market_supplier int NOT NULL AUTO_INCREMENT,
                id_market int NOT NULL,
                id_supplier int NOT NULL,
                PRIMARY KEY (`id_market_supplier`),
                KEY `idmarket_idx` (`id_market`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
        
        $res &= (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'market_lang` (
                `id_market` int(11) NOT NULL AUTO_INCREMENT,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(128) NOT NULL,
                `description` text,
                `link_rewrite` varchar(128) NOT NULL,
                `meta_title` varchar(128) DEFAULT NULL,
                `meta_keywords` varchar(255) DEFAULT NULL,
                `meta_description` varchar(255) DEFAULT NULL,
                `id_shop` int(11) DEFAULT NULL,
                PRIMARY KEY (`id_market`,`id_lang`),
                KEY `market_name` (`name`)
              ) ENGINE='._MYSQL_ENGINE_.' AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;');
        
        return $res;
    }
    
    public function uninstall()
    {
        return parent::uninstall() && $this->deleteTables();
    }

    protected function deleteTables() {
       return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'market`, `'._DB_PREFIX_.'market_supplier`, `'._DB_PREFIX_.'market_lang`;
        '); 
    }
}
