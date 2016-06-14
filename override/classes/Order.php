<?php
class Order extends OrderCore {
    const EN_ATTENTE_CHEQUE = 1;
    const PAIEMENT_ACCEPTE = 2;
    const PREPARATION_EN_COURS = 3;
    const EN_COURS_DE_LIVRAISON = 4;
    const LIVRE = 5;
    const ANNULE = 6;
    const REMBOURSE = 7;
    const ERREUR_PAIEMENT = 8;
    const EN_ATTENTE_PAYPAL = 10;
    const PAIEMENT_A_DISTANCE_ACCEPTE = 12;
    const AUTORISATION_ACCEPTE_PAYPAL = 13;
    const MIN_PRICE_FOR_FREE_SHIPPING = 20;
    
    public function reInitProducts($idOrder) {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order =' . $idOrder . ';INSERT INTO mp_order_detail (id_order, id_order_invoice, id_warehouse, id_shop, product_id, product_attribute_id, product_name, product_quantity, product_quantity_in_stock, product_quantity_refunded, product_quantity_return, product_quantity_reinjected, product_price, reduction_percent, reduction_amount, reduction_amount_tax_incl, reduction_amount_tax_excl, group_reduction, product_quantity_discount, product_ean13, product_upc, product_reference, product_supplier_reference, product_weight, id_tax_rules_group, tax_computation_method, tax_name, tax_rate, ecotax, ecotax_tax_rate, discount_quantity_applied, download_hash, download_nb, download_deadline, total_price_tax_incl, total_price_tax_excl, unit_price_tax_incl, unit_price_tax_excl, total_shipping_price_tax_incl, total_shipping_price_tax_excl, purchase_supplier_price, original_product_price, original_wholesale_price)
                SELECT oi.id_order, oi.id_order_invoice, 0 as id_warehouse, 1 as id_shop, pl.id_product as product_id, cp.id_product_attribute as product_attribute_id, pl.name as product_name, cp.quantity as product_quantity, 0 as product_quantity_in_stock, 0 as product_quantity_refunded, 0 as product_quantity_return, 0 as product_quantity_reinjected, IF(pas.price,p.price+pas.price,p.price) as product_price,0 as reduction_percent,0 as reduction_amount,0 as reduction_amount_tax_incl,0 as reduction_amount_tax_excl,0 as group_reduction,0 as product_quantity_discount,\'\' as product_ean13,\'\' as product_upc,\'\' as product_reference,\'\' as product_supplier_reference,0 as product_weight,p.id_tax_rules_group as id_tax_rules_group,0 as tax_computation_method,\'\' as tax_name,0 as tax_rate,0 as ecotax,0 as ecotax_tax_rate,0 as discount_quantity_applied,\'\' as download_hash,0 as download_nb,\'0000-00-00 00:00:00\' as download_deadline,IF(pas.price,p.price+pas.price,p.price)*IF(t.rate,(1+t.rate/100),1)*cp.quantity as total_price_tax_incl,IF(pas.price,p.price+pas.price,p.price)*cp.quantity as total_price_tax_excl,IF(pas.price,p.price+pas.price,p.price)*IF(t.rate,(1+t.rate/100),1) as unit_price_tax_incl,IF(pas.price,p.price+pas.price,p.price) as unit_price_tax_excl,0 as total_shipping_price_tax_incl,0 as total_shipping_price_tax_excl,p.wholesale_price as purchase_supplier_price,IF(pas.price,p.price+pas.price,p.price) as original_product_price,p.wholesale_price as original_wholesale_price
                FROM `mp_orders` o
                INNER JOIN `mp_order_invoice` oi ON oi.id_order=o.id_order
                INNER JOIN `mp_cart_product` cp ON cp.id_cart=o.id_cart
                INNER JOIN `mp_product` p ON p.id_product = cp.id_product
                INNER JOIN `mp_product_lang` pl ON pl.id_product = cp.id_product AND pl.id_lang=2
                LEFT JOIN `mp_product_attribute_combination` pac ON pac.id_product_attribute = cp.id_product_attribute
                LEFT JOIN `mp_product_attribute_shop` pas ON pas.id_product_attribute = cp.id_product_attribute AND pas.id_product=cp.id_product
                LEFT JOIN `mp_attribute_lang` al ON al.id_attribute = pac.id_attribute AND al.id_lang=2
                LEFT JOIN `mp_tax_rule` tr ON tr.id_tax_rules_group = p.id_tax_rules_group AND tr.id_country=8
                LEFT JOIN `mp_tax` t ON t.id_tax = tr.id_tax
                WHERE o.`id_order`=' . (int) $idOrder;
        
        return Db::getInstance()->execute($sql);
    }
    
    public function getCommands($status, $idSupplier) {
        $sql = 'SELECT o.id_order, o.reference, cp.id_cart, cp.id_product, IF(cp.id_product_attribute=0, cp.quantity, al.name) as quantity , cp.id_market, cp.date_withdrawal, p.price as productPrice, pa.price as attributePrice, pl.name as productName, m.city, m.postal_code, ml.name as marketName FROM ' . _DB_PREFIX_ . 'orders o '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cart_product cp ON cp.id_cart=o.id_cart '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product=cp.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=cp.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market m ON m.id_market=cp.id_market '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_lang ml ON ml.id_market=cp.id_market '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute=cp.id_product_attribute '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=cp.id_product_attribute '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute '
                . 'WHERE o.current_state=' . (int) $status . ' AND cp.id_supplier=' . (int) $idSupplier .' AND cp.status=' . (int) Cart::PRODUIT_EN_ATTENTE_VALIDATION . ' '
                . 'ORDER BY reference';

        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    public function getListForFacture() {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'orders o WHERE o.current_state IN (' . (int) self::PAIEMENT_ACCEPTE . ',' . (int) self::PREPARATION_EN_COURS . ',' . (int) self::EN_COURS_DE_LIVRAISON . ',' . (int) self::LIVRE . ')';
        return Db::getInstance()->executes($sql);
    }
    
    /**
     * @param int $idOrder
     * @return OrderDto
     */
    public function getOrderById($idOrder) {
        $sql = 'SELECT o.* FROM ' . _DB_PREFIX_ . 'orders o WHERE id_order=' . (int) $idOrder;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result, false);
    }
    
    /**
     * @param int $idOrder
     * @return array
     */
    public function getProductsByIdOrder($idOrder) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'order_detail od WHERE id_order=' . (int) $idOrder;
        return Db::getInstance()->executeS($sql);
    }
    
    /**
     * @param int $idOrder
     * @param int $status
     */
    public function changeStatusOrder($idOrder, $status) {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'orders SET current_state=' . (int) $status . ' WHERE id_order=' . (int) $idOrder;
        Db::getInstance()->execute($sql);
    }

    /**
     * @param int $idCart
     * @param int $status
     */
    public function changeStatusByIdCart($idCart, $status) {
        $sql = 'UPDATE ' . _DB_PREFIX_ . 'orders SET current_state=' . (int) $status . ' WHERE id_cart=' . (int) $idCart;
        Db::getInstance()->execute($sql);
    }
    
    public function getNbOrderFromIdCustomer($idCustomer, $idOrder = 0) {
        $sql = 'SELECT COUNT(*) as nb FROM ' . _DB_PREFIX_ . 'orders WHERE id_customer=' . (int) $idCustomer . ' AND current_state IN (' . (int) self::PAIEMENT_ACCEPTE . ',' . (int) self::LIVRE . ',' . (int) self::PREPARATION_EN_COURS . ',' . self::EN_COURS_DE_LIVRAISON .')';
        if(!empty($idOrder)) {
            $sql .= ' AND id_order!=' . (int) $idOrder;
        }
        $result = Db::getInstance()->getRow($sql);
        return $result['nb'];
    }
    
    /**
     * @return OrderDto[]
     */
    public function getCustomersToDelivred() {
        $sql = 'SELECT o.id_customer, o.reference, o.id_cart FROM ' . _DB_PREFIX_ . 'orders o '
                . 'WHERE o.current_state=' . (int) Order::PAIEMENT_ACCEPTE  .' ORDER BY o.id_customer';

        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    private function prepareDto($result, $multipleArray = false)
    {
        if($multipleArray) {
            $listObj = null;
            foreach($result as $key=>$row) {
                $obj = $this->createDto($row);
                $listObj[$key] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDto($result);
        }
    }
    
    private function createDto($result)
    {
        $order = null;
        if($result) {
            $order = new OrderDto();
            $order->setIdCart(isset($result['id_cart']) ? $result['id_cart'] : 0);
            $order->setIdProduct(isset($result['id_product']) ? $result['id_product'] : 0);
            $order->setIdOrder(isset($result['id_order']) ? $result['id_order'] : 0);
            $order->setAttributePrice(isset($result['attributePrice']) ? $result['attributePrice'] : '');
            $order->setCity(isset($result['city']) ? $result['city'] : '');
            $order->setDateWithdrawal(isset($result['date_withdrawal']) ? $result['date_withdrawal'] : '');
            $order->setIdMarket(isset($result['id_market']) ? $result['id_market'] : 0);
            $order->setMarketName(isset($result['marketName']) ? $result['marketName'] : '');
            $order->setPostalCode(isset($result['postal_code']) ? $result['postal_code'] : 0);
            $order->setProductName(isset($result['productName']) ? $result['productName'] : '');
            $order->setProductPrice(isset($result['productPrice']) ? $result['productPrice'] : '');
            $order->setQuantity(isset($result['quantity']) ? $result['quantity'] : '');
            $order->setReference(isset($result['reference']) ? $result['reference'] : '');
            $order->setIdCustomer(isset($result['id_customer']) ? $result['id_customer'] : 0);
            $order->setIdCarrier(isset($result['id_carrier']) ? $result['id_carrier'] : 0);
        }
        
        return $order;
    }
}
