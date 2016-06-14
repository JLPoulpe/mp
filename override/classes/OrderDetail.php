<?php
class OrderDetail extends OrderDetailCore {
    /**
     * @param int $idOrder
     * @return OrderDetailDto
     */
    public function getOrderDetailById($idOrder) {
        $sql = 'SELECT od.*, t.rate, cp.date_withdrawal,p.id_category_default, pl.description_short FROM ' . _DB_PREFIX_ . 'order_detail od '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_order=od.id_order '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product=od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=od.product_id AND pl.id_lang=' . (int)Context::getContext()->language->id . ' '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cart_product cp ON cp.id_cart=o.id_cart AND cp.id_product=od.product_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax_rule tr ON tr.id_tax_rules_group=od.id_tax_rules_group AND tr.id_country=' . (int)Context::getContext()->country->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax=tr.id_tax '
                . 'WHERE od.id_order=' . (int) $idOrder . ' '
                . 'ORDER BY cp.date_withdrawal, p.id_supplier';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    public function getDetailsCommandsForBo($status) {
        $sql = 'SELECT o.id_order, s.name as supplierName, s.id_supplier, pl.name as product_name, IF(od.product_attribute_id=0, od.product_quantity, al.name) as product_quantity , cp.date_withdrawal, od.original_wholesale_price as productPrice, p.unity, od.total_price_tax_incl, t.rate, p.mp_com FROM ' . _DB_PREFIX_ . 'orders o '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order=o.id_order '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product=od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=od.product_id AND pl.id_lang=' . (int)Context::getContext()->language->id . ' '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cart_product cp ON cp.id_cart=o.id_cart AND cp.id_product=od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=cp.id_supplier '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute AND al.id_lang=' . (int)Context::getContext()->language->id . '  '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax_rule tr ON tr.id_tax_rules_group=od.id_tax_rules_group AND tr.id_country=' . (int)Context::getContext()->country->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax=tr.id_tax '
                . 'WHERE o.current_state=' . (int) $status . ' '
                . 'ORDER BY o.date_add DESC, cp.id_supplier';

        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    public function getDetailsCommandsForBoByIdSupplier($status, $idSupplier) {
        $sql = 'SELECT o.id_order, pl.name as product_name, IF(od.product_attribute_id=0, od.product_quantity, al.name) as product_quantity , cp.date_withdrawal, od.original_wholesale_price as productPrice, p.unity, od.total_price_tax_incl, t.rate, p.mp_com, f.date_payment FROM ' . _DB_PREFIX_ . 'orders o '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'order_detail od ON od.id_order=o.id_order '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product=od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=od.product_id AND pl.id_lang=' . (int)Context::getContext()->language->id . ' '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cart_product cp ON cp.id_cart=o.id_cart AND cp.id_product=od.product_id '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'facture f ON f.id_order=od.id_order AND f.id_supplier=' . (int) $idSupplier . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON pa.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=od.product_attribute_id '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute AND al.id_lang=' . (int)Context::getContext()->language->id . '  '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax_rule tr ON tr.id_tax_rules_group=od.id_tax_rules_group AND tr.id_country=' . (int)Context::getContext()->country->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax=tr.id_tax '
                . 'WHERE o.current_state=' . (int) $status . ' AND cp.id_supplier=' . (int) $idSupplier . ' '
                . 'ORDER BY o.date_add DESC, cp.id_supplier';

        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    private function prepareDto($result, $multipleArray = false)
    {
        if($multipleArray) {
            $listObj = null;
            foreach($result as $row) {
                $obj = $this->createDto($row);
                $listObj[] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDto($result);
        }
    }
    
    private function createDto($result)
    {
        $orderDetail = null;
        if($result) {
            $orderDetail = new OrderDetailDto();
            $orderDetail->setIdOrder(isset($result['id_order']) ? $result['id_order'] : 0);
            $orderDetail->setIdOrderDetail(isset($result['id_order_detail']) ? $result['id_order_detail'] : 0);
            $orderDetail->setIdOrderInvoice(isset($result['id_order_invoice']) ? $result['id_order_invoice'] : 0);
            $orderDetail->setProductAttributeId(isset($result['product_attribute_id']) ? $result['product_attribute_id'] : 0);
            $orderDetail->setProductId(isset($result['product_id']) ? $result['product_id'] : 0);
            $orderDetail->setIdCategoryDefault(isset($result['id_category_default']) ? $result['id_category_default'] : 0);
            $orderDetail->setProductName(isset($result['product_name']) ? $result['product_name'] : '');
            $orderDetail->setTotalPriceTaxIncl(isset($result['total_price_tax_incl']) ? $result['total_price_tax_incl'] : 0);
            $orderDetail->setTotalPriceTaxExcl(isset($result['total_price_tax_excl']) ? $result['total_price_tax_excl'] : 0);
            $orderDetail->setUnitPriceTaxIncl(isset($result['unit_price_tax_incl']) ? $result['unit_price_tax_incl'] : 0);
            $orderDetail->setUnitPriceTaxExcl(isset($result['unit_price_tax_excl']) ? $result['unit_price_tax_excl'] : 0);
            $orderDetail->setProductDescription(isset($result['description_short']) ? $result['description_short'] : "");
            $orderDetail->setUnity(isset($result['unity']) ? $result['unity'] : "");
            $orderDetail->setMpCom(isset($result['mp_com']) ? $result['mp_com'] : "");
            $orderDetail->setProductPrice(isset($result['productPrice']) ? $result['productPrice'] : "");
            $orderDetail->setQuantity(isset($result['product_quantity']) ? $result['product_quantity'] : 0);
            $orderDetail->setTax(isset($result['rate']) ? $result['rate'] : 0);
            $orderDetail->setIdCart(isset($result['id_cart']) ? $result['id_cart'] : 0);
            $orderDetail->setDateWithdrawal(isset($result['date_withdrawal']) ? $result['date_withdrawal'] : 0);
            $orderDetail->setSupplierName(isset($result['supplierName']) ? $result['supplierName'] : "");
            $orderDetail->setIdSupplier(isset($result['id_supplier']) ? $result['id_supplier'] : 0);
            $orderDetail->setDatePayment(isset($result['date_payment']) ? $result['date_payment'] : "");
        }
        
        return $orderDetail;
    }
}
