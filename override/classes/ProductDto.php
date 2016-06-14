<?php
class ProductDto
{
    private $id_market;
    private $id_product;
    private $price;
    private $unity;
    private $unit_price_ratio;
    private $productName;
    private $description_short;
    private $description;
    private $id_supplier;
    private $supplierName;
    private $poids;
    private $commandeAvantJ;
    private $poidsAuChoix;
    private $link_rewrite;
    private $id_image;
    private $catLinkRewrite;
    private $attributes = array();
    private $maxCaractere = 53;
    private $categoryId;
    private $ownerProduct;
    private $reference;
    private $id_tax_rules_group;
    private $taxRate;
    private $specificPrice;
    private $reduction;
    private $onSale;
    private $produitAilleurs;
    private $tax;
    private $delayBeforeMarket;
    
    function getDelayBeforeMarket() {
        return $this->delayBeforeMarket;
    }

    function setDelayBeforeMarket($delayBeforeMarket) {
        $this->delayBeforeMarket = $delayBeforeMarket;
    }

    function getTax() {
        return $this->tax;
    }

    function setTax($tax) {
        $this->tax = $tax;
    }

    function getProduitAilleurs() {
        return $this->produitAilleurs;
    }

    function setProduitAilleurs($produitAilleurs) {
        $this->produitAilleurs = $produitAilleurs;
    }

    function getSpecificPrice() {
        return $this->specificPrice;
    }

    function getReduction() {
        return $this->reduction;
    }

    function getOnSale() {
        return $this->onSale;
    }

    function setSpecificPrice($specificPrice) {
        $this->specificPrice = $specificPrice;
    }

    function setReduction($reduction) {
        $this->reduction = $reduction;
    }

    function setOnSale($onSale) {
        $this->onSale = $onSale;
    }
    
    function getTaxRate() {
        return $this->taxRate;
    }

    function setTaxRate($taxRate) {
        $this->taxRate = $taxRate;
    }
    
    function getIdTaxRulesGroup() {
        return $this->id_tax_rules_group;
    }

    function setIdTaxRulesGroup($id_tax_rules_group) {
        $this->id_tax_rules_group = $id_tax_rules_group;
    }

    function getReference() {
        return strtolower($this->reference);
    }

    function setReference($reference) {
        $this->reference = $reference;
    }
    
    function getOwnerProduct() {
        return $this->ownerProduct;
    }

    function setOwnerProduct($ownerProduct) {
        $this->ownerProduct = $ownerProduct;
    }

    function getCategoryId() {
        return $this->categoryId;
    }

    function setCategoryId($categoryId) {
        $this->categoryId = $categoryId;
    }
    
    function getIdMarket() {
        return $this->id_market;
    }

    function getIdProduct() {
        return $this->id_product;
    }
    
    function getTTCPrice() {
        return $this->price*(1+($this->taxRate/100));
    }
    function getPrice() {
        return $this->price;
    }

    function getUnity() {
        return $this->unity;
    }

    function getUnitPriceRatio() {
        return $this->unit_price_ratio;
    }

    function getUnitPrice() {
        if($this->unit_price_ratio>0) {
            return number_format($this->price/$this->unit_price_ratio, 2, ',', ' ');
        }
        return null;
    }

    function setListAttribute(array $attribute) {
        $this->attributes[$attribute['id_product_attribute']] = $attribute;
    }
    
    function getListAttribute() {
        return $this->attributes;
    }
    
    function getProductName() {
        return $this->productName;
    }

    function getDescriptionShort() {
        return $this->description_short;
    }

    function getDescription() {
        return $this->description;
    }

    function getIdSupplier() {
        return $this->id_supplier;
    }

    function getSupplierName($isName = true) {
        if($isName) {
            return $this->supplierName;
        } else {
            return MPTools::rewrite($this->supplierName);
        }
    }

    function getPoids() {
        return $this->poids;
    }

    function getCommandeAvantJ() {
        return $this->commandeAvantJ;
    }

    function getPoidsAuChoix() {
        return $this->poidsAuChoix;
    }

    function getIdImage() {
        return $this->id_image;
    }
    
    function getListImage($type='home_default') {
        $image = new Image();
        $listIdImage = $image->getListIdImageForIdProduct($this->id_product);
        $listImage = array();
        foreach($listIdImage as $idImage) {
            $image = new Image($idImage['id_image']);
            $path = $image->getImgPath();
            if($path) {
                $imgPath = '/img/p/' . $image->getImgPath() . '-' . $type . '.jpg';
                unset($image);
                $listImage[] = $imgPath;
            }
        }
        
        return $listImage;
    }
    
    function getImgPath($type='home_default') {
        $image = new Image($this->id_image);
        $path = $image->getImgPath();
        if($path) {
            $imgPath = '/img/p/' . $image->getImgPath() . '-' . $type . '.jpg';
            unset($image);
            return $imgPath;
        }
        
        return null;
    }
    
    function getCatLinkRewrite() {
        return $this->catLinkRewrite;
    }

    function setIdMarket($id_market) {
        $this->id_market = $id_market;
    }

    function setIdProduct($id_product) {
        $this->id_product = $id_product;
    }

    function setPrice($price) {
        $this->price = $price;
    }

    function setUnity($unity) {
        $this->unity = $unity;
    }

    function setUnitPriceRatio($unit_price_ratio) {
        $this->unit_price_ratio = $unit_price_ratio;
    }

    function setProductName($productName) {
        $this->productName = $productName;
    }

    function setDescriptionShort($description_short) {
        $this->description_short = $description_short;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setIdSupplier($id_supplier) {
        $this->id_supplier = $id_supplier;
    }

    function setSupplierName($supplierName) {
        $this->supplierName = str_replace('"', '', $supplierName);
    }
    
    function setPoids($poids) {
        $this->poids = $poids;
    }

    function setCommandeAvantJ($commandeAvantJ) {
        $this->commandeAvantJ = $commandeAvantJ;
    }

    function setPoidsAuChoix($poidsAuChoix) {
        $this->poidsAuChoix = $poidsAuChoix;
    }

    function getLinkRewrite() {
        return $this->link_rewrite;
    }

    function setLinkRewrite($link_rewrite) {
        $this->link_rewrite = $link_rewrite;
    }

    function setIdImage($id_image) {
        $this->id_image = $id_image;
    }
    
    function setCatLinkRewrite($catLinkRewrite) {
        $this->catLinkRewrite = $catLinkRewrite;
    }
}