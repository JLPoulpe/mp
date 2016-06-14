<?php
if (!defined('_PS_VERSION_'))
  exit;

class Mpfacture extends Module
{
    public function __construct()
    {
        $this->name = 'mpfacture';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Julien Ledieu';
        $this->displayName = $this->l('Gestion des factures');
        $this->description = $this->l('Gérer l\'etat des factures producteurs');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        
        parent::__construct();
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'mpfacture/css/facture.css', 'all');
    }

    public function install()
    {   
        return parent::install();
    }

    public function uninstall()
    {
       return parent::uninstall();
    }
}