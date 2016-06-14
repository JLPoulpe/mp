<?php
class Product extends ProductCore
{
    public $alcoolCategory = array(22,24,26);
    
    /** V2 **/
    /**
     * Retourne la liste des produits pour un idSupplier
     * @param int $idSupplier
     * @return productDto[]
     */
    public function getProductsByIdSupplier($idSupplier) {
        $sql = 'SELECT p.id_product, p.id_tax_rules_group, p.reference, p.price, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, p.id_category_default '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND p.id_supplier=' . (int) $idSupplier .' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'ORDER BY p.id_category_default, p.owner_product';
        $result = Db::getInstance()->executeS($sql);

        return $this->prepareDtoV2($result, true);
    }
    
    /**
     * Retourne la liste des produits pour un idSupplier et une categorie
     * @param int $idSupplier
     * @return productDto[]
     */
    public function getProductsByIdSupplierAndCategoryId($idSupplier, $categoryId, $delay = 0) {
        $sql = 'SELECT p.id_product, p.id_tax_rules_group, p.reference, p.price, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, p.id_category_default '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND p.id_supplier=' . (int) $idSupplier . ' '
                . 'AND p.id_category_default=' . (int) $categoryId . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND p.product_type=\'product\' '
                . 'AND IF(delay_before_market="", 0, delay_before_market)<=' . (int) $delay . ' '
                . 'ORDER BY p.id_category_default, p.owner_product DESC, productName';
        $result = Db::getInstance()->executeS($sql);

        return $this->prepareDtoV2($result, true);
    }
    
    /**
     * @param int $idProduct
     * @return ProductDto
     */
    public function getByIdV2($idProduct)
    {
        $sql = 'SELECT p.id_product, p.id_tax_rules_group, p.reference, p.price, p.unity, p.unit_price_ratio, p.owner_product, t.rate, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, p.id_category_default '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax_rule tr ON tr.id_tax_rules_group=p.id_tax_rules_group AND tr.id_country=' . (int)Context::getContext()->country->id . ' '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON t.id_tax=tr.id_tax '
                . 'WHERE p.active=1 '
                . 'AND p.id_product=' . (int) $idProduct .' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'ORDER BY p.id_category_default, p.owner_product';
        $row = Db::getInstance()->getRow($sql);
        return $this->prepareDtoV2($row);
    }
    
    /** V1 **/
    public function getCount() {
        $sql = 'SELECT COUNT(id_product) as count FROM '._DB_PREFIX_. 'product'
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'WHERE active=1 '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id;
        $result = Db::getInstance()->executeS($sql);
        
        return $result[0]['count'];
    }
    
    public function getTaxForProduct($idTaxRulesGroup) {
        $sql = 'SELECT DISTINCT t.rate FROM '._DB_PREFIX_. 'tax t '
                . 'INNER JOIN '._DB_PREFIX_. 'tax_rule tr ON t.id_tax=tr.id_tax '
                . 'INNER JOIN '._DB_PREFIX_. 'product p ON p.id_tax_rules_group=tr.id_tax_rules_group 
                    WHERE tr.id_tax_rules_group=' . (int) $idTaxRulesGroup;
        
        return Db::getInstance()->getRow($sql);
    }
    
    /**
     * @param int $idCategory
     * @return ProductDto[]
     */
    public function getProductByCategory($idCategory) {
        $sql = 'SELECT p.id_product, p.price, p.reference, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, ca.id_category catId, ca.link_rewrite catLinkRewrite '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND p.id_category_default=' . (int) $idCategory . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'ORDER BY p.owner_product';
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idCategory
     * @param int $idSupplier
     * @return ProductDto[]
     */
    public function getDistinctProductByCategoryAndSupplier($idCategory, $idSupplier) {
        $sql = 'SELECT p.id_product, p.price, p.reference, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, ca.id_category catId, ca.link_rewrite catLinkRewrite, sp.price as specificprice, sp.reduction, p.on_sale '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_supplier ps ON ps.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'specific_price sp ON sp.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND ps.id_supplier=' . (int) $idSupplier . ' '
                . 'AND p.id_category_default=' . (int) $idCategory . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'GROUP BY p.reference ORDER BY p.owner_product';
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idCategory
     * @param int $idSupplier
     * @return ProductDto[]
     */
    public function getProductByCategoryAndSupplier($idCategory, $idSupplier) {
        $sql = 'SELECT p.id_product, p.price, p.reference, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, ca.id_category catId, ca.link_rewrite catLinkRewrite, sp.price as specificprice, sp.reduction, p.on_sale '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_supplier ps ON ps.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'specific_price sp ON sp.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND ps.id_supplier=' . (int) $idSupplier . ' '
                . 'AND p.id_category_default=' . (int) $idCategory . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'ORDER BY p.owner_product';
        $result = Db::getInstance()->executeS($sql);

        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idCategory
     * @param int $idSupplier
     * @return ProductDto[]
     */
    public function getProduct2ByCategoryAndSupplier($idCategory, $idSupplier) {
        $sql = 'SELECT p.id_product, p.price, p.reference, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, ca.id_category catId, ca.link_rewrite catLinkRewrite, sp.price as specificprice, sp.reduction, p.on_sale '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_supplier ps ON ps.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'specific_price sp ON sp.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND ps.id_supplier=' . (int) $idSupplier . ' '
                . 'AND p.id_category_default=' . (int) $idCategory . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'ORDER BY p.owner_product';
        $result = Db::getInstance()->executeS($sql);

        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idCategory
     * @param int $idSupplier
     * @return productDto[]
     */
    public function getDistinctProductByCategoryAndIdSupplier($idCategory, $idSupplier) {
        $sql = 'SELECT p.id_product, p.price, p.reference, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, ca.id_category catId, ca.link_rewrite catLinkRewrite, sp.price as specificprice, sp.reduction, p.on_sale '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'specific_price sp ON sp.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND p.id_supplier=' . (int) $idSupplier . ' '
                . 'AND p.id_category_default=' . (int) $idCategory . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'GROUP BY p.reference ORDER BY p.owner_product';
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idCategory
     * @return ProductDto[]
     */
    public function getProductByCategoryAndDay($idCategory, $day, $delay = 0) {
        $sql = 'SELECT p.id_product, p.price, p.reference, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, ca.id_category catId, ca.link_rewrite catLinkRewrite, sp.price as specificprice, sp.reduction, p.on_sale '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'specific_price sp ON sp.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'WHERE p.active=1 '
                . 'AND p.id_category_default=' . (int) $idCategory . ' '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND IF(delay_before_market="", 0, delay_before_market)<=' . (int) $delay . ' '
                . 'AND p.reference=\'' . $day . '\' '
                . 'ORDER BY p.price';
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDtoV2($result, true);
    }
    
    public function getProductsOnSale() {
        $sql = 'SELECT p.id_product, p.price, p.reference, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, i.id_image, ca.id_category catId, ca.link_rewrite catLinkRewrite, sp.price as specificprice, sp.reduction, p.on_sale, ps.id_supplier, ms.id_market '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'specific_price sp ON sp.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_supplier ps ON ps.id_product=p.id_product '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=ps.id_supplier '
                . 'WHERE p.active=1 '
                . 'AND p.on_sale=1 '
                . 'AND pl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'GROUP BY p.id_product';
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $idProduct
     * @return ProductDto
     */
    public function getById($idProduct, $isProductActive = 1, $isSupplierActive = 1)
    {
        $sql = 'SELECT DISTINCT s.id_supplier, p.id_product, p.id_tax_rules_group, p.reference, p.price, p.unity, p.unit_price_ratio, p.owner_product, pl.name productName, pl.link_rewrite, pl.description_short, pl.description, s.name supplierName, i.id_image, ca.id_category catId, ca.link_rewrite catLinkRewrite, ms.id_market '
                . 'FROM ' . _DB_PREFIX_ .  'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang ca ON ca.id_category=p.id_category_default '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'image i ON i.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_supplier ps ON ps.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=ps.id_supplier '
                . 'LEFT JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=ps.id_supplier '
                . 'WHERE pl.id_lang=' . (int) Context::getContext()->language->id . ' ';
        if($isProductActive) {
            $sql .= 'AND p.active=' . $isProductActive . ' ';
        }
        if($isSupplierActive) {
            $sql .= 'AND s.active=' . $isSupplierActive . ' ';
        }
        $sql .= 'AND p.id_product=' . (int) $idProduct . ' ORDER BY catId';
        $row = Db::getInstance()->getRow($sql);

        $productDto = null;
        if(!empty($row)) {
            $productDto = $this->createDtoV2($row);
            $productDto = $this->getAttributeForProductDto($productDto);
        }
        
        return $productDto;
    }

    public function getAttributeForProductDto(ProductDto $productDto) {
        $sql = 'SELECT pa.id_product_attribute, pa.price, al.name FROM ' . _DB_PREFIX_ . 'product_attribute pa '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=pa.id_product_attribute '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute '
                . 'WHERE pa.id_product=' . (int) $productDto->getIdProduct() . ' '
                . 'AND al.id_lang=' . (int) Context::getContext()->language->id;
        $result = Db::getInstance()->executeS($sql);

        foreach($result as $attribute) {
            $productDto->setListAttribute($attribute);
        }
        
        return $productDto;
    }
    
    public function getAttributeForProductDtoV2(ProductDto $productDto) {
        $sql = 'SELECT pa.id_product_attribute, pa.price, al.name FROM ' . _DB_PREFIX_ . 'product_attribute pa '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_product_attribute=pa.id_product_attribute '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute=pac.id_attribute '
                . 'WHERE pa.id_product=' . (int) $productDto->getIdProduct() . ' '
                . 'AND al.id_lang=' . (int) Context::getContext()->language->id;
        $result = Db::getInstance()->executeS($sql);

        foreach($result as $attribute) {
            $productDto->setListAttribute($attribute);
        }
    }
    
    public function getIdCategoriesDefault(array $listIdsMarket)
    {
        $sql = 'SELECT DISTINCT id_category_default FROM '. _DB_PREFIX_ . 'product p '
                . 'INNER JOIN '. _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=p.id_supplier '
                . 'WHERE p.active=1 AND ms.id_market IN (' . implode(',', $listIdsMarket) . ')';
        $list = Db::getInstance()->executeS($sql);
        
        $listIdsCategoryDefault = array();
        foreach($list as $row) {
            $listIdsCategoryDefault[] = $row['id_category_default'];
        }
        
        return $listIdsCategoryDefault;
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
    
    private function prepareDtoV2($result, $multipleArray = false)
    {
        if($multipleArray) {
            $listObj = null;
            foreach($result as $key=>$row) {
                $obj = $this->createDtoV2($row);
                $listObj[$key] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDtoV2($result);
        }
    }
    
    private function createDtoV2($result)
    {
        $product = null;
        if($result) {
            $product = new ProductDto();
            $product->setReference(isset($result['reference']) ? $result['reference'] : '');
            $product->setDescription(isset($result['description']) ? $result['description'] : '');
            $product->setDescriptionShort(isset($result['description_short']) ? $result['description_short'] : '');
            $product->setIdMarket(isset($result['id_market']) ? $result['id_market'] : 0);
            $product->setIdProduct(isset($result['id_product']) ? $result['id_product'] : 0);
            $product->setIdSupplier(isset($result['id_supplier']) ? $result['id_supplier'] : 0);
            $product->setPrice(isset($result['price']) ? $result['price'] : '');
            $product->setProductName(isset($result['productName']) ? $result['productName'] : '');
            $product->setSupplierName(isset($result['supplierName']) ? $result['supplierName'] : '');
            $product->setUnitPriceRatio(isset($result['unit_price_ratio']) ? $result['unit_price_ratio'] : '');
            $product->setUnity(isset($result['unity']) ? $result['unity'] : '');
            $product->setIdImage(isset($result['id_image']) ? $result['id_image'] : 0);
            $product->setLinkRewrite(isset($result['link_rewrite']) ? $result['link_rewrite'] : '');
            $product->setCatLinkRewrite(isset($result['catLinkRewrite']) ? $result['catLinkRewrite'] : '');
            $product->setCategoryId(isset($result['catId']) ? $result['catId'] : 0);
            $product->setOwnerProduct(isset($result['owner_product']) ? $result['owner_product'] : 0);
            $product->setProduitAilleurs(isset($result['produit_ailleurs']) ? $result['produit_ailleurs'] : 0);
            $product->setDelayBeforeMarket(isset($result['delay_before_market']) ? $result['delay_before_market'] : 0);
            $product->setIdTaxRulesGroup(isset($result['id_tax_rules_group']) ? $result['id_tax_rules_group'] : 0);
            $product->setTax(isset($result['rate']) ? $result['rate'] : 0);
            $product->setOnSale(isset($result['on_sale']) ? $result['on_sale'] : 0);
            $product->setSpecificPrice(isset($result['specificprice']) ? $result['specificprice'] : 0);
            $product->setReduction(isset($result['reduction']) ? $result['reduction'] : 0);
            $this->getAttributeForProductDtoV2($product);
            
            $taxRuleGroup = $product->getIdTaxRulesGroup();
            if(!empty($taxRuleGroup)) {
                $taxRate = $this->getTaxForProduct($product->getIdTaxRulesGroup());
                $product->setTaxRate($taxRate['rate']);
            }
        }
        return $product;
    }
}