<?php
class OrderDto { 
    public $reference;
    public $quantity;
    public $idMarket;
    public $dateWithdrawal;
    public $productPrice;
    public $attributePrice;
    public $productName;
    public $city;
    public $postalCode;
    public $marketName;
    public $attributeName;
    public $idProduct;
    public $idOrder;
    public $idCart;
    public $idCustomer;
    public $idCarrier;
    
    function getIdCarrier() {
        return $this->idCarrier;
    }

    function setIdCarrier($idCarrier) {
        $this->idCarrier = $idCarrier;
    }

    function getIdCustomer() {
        return $this->idCustomer;
    }

    function setIdCustomer($idCustomer) {
        $this->idCustomer = $idCustomer;
    }

    function getIdCart() {
        return $this->idCart;
    }

    function setIdCart($idCart) {
        $this->idCart = $idCart;
    }

    function getIdOrder() {
        return $this->idOrder;
    }

    function setIdOrder($idOrder) {
        $this->idOrder = $idOrder;
    }
    
    function getIdProduct() {
        return $this->idProduct;
    }

    function setIdProduct($idProduct) {
        $this->idProduct = $idProduct;
    }

    function getReference($proView=0) {
        if($proView) {
            return substr($this->reference, 0, 4);
        }
        return $this->reference;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getIdMarket() {
        return $this->idMarket;
    }

    function getDateWithdrawal() {
        $date = new DateTime($this->dateWithdrawal);
        return $date->format('d/m/Y');
    }

    function getProductPrice() {
        return $this->productPrice+$this->attributePrice;
    }

    function getAttributePrice() {
        return $this->attributePrice;
    }

    function getProductName() {
        return $this->productName;
    }

    function getCity() {
        return $this->city;
    }

    function getPostalCode() {
        return $this->postalCode;
    }

    function getMarketName() {
        return $this->marketName;
    }

    function setReference($reference) {
        $this->reference = $reference;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    function setIdMarket($idMarket) {
        $this->idMarket = $idMarket;
    }

    function setDateWithdrawal($dateWithdrawal) {
        $this->dateWithdrawal = $dateWithdrawal;
    }

    function setProductPrice($productPrice) {
        $this->productPrice = $productPrice;
    }

    function setAttributePrice($attributePrice) {
        $this->attributePrice = $attributePrice;
    }

    function setProductName($productName) {
        $this->productName = $productName;
    }

    function setCity($city) {
        $this->city = $city;
    }

    function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }

    function setMarketName($marketName) {
        $this->marketName = $marketName;
    }

    function setAttributeName($attributeName) {
        $this->attributeName = $attributeName;
    }
}