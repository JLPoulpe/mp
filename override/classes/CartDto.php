<?php
class CartDto {
    private $idOrder;
    private $orderReference;
    private $idCart;
    private $idProduct;
    private $idAddressDelivery;
    private $idShop;
    private $idProductAttribute;
    private $quantity;
    private $dateAdd;
    private $idSupplier;
    private $idMarket;
    private $dateWithdrawal;
    private $status;
    private $priceIncludeTax;
    private $tax;
    private $productName;
    private $productPrice;
    private $unity;
    private $mpCom;
    private $datePayment;
    
    function getDatePayment($format = 'd/m/Y', $strinDay = false) {
        if(!empty($this->datePayment)) {
            $date = new DateTime($this->datePayment);
            if($strinDay) {
                return MPTools::$listJour[$date->format('w')] . $date->format(' d') . ' ' . MPTools::$listMois[$date->format('n')-1] . $date->format(' Y');
            } else {
                if($format) {
                    return $date->format($format);
                }
            }
            return $this->datePayment;
        }
        
        return '';
    }

    function setDatePayment($datePayment) {
        $this->datePayment = $datePayment;
    }

    function getMpCom() {
        return $this->mpCom;
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

    function getUnity() {
        return $this->unity;
    }

    function setProductPrice($productPrice) {
        $this->productPrice = $productPrice;
    }

    function setUnity($unity) {
        $this->unity = $unity;
    }
    
    function getProductName() {
        return $this->productName;
    }

    function setProductName($productName) {
        $this->productName = $productName;
    }

    function getPriceForSupplier() {
        $montantTTC = number_format($this->priceIncludeTax, 2);
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
    
    function getPriceIncludeTax() {
        return $this->priceIncludeTax;
    }

    function getTax() {
        return $this->tax;
    }

    function setPriceIncludeTax($priceIncludeTax) {
        $this->priceIncludeTax = $priceIncludeTax;
    }

    function setTax($tax) {
        $this->tax = $tax;
    }
    
    function getOrderReference($proView=0) {
        if($proView) {
            return substr($this->orderReference, 0, 4);
        }
        return $this->orderReference;
    }

    function setOrderReference($orderReference) {
        $this->orderReference = $orderReference;
    }

    function getIdOrder() {
        return $this->idOrder;
    }

    function getIdCart() {
        return $this->idCart;
    }

    function getIdProduct() {
        return $this->idProduct;
    }
    
    function getProductDto() {
        $product = new Product();
        return $product->getById($this->idProduct);
    }
    
    function getIdAddressDelivery() {
        return $this->idAddressDelivery;
    }

    function getIdShop() {
        return $this->idShop;
    }

    function getIdProductAttribute() {
        return $this->idProductAttribute;
    }

    function getQuantity() {
        return $this->quantity;
    }

    function getDateAdd() {
        return $this->dateAdd;
    }

    function getIdSupplier() {
        return $this->idSupplier;
    }

    function getIdMarket() {
        return $this->idMarket;
    }
    
    function getMarketDto() {
        $market = new Market();
        return $market->getMarketFromId($this->idMarket, false, false, false);
    }
    
    function getDateWithdrawal($format = 'd/m/Y', $strinDay = false) {
        $date = new DateTime($this->dateWithdrawal);
        if($strinDay) {
            return MPTools::$listJour[$date->format('w')] . $date->format(' d') . ' ' . MPTools::$listMois[$date->format('n')-1] . $date->format(' Y');
        } else {
            if($format) {
                return $date->format($format);
            }
        }
        return $this->dateWithdrawal;
    }

    function getStatus() {
        return $this->status;
    }

    function setIdOrder($idOrder) {
        $this->idOrder = $idOrder;
    }

    function setIdCart($idCart) {
        $this->idCart = $idCart;
    }

    function setIdProduct($idProduct) {
        $this->idProduct = $idProduct;
    }

    function setIdAddressDelivery($idAddressDelivery) {
        $this->idAddressDelivery = $idAddressDelivery;
    }

    function setIdShop($idShop) {
        $this->idShop = $idShop;
    }

    function setIdProductAttribute($idProductAttribute) {
        $this->idProductAttribute = $idProductAttribute;
    }

    function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    function setDateAdd($dateAdd) {
        $this->dateAdd = $dateAdd;
    }

    function setIdSupplier($idSupplier) {
        $this->idSupplier = $idSupplier;
    }

    function setIdMarket($idMarket) {
        $this->idMarket = $idMarket;
    }

    function setDateWithdrawal($dateWithdrawal) {
        $this->dateWithdrawal = $dateWithdrawal;
    }

    function setStatus($status) {
        $this->status = $status;
    }


}
