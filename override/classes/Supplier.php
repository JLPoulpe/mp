<?php
Class Supplier extends SupplierCore {
   const MESPAYSANS_IDF = 58;
   const MESPAYSANS_AQUITAINE = 52;
   const FULL_NATURE = 28;
   
   public static function getMPByRegion() {
       $cookieLoc = filter_input(INPUT_COOKIE, 'loc');
       if(!$cookieLoc) {
           $cookieLoc = 'Aquitaine';
       }
       $mp = 0;
       switch(strtoupper($cookieLoc)) {
           case 'Aquitaine' :
                $mp = self::MESPAYSANS_AQUITAINE;
                break;
            case 'IDF' :
                $mp = self::MESPAYSANS_IDF;
                break;
            default:
                $mp = self::MESPAYSANS_AQUITAINE;
                break;
       }
       
       return $mp;
   }

    // GESTION DU BO
    public static function getListSupplierForBO() {
        $sql = 'SELECT id_supplier, name FROM '._DB_PREFIX_. 'supplier'
                . ' ORDER BY name';
        return Db::getInstance()->executeS($sql);
    }
    
    public function getCount() {
        $sql = 'SELECT COUNT(id_supplier) as count FROM '._DB_PREFIX_. 'supplier WHERE active=1';
        $result = Db::getInstance()->executeS($sql);
        
        return $result[0]['count'];
    }
    
    // GESTION DU FRONT
    
    /** V2 **/
    /**
     * Retourne un producteur en fonction d'un idProduct donné
     * @param int $idProduct
     * @return SupplierDto
     */
    public function getFromIdProduct($idProduct) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_lang sl ON sl.id_supplier=s.id_supplier AND sl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'INNER JOIN ' . _DB_PREFIX_ .'product p ON p.id_supplier=s.id_supplier '
                . 'WHERE p.id_product=' . (int) $idProduct;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result);
    }
    /**
     * Renvoi la liste des Supplier d un marche
     * @param int $idMarket
     * @return supplierDto[]
     */
    public function getFromIdMarket($idMarket) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'WHERE s.active=1 AND ms.id_market=' . (int) $idMarket . ' AND sl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'GROUP BY s.id_supplier ORDER BY s.id_supplier';
        
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Renvoi la liste des Supplier d un marche en prenant en compte la disponibilite des suppliers
     * @param int $idMarket
     * @param int $delayToMarket
     * @return supplierDto[]
     */
    public function getFromIdMarketWithDelay($idMarket, $delayToMarket) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'WHERE s.active=1 AND ms.id_market=' . (int) $idMarket . ' AND sl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND IF(meta_title="", 2, meta_title)<=' . (int) $delayToMarket . ' '
                . 'GROUP BY s.id_supplier ORDER BY s.withdrawal DESC';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    /**
     * Retourne la liste des paysans present sur les marches du jour $day
     * @param string $day
     * @param int $numDep
     * @return supplierDto[]
     */
    public function getSupplierFromDayAndCategoryId($day, $numDep, $categoryId) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'product p ON s.id_supplier=p.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'market m ON m.id_market=ms.id_market '
                . 'WHERE s.active=1 AND m.' . $day . '=1 AND p.id_category_default=' . (int) $categoryId .' AND sl.id_lang=' . (int) Context::getContext()->language->id . ' AND m.postal_code LIKE "' . (int) $numDep . '%" AND m.active=1 '
                . 'GROUP BY s.id_supplier ORDER BY p.id_category_default, s.id_supplier';

        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Retourne la liste des paysans present sur les marches du jour $day en prenant en compte la disponibilite des suppliers
     * @param string $day
     * @param int $numDep
     * @param int $delayToMarket
     * @return supplierDto[]
     */
    public function getSupplierFromDayAndCategoryIdWithDelay($day, $categoryId, $delayToMarket) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'product p ON s.id_supplier=p.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'market m ON m.id_market=ms.id_market '
                . 'WHERE s.active=1 AND m.' . $day . '=1 AND p.id_category_default=' . (int) $categoryId .' AND sl.id_lang=' . (int) Context::getContext()->language->id . ' AND m.active=1 '
                . 'AND IF(meta_title="", 2, meta_title)<=' . (int) $delayToMarket . ' '
                . 'GROUP BY s.id_supplier '
                . 'ORDER BY p.owner_product DESC, s.id_supplier';
        //echo '<!--' . print_r($sql, true) . '-->';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Recupere l'idSupplier lie au compte client
     * @param int $idUser
     * @return array
     */
    public function getSupplierIdsByIdUser($idUser) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'supplier_account ms WHERE id_account=' . (int) $idUser;
        $result = Db::getInstance()->executeS($sql);
        
        $listIds = array();
        foreach($result as $row) {
            $listIds[] = $row['id_supplier'];
        }
        
        return $listIds;
    }
    
    /**
     * Recupere les informations propre au supplier
     * @param int $idSupplier
     * @return supplierDto
     */
    public function getByIdV2($idSupplier)
    {
        $sql = 'SELECT s.id_supplier, s.active, s.name, s.withdrawal, sl.description, sl.meta_title FROM ' . _DB_PREFIX_ . 'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'WHERE s.id_supplier=' . (int) $idSupplier . ' '
                . 'AND s.active=1';
        
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, false);
    }
    
    public function getListSupplier() {
       $sql = 'SELECT * FROM '._DB_PREFIX_. 'supplier s '
               . 'INNER JOIN '._DB_PREFIX_. 'supplier_lang sl ON s.id_supplier=sl.id_supplier '
               . 'INNER JOIN ' . _DB_PREFIX_ .'market_supplier ms ON ms.id_supplier=s.id_supplier '
               . 'INNER JOIN ' . _DB_PREFIX_ .'market m ON m.id_market=ms.id_market '
               . 'WHERE s.active=1 AND m.postal_code LIKE "33%" AND sl.id_lang=' . (int) Context::getContext()->language->id . ' GROUP BY s.id_supplier';
       $result = Db::getInstance()->executeS($sql);
       return $this->prepareDto($result, true);
    }
    
    /** V1 **/
    public static function getSuppliersForForm($listSupplier = array()) {
        $condition = '';
        if(count($listSupplier)) {
             $condition = ' WHERE id_supplier NOT IN (' . implode(',', $listSupplier) . ')';
        }
        return Db::getInstance()->executeS('SELECT id_supplier, name FROM ' . _DB_PREFIX_ . 'supplier' . $condition);
    }
    
    /**
     * @return SupplierDto[]
     */
    public function getAll() {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_account sa ON sa.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'WHERE s.active=1 AND sl.id_lang=' . (int) Context::getContext()->language->id;
        
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * @param int $codeDepartement
     * @return SupplierDto[]
     */
    public function getAllByDepartement($codeDepartement) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_account sa ON sa.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'customer c ON c.id_customer=sa.id_account '
                . 'INNER JOIN ' . _DB_PREFIX_ .'address a ON a.id_customer=c.id_customer '
                . 'WHERE s.active=1 '
                . 'AND sl.id_lang=' . (int) Context::getContext()->language->id .' '
                . 'AND a.postcode LIKE "' . $codeDepartement . '%"';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * @return SupplierDto
     */
    public function getAllInfo($idSupplier) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ .'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ .'supplier_account sa ON sa.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ .'customer c ON c.id_customer=sa.id_account '
                . 'WHERE s.id_supplier=' . (int) $idSupplier;
        
        $result = Db::getInstance()->getRow($sql);
        
        return $this->prepareDto($result);
    }
    
    /**
     * 
     * @param int $idSupplier
     * @return supplierDto
     */
    public function getById($idSupplier)
    {
        $sql = 'SELECT s.id_supplier, s.active, s.name, s.withdrawal, sl.description, sl.meta_title, ms.id_market, p.id_product, cl.name as catName FROM ' . _DB_PREFIX_ . 'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl ON cl.id_category=p.id_category_default '
                . 'WHERE s.id_supplier=' . (int) $idSupplier . ' '
                . 'AND p.active=1';
        
        $result = Db::getInstance()->executeS($sql);
        
        $supplierDto = null;
        foreach($result as $row) {
            if(isset($supplierDto)) {
                /*@var $dto SupplierDto*/
                $dto = $supplierDto;
                $dto->addIdMarket($row['id_market']);
                $listIdsProduct = $dto->getListIdProduct();
                if(!in_array($row['id_product'], $listIdsProduct)) {
                    $dto->addIdProduct($row['id_product']);
                    $dto->setCategorie($row['catName']);
                }
            } else {
                $dto = $this->createDto($row);
            }
            $supplierDto = $dto;
        }

        return $supplierDto;
    }
    
    /**
     * 
     * @param int $idSupplier
     * @return array
     */
    public function getMarkets($idSupplier) {
        $sql = 'SELECT ms.id_market FROM ' . _DB_PREFIX_ . 'market_supplier ms '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market m ON m.id_market=ms.id_market '
                . 'WHERE ms.id_supplier=' . (int) $idSupplier . ' AND m.active=1';
        return Db::getInstance()->executeS($sql);
    }
    
    /**
     * 
     * @param int $idMarket
     * @param int $nbDaysBeforeMarket
     * @param array $options
     * @return supplierDto[]
     */
    public function getAllByIdMarket($idMarket, $nbDaysBeforeMarket=7, $options = array(), $loc='aquitaine') {
        $sql = 'SELECT DISTINCT s.id_supplier, s.withdrawal, s.active, s.name, sl.description, sl.meta_title, if(sl.meta_title>'.$nbDaysBeforeMarket.',0,1) as isDispo, ' . $idMarket . ' as id_market, cs.position FROM ' . _DB_PREFIX_ . 'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON cs.id_category=p.id_category_default '
                . 'WHERE ms.id_market=' . (int) $idMarket . ' ';
                if(!empty($options)) {
                    $sql .= 'AND s.id_supplier IN (' . implode(',', $options) . ') ';
                }
                if($loc==DepartementLivraison::AQUITAINE) {
                    $sql .= 'AND sl.meta_keywords IN (\'' . DepartementLivraison::AQUITAINE . '\', \'\') ';
                } else {
                    $sql .= 'AND sl.meta_keywords=\'' . DepartementLivraison::IDF . '\' ';
                }
        $sql .= 'AND s.active=1 '
                . 'ORDER BY isDispo, cs.position, s.id_supplier';
        d($sql);
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    public function getLastSupplier($nb) {
        $sql = 'SELECT DISTINCT s.id_supplier, s.name, ms.id_market FROM ' . _DB_PREFIX_ . 'supplier s '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'supplier_lang sl ON sl.id_supplier=s.id_supplier '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'market_supplier ms ON ms.id_supplier=s.id_supplier '
                . 'WHERE s.active=1 '
                . 'ORDER BY s.id_supplier DESC '
                . 'LIMIT ' . $nb;
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    public function getSentences($delay, $idMarket) {
        $sentences = array(
            'Bonjour,<br />nous sommes trop proche du marché pour avoir le temps de préparer votre commande. Vous pouvez par contre me retrouver sur <a href="">ce marché</a>',
        );
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
        $supplier = null;
        if($result) {
            $supplier = new SupplierDto();
            $supplier->setActive(isset($result['active']) ? $result['active'] : 0);
            $supplier->setDescription(isset($result['description']) ? $result['description'] : '');
            $supplier->setMetaDescription(isset($result['meta_description']) ? $result['meta_description'] : '');
            $supplier->setIdSupplier(isset($result['id_supplier']) ? $result['id_supplier'] : 0);
            $supplier->setListIdMarket();
            $supplier->addIdProduct(isset($result['id_product']) ? $result['id_product'] : 0);
            $supplier->setName(isset($result['name']) ? $result['name'] : '');
            $supplier->setCategorie(isset($result['catName']) ? $result['catName'] : '');
            $supplier->setLastDispo(isset($result['meta_title']) ? $result['meta_title'] : 0);
            $supplier->setIsDispo(isset($result['isDispo']) ? $result['isDispo'] : 0);
            $supplier->setIdCustomer(isset($result['id_customer']) ? $result['id_customer'] : 0);
            $supplier->setIsBio(isset($result['withdrawal']) ? $result['withdrawal'] : 0);
            $supplier->setFirstname(isset($result['firstname']) ? $result['firstname'] : '');
            $supplier->setLastname(isset($result['lastname']) ? $result['lastname'] : '');
            $supplier->setEmail(isset($result['email']) ? $result['email'] : '');
        }
        return $supplier;
    }
}