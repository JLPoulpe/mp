<?php
if (!defined('_PS_VERSION_'))
  exit;

class Mprecettes extends Module
{
    public static $moduleRoutes = array(
        
    );

    public function __construct()
    {
        $this->name = 'mprecettes';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Julien Ledieu';
        
        parent::__construct();

        $this->displayName = $this->l('Recettes - Mes Paysans');
        $this->description = $this->l('Recettes mespaysans.com');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {   
        return parent::install() && $this->registerHook('displayRightColumn') && $this->registerHook('ModuleRoutes');
    }

    public function uninstall()
    {
       return parent::uninstall();
    }
    
    public function hookModuleRoutes()
    {
        return self::$moduleRoutes;
    }
    
    public function hookDisplayRightColumn($params)
    {
        $service = new Service();
        $listRecettes = $service->getNbRecettes();
        $this->context->smarty->assign(
            array(
                'listRecettes'  => $listRecettes,
            )
        );
        return $this->display(__FILE__, 'columnRightRecettes.tpl');
    }
}