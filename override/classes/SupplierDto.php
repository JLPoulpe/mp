<?php
class SupplierDto {
    private $id_supplier;
    private $name;
    private $date_add;
    private $date_upd;
    private $active;
    private $id_lang;
    private $description;
    private $meta_title;
    private $meta_keywords;
    private $meta_description;
    private $listIdMarket;
    private $cacheMarket;
    private $listIdProduct;
    private $listProducts;
    private $categorie;
    private $lastDispo;
    private $isDispo;
    private $withdrawal;
    
    //customer
    private $idCustomer;
    private $firstname;
    private $lastname;
    private $email;
    
    function getIsBio() {
        return $this->withdrawal;
    }

    function setIsBio($isBio) {
        $this->withdrawal = $isBio;
    }
    
    function getIdCustomer() {
        return $this->idCustomer;
    }

    function getFirstname() {
        return $this->firstname;
    }

    function getLastname() {
        return $this->lastname;
    }

    function getEmail() {
        return $this->email;
    }

    function setIdCustomer($idCustomer) {
        $this->idCustomer = $idCustomer;
    }

    function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    function setEmail($email) {
        $this->email = $email;
    }
    
    function getIdSupplier() {
        return $this->id_supplier;
    }

    function getName() {
        return str_replace('"', '', $this->name);
    }

    function getDateAdd() {
        return $this->date_add;
    }

    function getDateUpd() {
        return $this->date_upd;
    }

    function getActive() {
        return $this->active;
    }

    function getIdLang() {
        return $this->id_lang;
    }

    function getDescription() {
        return $this->description;
    }

    function getMetaTitle() {
        return $this->meta_title;
    }

    function getMetaKeywords() {
        return $this->meta_keywords;
    }

    function getMetaDescription() {
        return $this->meta_description;
    }

    function getListIdMarket() {
        return $this->listIdMarket;
    }

    function getListIdProduct() {
        return $this->listIdProduct;
    }

    function getListProducts() {
        return $this->listProducts;
    }

    function getLinkRewrite() {
        return MPTools::rewrite($this->getName());
    }
    
    function getLastDispo() {
        return $this->lastDispo;
    }
    
    function getListNextMarket() {
        $market = new Market();
        if(count($this->listIdMarket)>0) {
            $tmp = $this->listIdMarket;
            $listAvailableMarket = array();
            foreach($tmp as $marketId) {
                $marketDto = $market->getMarketFromId($marketId, false, false, false);
                $dateMarket = new DateTime($marketDto->getNextDateOpenWithFormat('Ymd'));
                $date = new DateTime();
                $date->setTime(0, 0, 0);
                $interval = $date->diff($dateMarket);
                $nbJour = $interval->format('%a');
                if($nbJour>$this->lastDispo) {
                    $listAvailableMarket[$marketDto->getNextDateOpenWithFormat('Ymd')] = $marketDto;
                }
            }
            ksort($listAvailableMarket);
            return $listAvailableMarket;
        }
    }

    function setIsDispo($isDispo) {
        $this->isDispo = $isDispo;
    }
    
    function isDispo() {
        return $this->isDispo;
    }
    
    function setIdSupplier($id_supplier) {
        $this->id_supplier = $id_supplier;
    }
    
    function setName($name) {
        $this->name = $name;
    }

    function setDateAdd($date_add) {
        $this->date_add = $date_add;
    }

    function setDateUpd($date_upd) {
        $this->date_upd = $date_upd;
    }

    function setActive($active) {
        $this->active = $active;
    }

    function setIdLang($id_lang) {
        $this->id_lang = $id_lang;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setMetaTitle($meta_title) {
        $this->meta_title = $meta_title;
    }

    function setMetaKeywords($meta_keywords) {
        $this->meta_keywords = $meta_keywords;
    }

    function setMetaDescription($meta_description) {
        $this->meta_description = $meta_description;
    }

    function addIdMarket($idMarket) {
        $this->listIdMarket[] = $idMarket;
    }
    
    function setListIdMarket() {
        if(empty($this->listIdMarket)) {
            $supplier = new Supplier();
            $listMarkets = $supplier->getMarkets($this->id_supplier);
            foreach($listMarkets as $market) {
                $this->listIdMarket[] = $market['id_market'];
            }
        }
    }
    
    function addIdProduct($idProduct) {
        $this->listIdProduct[] = $idProduct;
    }
    
    function addProduct(ProductDto $productDto) {
        $this->listProducts[] = $productDto;
    }
    
    function getCategorie() {
        return $this->categorie;
    }

    function setCategorie($categorie) {
        $this->categorie = $categorie;
    }
    
    function setLastDispo($lastDispo) {
        $this->lastDispo = $lastDispo;
    }
}