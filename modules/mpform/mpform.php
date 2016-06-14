<?php
if (!defined('_PS_VERSION_'))
  exit;

class Mpform extends Module
{
    public static $moduleRoutes = array(
        'rejoindre-mespaysans' => array(
                                'controller'    => 'form',
                                'rule'          => 'formulaire/{action}{slashes}',
                                'keywords'  => array(
                                    'action'        => array('regexp' => '[a-z]+', 'param' => 'action'),
                                    'slashes'  => array('regexp' => '/?', 'param' => 'slashes'),
                                ),
                                'params'    => array(
                                    'fc'        => 'module',
                                    'module'    => 'mpform',
                                )
                            ),
    );
    
    public function __construct()
    {
        $this->name = 'mpform';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Julien Ledieu';
        
        parent::__construct();

        $this->displayName = $this->l('Mes Paysans - Formulaires');
        $this->description = $this->l('mespaysans.com - Formulaires');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }
    
    public function hookModuleRoutes()
    {
        return self::$moduleRoutes;
    }
    
    public function install()
    {
        return parent::install() && $this->registerHook('ModuleRoutes') && $this->updateTable();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    
    public function updateTable() {
        $res = (bool)Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'cart_product '
                . 'ADD `id_supplier` INT NOT NULL, '
                . 'ADD `id_market` INT NOT NULL, '
                . 'ADD `date_withdrawal` DATETIME NOT NULL, '
                . 'ADD `status` INT NOT NULL, '
                . 'ADD INDEX ( `status` ), '
                . 'ADD INDEX ( `id_supplier` ), '
                . 'ADD INDEX ( `id_market` ); '
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'cart_product` DROP PRIMARY KEY; '
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'image_shop` DROP PRIMARY KEY; '
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'image_shop` DROP INDEX `id_product` ,'
                . 'ADD UNIQUE `id_image` ( `id_image` , `id_shop` , `cover` ) COMMENT \'\'; '
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'product` ADD `owner_product` TINYINT( 1 ) NOT NULL AFTER `online_only`; '
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'product_attribute` DROP INDEX `product_default` ,'
                . 'ADD INDEX `product_default` ( `id_product` , `default_on` ) COMMENT \'\'; '
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'product_attribute_shop` DROP INDEX `id_product` ;'
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'supplier` ADD `withdrawal` TINYINT( 1 ) NOT NULL ;'
                . 'ALTER TABLE `' . _DB_PREFIX_ . 'product` ADD `mp_com` INT NOT NULL ; ');
        
        return $res;
    }
}
