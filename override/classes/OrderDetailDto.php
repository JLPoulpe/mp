<?php
class OrderDetailDto { 
    private $idOrderDetail;
    private $idOrder;
    private $idOrderInvoice;
    private $productId;
    private $productAttributeId;
    private $productName;
    private $totalPriceTaxIncl;
    private $totalPriceTaxExcl;
    private $unitPriceTaxIncl;
    private $unitPriceTaxExcl;
    private $listProduct;
    private $Quantity;
    private $tax;
    private $idCart;
    private $dateWithdrawal;
    private $idCategoryDefault;
    private $productDescription;
    private $supplierName;
    private $productPrice;
    private $unity;
    private $mpCom;
    private $idSupplier;
    private $datePayment;
    
    function getDatePayment() {
        $dateTime = new DateTime($this->datePayment);
        return $dateTime->format("d/m/Y");
    }

    function setDatePayment($datePayment) {
        $this->datePayment = $datePayment;
    }

    function getIdSupplier() {
        return $this->idSupplier;
    }

    function setIdSupplier($idSupplier) {
        $this->idSupplier = $idSupplier;
    }

    function getUnity() {
        return $this->unity;
    }

    function getMpCom() {
        return $this->mpCom;
    }

    function setUnity($unity) {
        $this->unity = $unity;
    }

    function setMpCom($mpCom) {
        $this->mpCom = $mpCom;
    }

    function getProductPrice() {
        if(!empty($this->mpCom)) {
            $productPriceWithTax = $this->productPrice + (($this->productPrice*$this->mpCom) / 100);
        } else {
            $productPriceWithTax = $this->productPrice;
        }
        return $productPriceWithTax;
    }
    
    function getPriceForSupplier() {
        $montantTTC = number_format($this->totalPriceTaxIncl, 2);
        return $montantTTC*0.87;
        /*if(empty($this->tax)) {
            $tax = 1.055;
        } else {
            $tax = 1+(number_format($this->tax, 2)/100);
        }
        $montantHT = $montantTTC/$tax;
        $montantHTMP = $montantHT/1.13;
        $caTTCMP = $montantHT - $montantHTMP;
        return $montantTTC - $caTTCMP;*/
    }
    
    function setProductPrice($productPrice) {
        $this->productPrice = $productPrice;
    }

    function getSupplierName() {
        return $this->supplierName;
    }

    function setSupplierName($supplierName) {
        $this->supplierName = $supplierName;
    }

    function getProductDescription() {
        return $this->productDescription;
    }

    function setProductDescription($productDescription) {
        $this->productDescription = $productDescription;
    }

    function getIdCategoryDefault() {
        return $this->idCategoryDefault;
    }

    function setIdCategoryDefault($idCategoryDefault) {
        $this->idCategoryDefault = $idCategoryDefault;
    }

    function getDateWithdrawal($useString = true) {
        if($useString) {
            $dateTime = new DateTime($this->dateWithdrawal);
            return  MPTools::$listJour[$dateTime->format('w')] . $dateTime->format(' d') . ' ' . MPTools::$listMois[$dateTime->format('n')-1] . $dateTime->format(' Y');
        } else {
            return $this->dateWithdrawal;
        }
    }

    function setDateWithdrawal($dateWithdrawal) {
        $this->dateWithdrawal = $dateWithdrawal;
    }
    
    function getIdCart() {
        return $this->idCart;
    }

    function setIdCart($idCart) {
        $this->idCart = $idCart;
    }
    
    function getTax() {
        return $this->tax;
    }

    function setTax($tax) {
        $this->tax = $tax;
    }

    function getQuantity() {
        return $this->Quantity;
    }

    function setQuantity($Quantity) {
        $this->Quantity = $Quantity;
    }

    function getUnitPriceTaxIncl() {
        return $this->unitPriceTaxIncl;
    }

    function setUnitPriceTaxIncl($unitPriceTaxIncl) {
        $this->unitPriceTaxIncl = $unitPriceTaxIncl;
    }

    function getUnitPriceTaxExcl() {
        return $this->unitPriceTaxExcl;
    }

    function setUnitPriceTaxExcl($unitPriceTaxExcl) {
        $this->unitPriceTaxExcl = $unitPriceTaxExcl;
    }

    function getListProduct() {
        return $this->listProduct;
    }

    function setListProduct($listProduct) {
        $this->listProduct = $listProduct;
    }
    
    function addProductToList($product) {
        $this->listProduct[] = $product;
    }
    
    function getIdOrderDetail() {
        return $this->idOrderDetail;
    }

    function getIdOrder() {
        return $this->idOrder;
    }

    function getIdOrderInvoice() {
        return $this->idOrderInvoice;
    }

    function getProductId() {
        return $this->productId;
    }

    function getProductAttributeId() {
        return $this->productAttributeId;
    }

    function getProductName() {
        return $this->productName;
    }

    function setIdOrderDetail($idOrderDetail) {
        $this->idOrderDetail = $idOrderDetail;
    }

    function setIdOrder($idOrder) {
        $this->idOrder = $idOrder;
    }

    function setIdOrderInvoice($idOrderInvoice) {
        $this->idOrderInvoice = $idOrderInvoice;
    }

    function setProductId($productId) {
        $this->productId = $productId;
    }

    function setProductAttributeId($productAttributeId) {
        $this->productAttributeId = $productAttributeId;
    }

    function setProductName($productName) {
        $this->productName = $productName;
    }

    function getTotalPriceTaxIncl() {
        return $this->totalPriceTaxIncl;
    }

    function setTotalPriceTaxIncl($totalPriceTaxIncl) {
        $this->totalPriceTaxIncl = $totalPriceTaxIncl;
    }

    function getTotalPriceTaxExcl() {
        return $this->totalPriceTaxExcl;
    }

    function setTotalPriceTaxExcl($totalPriceTaxExcl) {
        $this->totalPriceTaxExcl = $totalPriceTaxExcl;
    }
}