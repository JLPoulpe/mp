<?php
class mppaysanshomeModuleFrontController extends ModuleFrontController
{
    private $action;

    public function __construct()
    {
        parent::__construct();
        $this->context  = Context::getContext();
        $this->action   = Tools::getValue('action');
    }

    public function initContent()
    {
        try {
            parent::initContent();
            $this->{$this->action}();
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    public function chefjesus() {
        $service = new Service();
        $content = $service->getCMSByContent(CMS::CMS_ARTICLE_CHEF_JESUS, (int) Context::getContext()->language->id, (int) Context::getContext()->shop->id);
        $listRecettes = $service->getCMSArticlesByIdCMSCategory(CMS::CMS_CATEGORY_RECETTES_CHEF_JESUS);
        $breadcrumb = new Breadcrumb($this->action);
        $this->context->smarty->assign(
            array(
                'breadcrumb'            => $breadcrumb->getMenu('Chef Jésus'),
                'noRightColumn'         => true,
                'content'               => $content['content'],
                'listRecettes'          => $listRecettes,
                'meta_title'            => 'Chef Jésus - mespaysans.com',
            )
        );
        $this->setTemplate('chefjesus.tpl');
    }
    
    public function livraison() {
        $cms = new CMS();
        $content = $cms->getCMSContent(CMS::CMS_ARTICLE_LIVRAISON, (int) Context::getContext()->language->id, (int) Context::getContext()->shop->id);
        $breadcrumb = new Breadcrumb($this->action);
        $this->context->smarty->assign(
            array(
                'breadcrumb'    => $breadcrumb->getMenu('En savoir plus sur la livraison'),
                'noRightColumn' => true,
                'content'       => $content['content'],
                'meta_title'    => 'Livraison - mespaysans.com',
            )
        );
        $this->setTemplate('livraison.tpl');
    }
    
    public function partenaire() {
        $cms = new CMS();
        $content = $cms->getCMSContent(CMS::CMS_ARTICLE_PARTENAIRE, (int) Context::getContext()->language->id, (int) Context::getContext()->shop->id);
        $breadcrumb = new Breadcrumb($this->action);
        $this->context->smarty->assign(
            array(
                'breadcrumb'    => $breadcrumb->getMenu('En savoir plus sur nos partenaires'),
                'noRightColumn' => true,
                'content'       => $content['content'],
                'meta_title'    => 'Nos partenaires - mespaysans.com',
            )
        );
        $this->setTemplate('partenaire.tpl');
    }
    
    public function vinsbio() {
        $cms = new CMS();
        $content = $cms->getCMSContent(CMS::CMS_ARTICLE_VINSBIO, (int) Context::getContext()->language->id, (int) Context::getContext()->shop->id);
        $breadcrumb = new Breadcrumb($this->action);
        $service = new Service();
        $day = $service->getNextDateForSupplier(Supplier::FULL_NATURE);
        
        $this->context->smarty->assign(
            array(
                'noRightColumn' => true,
                'breadcrumb'    => $breadcrumb->getMenu('En savoir plus sur nos vins BIO'),
                'content'       => $content['content'],
                'day'           => $day,
                'idCategory'    => Category::CATEGORY_VINS_BIO,
                'linkRewrite'   => 'vins',
                'meta_title'    => 'Nos vins BIO - mespaysans.com',
            )
        );
        $this->setTemplate('vinsbio.tpl');
    }
    
    public function listePaysans() {
        $service = new Service();
        $listPaysansByCategory = $service->getListSupplier();
        
        $this->context->smarty->assign(
            array(
                'noRightColumn'         => true,
                'hideTop'               => true,
                'meta_title'            => 'La liste des paysans - mespaysans.com',
                'listPaysansByCategory' => $listPaysansByCategory,
            )
        );
        $this->setTemplate('listepaysans.tpl');
    }
}
