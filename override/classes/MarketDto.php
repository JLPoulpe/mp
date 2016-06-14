<?php
class MarketDto {
    private $id_market;
    private $address;
    private $postal_code;
    private $city;
    private $date_add;
    private $date_upd;
    private $active;
    private $open_at;
    private $close_at;
    private $lundi;
    private $mardi;
    private $mercredi;
    private $jeudi;
    private $samedi;
    private $vendredi;
    private $dimanche;
    private $latitude;
    private $longitude;
    private $name;
    private $meta_title;
    private $meta_keywords;
    private $meta_description;
    private $link_rewrite;
    private $id_shop;
    private $id_lang;
    private $description;
    private $listProduits = array();
    private $listCat = array();
    private $listSupplier = array();
    private $nbSupplier = 0;
    
    function getIdMarket() {
        return $this->id_market;
    }

    function getAddress() {
        return $this->address;
    }

    function getPostalCode() {
        return $this->postal_code;
    }

    function getCity() {
        return $this->city;
    }

    function getCityRewrite() {
        return MPTools::rewrite(strtolower($this->city));
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

    function getOpenAt() {
        return $this->open_at;
    }

    function getCloseAt() {
        return $this->close_at;
    }
    
    function showDays() {
        $listDay = array();
        if($this->getLundi()) {
            $listDay[] = 'Lundi';
        }
        if($this->getMardi()) {
            $listDay[] = 'Mardi';
        }
        if($this->getMercredi()) {
            $listDay[] = 'Mercredi';
        }
        if($this->getJeudi()) {
            $listDay[] = 'Jeudi';
        }
        if($this->getVendredi()) {
            $listDay[] = 'Vendredi';
        }
        if($this->getSamedi()) {
            $listDay[] = 'Samedi';
        }
        if($this->getDimanche()) {
            $listDay[] = 'Dimanche';
        }
        
        return implode(', ', $listDay);
    }
    
    function getLundi() {
        return $this->lundi;
    }

    function getMardi() {
        return $this->mardi;
    }

    function getMercredi() {
        return $this->mercredi;
    }

    function getJeudi() {
        return $this->jeudi;
    }

    function getSamedi() {
        return $this->samedi;
    }

    function getVendredi() {
        return $this->vendredi;
    }

    function getDimanche() {
        return $this->dimanche;
    }

    function getLatitude() {
        return $this->latitude;
    }

    function getLongitude() {
        return $this->longitude;
    }

    function getName() {
        return $this->name;
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

    function getLinkRewrite() {
        return MPTools::rewrite(strtolower($this->name));
    }

    function getShortName() {
        return $this->link_rewrite;
    }

    function getIdShop() {
        return $this->id_shop;
    }

    function getIdLang() {
        return $this->id_lang;
    }

    function getDescription() {
        return $this->description;
    }

    function getListProduits() {
        return $this->listProduits;
    }
    
    function getListCat() {
        return $this->listCat;
    }
    
    function getListSupplier() {
        return $this->listSupplier;
    }
    
    function getSupplier($idSupplier) {
        return $this->listSupplier[$idSupplier];
    }
    
    /**
     * @return DateTime
     */
    function getNextDateOpen($addWeek = false) {
        return MPTools::whatIsNextDateForThisMarket($this, $addWeek);
    }
    
    /**
     * @return String
     */
    function getNextDateOpenForLink() {
        $date = MPTools::whatIsNextDateForThisMarket($this);
        return strtolower(MPTools::$listJour[$date->format('w')]);
    }

    function getNextDateOpenWithFormat($format='stringDay', $useDelay = true) {
        $date = MPTools::whatIsNextDateForThisMarket($this, $useDelay);
        if($format=='jour') {
            return strtolower(MPTools::$listJour[$date->format('w')]);
        } elseif ($format=='stringDay') {
            return MPTools::$listJour[$date->format('w')] . $date->format(' d') . ' ' . MPTools::$listMois[$date->format('n')-1] . $date->format(' Y');
        } else {
            return $date->format($format);
        }
    }
    
    function getNbSupplier() {
        return $this->nbSupplier;
    }
    
    function setAddress($address) {
        $this->address = $address;
    }

    function setPostalCode($postal_code) {
        $this->postal_code = $postal_code;
    }

    function setCity($city) {
        $this->city = $city;
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

    function setOpenAt($open_at) {
        $this->open_at = MPTools::formatHeure($open_at);
    }

    function setCloseAt($close_at) {
        $this->close_at = MPTools::formatHeure($close_at);
    }

    function setLundi($lundi) {
        $this->lundi = $lundi;
    }

    function setMardi($mardi) {
        $this->mardi = $mardi;
    }

    function setMercredi($mercredi) {
        $this->mercredi = $mercredi;
    }

    function setJeudi($jeudi) {
        $this->jeudi = $jeudi;
    }

    function setSamedi($samedi) {
        $this->samedi = $samedi;
    }

    function setVendredi($vendredi) {
        $this->vendredi = $vendredi;
    }

    function setDimanche($dimanche) {
        $this->dimanche = $dimanche;
    }

    function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    function setName($name) {
        $this->name = $name;
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

    function setLinkRewrite($link_rewrite) {
        $this->link_rewrite = $link_rewrite;
    }

    function setIdShop($id_shop) {
        $this->id_shop = $id_shop;
    }

    function setIdMarket($id_market) {
        $this->id_market = $id_market;
    }

    function setIdLang($id_lang) {
        $this->id_lang = $id_lang;
    }

    function setDescription($description) {
        $this->description = $description;
    }
    
    function setListCat($listCat) {
        $this->listCat = $listCat;
    }
    
    function setListSupplier($listSupplier) {
        $this->listSupplier = $listSupplier;
    }
    
    function addSupplier($key, SupplierDto $supplierDto) {
        $this->listSupplier[$key] = $supplierDto;
        $this->addNbSupplier();
    }
    
    function addNbSupplier() {
        $this->nbSupplier++;
    }
    
    function addProduct(ProductDto $productDto) {
        $this->listProduits[] = $productDto;
    }
    
    function addCat(CategoryDto $categoryDto) {
        $this->listCat[] = $categoryDto;
    }
}