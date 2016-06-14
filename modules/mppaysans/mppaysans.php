<?php
if (!defined('_PS_VERSION_'))
  exit;

class Mppaysans extends Module
{
    public function __construct()
    {
        $this->name = 'mppaysans';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Julien Ledieu';
        
        parent::__construct();

        $this->displayName = $this->l('Associations Paysans - Compte Client');
        $this->description = $this->l('Gestion des comptes paysans');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install() && $this->createTables();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->deleteTables();
    }

    protected function createTables()
    {
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE `'._DB_PREFIX_.'supplier_account` (
                `id_supplier_account` int(11) NOT NULL AUTO_INCREMENT,
                `id_supplier` int(11) NOT NULL,
                `id_account` int(11) NOT NULL,
                `code_etablissement` varchar(6) NOT NULL,
                `code_guichet` varchar(6) NOT NULL,
                `numero_compte` varchar(11) NOT NULL,
                `cle_rib` varchar(2) NOT NULL,
                `iban` varchar(34) NOT NULL,
                `code_bic` varchar(11) NOT NULL,
                PRIMARY KEY (`id_supplier_account`),
                KEY `id_supplier` (`id_supplier`,`id_account`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
        
        return $res;
    }
    
    protected function deleteTables() {
       return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'supplier_account`;
        '); 
    }
}