<?php
class Category extends CategoryCore
{
    const CATEGORY_PANIER = 37;
    const CATEGORY_RECETTE = 44;
    const CATEGORY_ABONNEMENT_PANIER = 38;
    const CATEGORY_VINS_BIO = 24;
    const CATEGORY_ROTISSEUR = 42;
    const CATEGORY_ROTISSEUR_BIO = 43;
    const CATEGORY_TRAITEUR = 20;
    const CATEGORY_PATISSIER = 12;
    const CATEGORY_PATISSIER_BIO = 28;
    const CATEGORY_FROMAGER = 14;
    const CATEGORY_FROMAGER_BIO = 33;
    const CATEGORY_BOULANGER = 17;
    const CATEGORY_BOULANGER_BIO = 31;
    
    /* V2 */
    /**
     * Recupre LA categorie de produit associe a un supplier
     * @param int $idSupplier
     * @return supplierDto
     */
    public function getCategoryFromIdSupplier($idSupplier) {
        $sql = 'SELECT c.*, cl.name, cl.link_rewrite FROM ' . _DB_PREFIX_ . 'category c '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl ON cl.id_category=c.id_category '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_category_default=c.id_category '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_supplier ps ON ps.id_product=p.id_product '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=ps.id_supplier '
                . 'WHERE s.active=1 AND p.active=1 AND c.active=1 AND ps.id_supplier=' . (int) $idSupplier . ' GROUP BY c.id_category';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }

    public function hasBioForThisDay($idCategory, $stringDay, $diff) {
        //withdrawal = isBio
        $sql = 'SELECT s.* FROM ' . _DB_PREFIX_ . 'category c '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_category_default=c.id_category '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=p.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market m ON m.id_market=ms.id_market '
                . 'WHERE ' . $stringDay . '=1 AND c.id_category=' . (int) $idCategory . ' AND s.withdrawal=1 AND IF(sl.meta_title="", 2, sl.meta_title)<=' . (int) $diff . ' GROUP BY s.id_supplier';
        return Db::getInstance()->executeS($sql);
    }

    /* V1 */
    public function getAllCat() {
        $sql = 'SELECT DISTINCT cl.id_category, cl.name, cl.link_rewrite FROM ' . _DB_PREFIX_ . 'category_lang cl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category c ON c.id_category=cl.id_category '
                    . 'WHERE cl.id_category>2 AND c.active=1';
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true);
    }
    
    public function getCatFromIdMarket($idMarket, $options = array()) {
        $sql = 'SELECT DISTINCT cl.id_category, s.id_supplier, cl.name, cl.link_rewrite FROM ' . _DB_PREFIX_ . 'market m '
                    . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_market=m.id_market '
                    . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier s ON s.id_supplier=ms.id_supplier '
                    . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_supplier=ms.id_supplier '
                    . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl ON cl.id_category=p.id_category_default '
                    . 'WHERE cl.id_category!=2 ';
        if(!empty($options)) {
            $sql .= ' AND cl.id_category IN (' . implode(',', $options) . ') ';
        }
        $sql .= 'AND m.id_market=' . (int) $idMarket . ' ORDER BY id_category, s.id_supplier';
        //d($sql);
        $result = Db::getInstance()->executeS($sql);
        
        return $this->prepareDto($result, true, 'id_supplier');
    }
    
    public function getCatFromId($idCat) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang cl '
                    . 'WHERE cl.id_category=' . (int) $idCat;
        $result = Db::getInstance()->getRow($sql);
        if(empty($result)) {
            throw new Exception('notfoundidCat');
        }
        return $this->prepareDto($result);
    }
    
    public function getListIdProducts($idCat, $idMarket=0) {
        if(empty($idMarket)) {
            $sql = 'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p '
                    . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                    . 'WHERE p.active=1 AND p.id_category_default=' . (int) $idCat . ' ORDER BY p.id_supplier, pl.name';
        } else {
            $sql = 'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=p.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product=p.id_product '
                    . 'WHERE p.active=1 AND p.id_category_default=' . (int) $idCat . ' AND ms.id_market=' . (int) $idMarket . ' ORDER BY ms.id_supplier, pl.name';
        }
        $result = Db::getInstance()->executeS($sql);
        $list = array();
        foreach($result as $row) {
            $list[] = $row['id_product'];
        }
        
        return $list;
    }
    
    private function prepareDto($result, $multipleArray = false, $iterationName = '')
    {
        if($multipleArray) {
            $listObj = null;
            $key = 0;
            foreach($result as $key=>$row) {
                $obj = $this->createDto($row);
                if(!empty($iterationName)) { $key = $row[$iterationName]; }
                $listObj[$key] = $obj;
            }
            return $listObj;
        } else {
            return $this->createDto($result);
        }
    }
    
    private function createDto($result)
    {
        $category = null;
        if($result) {
            $category = new CategoryDto();
            $category->setName(isset($result['name']) ? $result['name'] : '');
            $category->setIdCategory(isset($result['id_category']) ? $result['id_category'] : 0);
            $category->setLinkRewrite(isset($result['link_rewrite']) ? $result['link_rewrite'] : '');
            $category->setPosition(isset($result['position']) ? $result['position'] : 0);
            $category->setIsBio(isset($result['isBio']) ? $result['isBio'] : 0);
        }
        return $category;
    }
}
