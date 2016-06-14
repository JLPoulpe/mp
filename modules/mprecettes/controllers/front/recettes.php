<?php
class mprecettesrecettesModuleFrontController extends ModuleFrontController
{
    private $cmsId;

    public function __construct()
    {
        parent::__construct();
        $this->context  = Context::getContext();
        $this->action   = Tools::getValue('action');
        $mobileDetect = new MobileDetect();
        $this->isMobile = $mobileDetect->isMobile();
        $this->cmsId = Tools::getValue('cmsId');
    }

    public function initContent()
    {
        try {
            parent::initContent();
            $cookie = new MPCookie();
            $this->deliveryDep = $cookie->readCookie('deliveryDep');
            if(empty($this->deliveryDep)){
                header('Location:/');
            }
            $this->{$this->action}();
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    public function touteslesrecettes() {
        $service = new Service();
        try {
            $listRecettes = $service->getLastTenRecettesByTypePlat();
            $listRecettesSearch = array();
            $search = $typePlat = '';
            if(Tools::getValue('search') || Tools::getValue('typePlat')) {
                $search = Tools::getValue('search');
                $typePlat = Tools::getValue('typePlat');
                $listRecettesSearch = $service->getRecettesBySearch(Tools::getValue('search'), Tools::getValue('typePlat'));
            }
        } catch(MPException $e) {
            $listRecettes = null;
        }
        $this->context->smarty->assign(
            array(
                'idCategoryChefJesus'   => CMS::CMS_CATEGORY_RECETTES_CHEF_JESUS,
                'noRightColumn'         => true,
                'meta_title'            => 'Toutes les recettes - mespaysans.com',
                'listRecettes'          => $listRecettes,
                'listRecettesSearch'    => $listRecettesSearch,
                'search'                => $search,
                'typePlat'              => $typePlat,
                'detailPage'            => 'Découvrez toutes nos recettes réalisable avec les produits de vos paysans !<br />Les idées de recettes proviennent de Chef Jésus comme de la communauté !',
            )
        );
        $this->setTemplate('touteslesrecettes.tpl');
    }
    
    public function recetteByCmsId() {
        $service = new Service();
        $cmsDto = $service->getCmsById($this->cmsId);
        
        $this->context->smarty->assign(
            array(
                'idCategoryChefJesus'   => CMS::CMS_CATEGORY_RECETTES_CHEF_JESUS,
                'noRightColumn'         => true,
                'meta_title'            => $cmsDto->getMetaTitle() . ' - mespaysans.com',
                'cmsDto'                => $cmsDto,
                'detailPage'            => 'Découvrez toutes nos recettes réalisable avec les produits de vos paysans !<br />Les idées de recettes proviennent de Chef Jésus comme de la communauté !',
            )
        );
        $this->setTemplate('recette.tpl');
    }
}
