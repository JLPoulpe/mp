<?php
include_once(_PS_MODULE_DIR_.'mpfacture/models/Facture.php');
include_once(_PS_MODULE_DIR_.'mpfacture/models/FactureDto.php');
class mespaysansmespaysansModuleFrontController extends ModuleFrontController
{
    private $jour;
    private $categoryId;
    public function __construct() {
        parent::__construct();
        $this->context      = Context::getContext();
        $this->action       = Tools::getValue('action');
        $mobileDetect       = new MobileDetect();
        $this->isMobile     = $mobileDetect->isMobile();
        $this->jour         = Tools::getValue('jour');
        $this->categoryId   = Tools::getValue('categoryId');
    }

    public function initContent() {
        try {
            parent::initContent();
            $this->{$this->action}();
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    public function jefaismonmarche() {
        $service = new Service();
        try {
            $listCategoryByDate = $service->getCategoriesByDays();
            $listCategoryByStringDateTime = array();
            foreach($listCategoryByDate as $date=>$listCategory) {
                $oDate = new DateTime($date);
                $listCategoryByStringDateTime[MPTools::$listJour[$oDate->format('w')] . $oDate->format(' d') . ' ' . MPTools::$listMois[$oDate->format('n')-1]] = array('list' => $listCategory, 'jour' => strtolower(MPTools::$listJour[$oDate->format('w')]));
            }
            unset($listCategoryByDate);
        } catch(MPException $e) {
            $listCategoryByStringDateTime = null;
        }
        $breadcrumb = new Breadcrumb($this->action);
        $content = array(
                'breadcrumb'                    => $breadcrumb->getMenu('Je fais mon marché'),
                'meta_title'                    => 'Je fais mon marché - mespaysans.com',
                'listCategoryByStringDateTime'  => $listCategoryByStringDateTime,
                'detailPage'                    => 'Il y a des marchés différents chaque jour. Vous retrouvez donc des produits et des paysans différents chaque jour.<br />Commandez vos produits des marchés du jour, nous vous les livrons dans l’après midi ou en soirée.',
            );
        if ($this->isMobile) {
            $content['noRightColumn'] = true;
        }
        $this->context->smarty->assign(
            $content
        );
        $this->setTemplate('jefaismonmarche.tpl');
    }
    
    public function lesproduitsdujour() {
        $service = new Service();
        $listProductsBySupplier = array();
        try {
            $listCategoryByDate = $service->getCategoriesByDaysWithoutPosition();
            $dateTime = MPTools::getNextDateFromDay($this->jour);
            $listCategory = array();
            if(isset($listCategoryByDate[$dateTime->format('Ymd')])) {
                $listCategory = $listCategoryByDate[$dateTime->format('Ymd')];
            }
            $listProductsBySupplier = $service->getProductsBySupplierByDayAndCategoryId($this->jour, $this->categoryId, $dateTime->format('Ymd'));
        } catch(MPException $e) {
            $listCategory = null;
        }
        $breadcrumb = new Breadcrumb($this->action);
        $content  = array(
                'breadcrumb'                => $breadcrumb->getMenu('Je choisis mes produits du ' . $this->jour),
                'meta_title'                => 'Je choisis mes produits du ' . $this->jour . ' - mespaysans.com',
                'day'                       => $this->jour,
                'listCategory'              => $listCategory,
                'listProductsBySupplier'    => $listProductsBySupplier,
                'datewithdrawal'            => $dateTime->format('Y-m-d'),
                'detailPageProduct'         => '<span class="ClaireHandRegular" style="font-size: 25px;">Les produits de vos paysans du ' . $this->jour . '</span><br />Prochaine livraison le ' . $this->jour . ' ' . $dateTime->format('d') . ' ' . MPTools::$listMoisDiffere[$dateTime->format('n')],
                'isBio'                     => 0,
                'categoryId'                => $this->categoryId,
                'isMobile'                  => $this->isMobile,
            );
        $this->context->smarty->assign(
            $content
        );
        $this->setTemplate('lesproduitsdujour.tpl');
    }
    
    public function lesproduitsdujourbio() {
        $service = new Service();
        try {
            $listCategoryByDate = $service->getCategoriesByDaysWithoutPosition();
            $dateTime = MPTools::getNextDateFromDay($this->jour);
            $listCategory = $listCategoryByDate[$dateTime->format('Ymd')];
            $listProductsBySupplier = $service->getProductsBySupplierByDayAndCategoryId($this->jour, $this->categoryId, $dateTime->format('Ymd'));
        } catch(MPException $e) {
            $listCategory = null;
        }
        $breadcrumb = new Breadcrumb($this->action);
        $this->context->smarty->assign(
            array(
                'breadcrumb'                => $breadcrumb->getMenu('Je choisis mes produits du ' . $this->jour),
                'meta_title'                => 'Je choisis mes produits du ' . $this->jour . ' - mespaysans.com',
                'day'                       => $this->jour,
                'listCategory'              => $listCategory,
                'listProductsBySupplier'    => $listProductsBySupplier,
                'datewithdrawal'            => $dateTime->format('Y-m-d'),
                'detailPageProduct'         => 'Les produits de vos paysans du ' . $this->jour,
                'isBio'                     => 1,
                'categoryId'                => $this->categoryId,
                'isMobile'                  => $this->isMobile,
            )
        );
        $this->setTemplate('lesproduitsdujour.tpl');
    }
    
    public function espacePro() {
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=order-follow');
        }

        $idUser = $this->context->customer->id;
        $supplier = new Supplier();
        $supplierIds = $supplier->getSupplierIdsByIdUser($idUser);
        if (empty($supplierIds)) {
            header('Location:/mon-compte');
            exit;
        }
        
        $dateTime = new DateTime();
        $dateFrom = $dateTime->setDate($dateTime->format('Y'), $dateTime->format('m')-1, '01');
        $cart = new Cart();
        $listCommands = $cart->getCommandesByDate(Order::PAIEMENT_ACCEPTE, $supplierIds, $dateFrom->format('Y-m-d'));
        
        $listOrders = array();
        $facture = new Facture();
        foreach($listCommands as $cartDto) {
            $result = $facture->isAlreadyPaid($cartDto->getIdSupplier(), $cartDto->getIdOrder());
            if(!empty($result)) {
                $cartDto->setDatePayment($result['date_payment']);
            }
            $listOrders[] = $cartDto;
        }
        /*$listCartDto = $cart->getProductsFromPack($supplierIds, $dateFrom->format('Y-m-d'));
        foreach($listCartDto as $cartDto) {
            $listCommands[] = $cartDto;
        }*/
        $this->context->smarty->assign(
            array(
                'noRightColumn' => true,
                'listCommands'  => $listOrders,
                'listDates'     => MPTools::getListDates(),
                'meta_title'    => 'Espace Pro - mespaysans.com',
            )
        );
        $this->setTemplate('espacepro.tpl');
    }

    public function espaceProFacture() {
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=order-follow');
        }
        
        $idUser = $this->context->customer->id;
        $supplier = new Supplier();
        $supplierIds = $supplier->getSupplierIdsByIdUser($idUser);
        
        if (empty($supplierIds)) {
            header('Location:/mon-compte');
            exit;
        }
        
        $mois = Tools::getValue('mois');
        $dateString = $mois . '01';
        $dateFrom = new DateTime($dateString);
        $dateTo = clone($dateFrom);
        $dateTo->modify('+1 month');
        $cart = new Cart();
        $listCommands = $cart->getCommandesByDateInDateOut($supplierIds, $dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d'));
        
        $this->context->smarty->assign(
            array(
                'noRightColumn' => true,
                'listCommands'  => $listCommands,
                'meta_title'    => 'Espace Pro - mespaysans.com',
                'mois'          => MPTools::$listMoisDiffere[$dateFrom->format('n')],
                'annee'         => $dateFrom->format('Y'),
            )
        );
        $this->setTemplate('facture.tpl');
    }
    
    public function paniersjour() {
        $dateMarket = MPTools::getNextDateFromDay($this->jour);
        $dateTime = new DateTime();
        $dateMarket->setTime($dateTime->format('H'), $dateTime->format('i'), $dateTime->format('s'));
        $interval = $dateTime->diff($dateMarket, true);
        $diff = $interval->format('%a');
        $product = new Product();
        $listPanier = $product->getProductByCategoryAndDay(Category::CATEGORY_PANIER, $this->jour, $diff);
        $aPanier = array();
        $date = MPTools::getNextDateFromDay($this->jour);
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
            );
            $aPanier[] = $tmp;
        }
        $breadcrumb = new Breadcrumb($this->action);
        $this->context->smarty->assign(
               array(
                   'meta_title'         => 'Vos paniers du ' . $this->jour . ' - mespaysans.com',
                   'aPanier'            => $aPanier,
                   'detailPageProduct'  => '<span class="ClaireHandRegular" style="font-size: 25px;">Les paniers du ' . $this->jour . '</span><br />Prochaine livraison le ' . $this->jour . ' ' . $date->format('d') . ' ' . MPTools::$listMoisDiffere[$date->format('n')],
                   'breadcrumb'         => $breadcrumb->getMenu('Les panier du ' . $this->jour),
                   'rightColumn'        => true,
                   'jour'               => $this->jour,
                   'dateWithDrawal'     => $date->format('Y-m-d'),
               )
           );
        $this->setTemplate('paniersjour.tpl');
    }
}
