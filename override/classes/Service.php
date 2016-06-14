<?php
Class Service {
    /**
     * Permet de recuperer une liste de categorie par jour en fonction du code postal
     * @param int $numDep
     * @return array array(CategoryDto[])
     */
    public function getCategoriesByDays() {
        $market = new Market();
        $listMarket = $market->getMarket();
        
        if(empty($listMarket)) {
            throw new MPException('', MPException::$codeNoMarket);
        }
        
        // on recupere les jours d'affichage
        $listMarketByDate = array();
        $dateTime = new DateTime();
        $dayStart = 1;
        $dayEnd = 7;
        if((int) $dateTime->format('H')>=(int) MPTools::LIMITE_HOUR_COMMAND_TODAY) {
            $dateTime->modify('+1 day');
        }
        for($i=$dayStart;$i<$dayEnd;$i++) {
            if($i!=$dayStart) {
                $dateTime->modify('+1 day');
            }
            $dayOfWeek = $dateTime->format('w');
            if($dayOfWeek==1) {
                $dateTime->modify('+1 day');
                $dayOfWeek = $dateTime->format('w');
            }
            $stringDay = MPTools::$listJour[$dayOfWeek];
            foreach($listMarket as $marketDto) {
                if($marketDto->{(string) 'get' . $stringDay}()) {
                    $listMarketByDate[$dateTime->format('Ymd')][] = $marketDto->getIdMarket();
                }
            }
        }
        

        $listSupplierByDate = array();
        $supplier = new Supplier();
        foreach($listMarketByDate as $dateFormat=>$listIdMarket) {
            $nextDate = new DateTime($dateFormat);
            $dateTime = new DateTime();
            $interval = $dateTime->diff($nextDate, true);
            $diff = $interval->format('%a');
            // on recupere tous les paysans associes aux marches
            foreach($listIdMarket as $idMarket) {
                $listSupplier = $supplier->getFromIdMarketWithDelay($idMarket, $diff);
                if(!empty($listSupplier)) {
                    if(isset($listSupplierByDate[$nextDate->format('Ymd')])) {
                        $suppliers = $listSupplierByDate[$nextDate->format('Ymd')];
                        $listSupplier = array_merge($listSupplier, $suppliers);
                    }
                    $listSupplierByDate[$nextDate->format('Ymd')] = $listSupplier;
                }
            }
        }
        unset($listSupplier);

        if(empty($listSupplierByDate)) {
            throw new MPException('', MPException::$codeNoSupplier);
        }
        
        unset($listMarket);
        // a partir des idSupplier on recupere, par jour, les cartegories de leurs produits
        $listCategoryByDate = array();
        $category = new Category();
        foreach($listSupplierByDate as $date=>$listSupplier) {
            $listCategoryForIdSupplier = array();
            foreach ($listSupplier as $supplierDto) {
                $listCategoryForIdSupplier[] = $category->getCategoryFromIdSupplier($supplierDto->getIdSupplier());
            }
            $listCategoryByDate[$date] = $this->deleteDoublon($listCategoryForIdSupplier);
        }
        unset($listSupplierByDate, $listCategoryForIdSupplier);
        // on ordonne les categories par position
        foreach($listCategoryByDate as $date=>$listCategoryDto) {
            $tmp = array();
            foreach($listCategoryDto as $categoryDto) {
                $tmp[$categoryDto->getPosition()][] = $categoryDto;
            }
            ksort($tmp);
            $listCategoryByDate[$date] = $tmp;
        }
        if(empty($listCategoryByDate)) {
            throw new MPException('', MPException::$codeNoCateggory);
        }
        ksort($listCategoryByDate);
        return $listCategoryByDate;
    }

    /**
     * Permet de recuperer une liste de categorie par jour en fonction du code postal
     * @return array array(CategoryDto[])
     */
    public function getCategoriesByDaysWithoutPosition() {
        // on recupere la liste des marches proche de $numDep
        $market = new Market();
        $listMarket = $market->getMarket();
        
        if(empty($listMarket)) {
            throw new MPException('', MPException::$codeNoMarket);
        }
        
        // on recupere les jours d'affichage
        $listMarketByDate = array();
        $dateTime = new DateTime();
        $dayStart = 1;
        $dayEnd = 7;
        if($dateTime->format('H')<MPTools::LIMITE_HOUR_COMMAND_TODAY) {
            $dayStart = 0;
            $dayEnd = 6;
        }
        for($i=$dayStart;$i<$dayEnd;$i++) {
            if($i!=$dayStart) {
                $dateTime->modify('+1 day');
            }
            $dayOfWeek = $dateTime->format('w');
            if($dayOfWeek==1) {
                $dateTime->modify('+1 day');
                $dayOfWeek = $dateTime->format('w');
            }
            $stringDay = MPTools::$listJour[$dayOfWeek];
            foreach($listMarket as $marketDto) {
                if($marketDto->{(string) 'get' . $stringDay}()) {
                    $listMarketByDate[$dateTime->format('Ymd')][] = $marketDto->getIdMarket();
                }
            }
        }
        
        $listSupplierByDate = array();
        $supplier = new Supplier();
        foreach($listMarketByDate as $dateFormat=>$listIdMarket) {
            $nextDate = new DateTime($dateFormat);
            $dateTime = new DateTime();
            $interval = $dateTime->diff($nextDate, true);
            $diff = $interval->format('%a');
            // on recupere tous les paysans associes aux marches
            foreach($listIdMarket as $idMarket) {
                $listSupplier = $supplier->getFromIdMarketWithDelay($idMarket, $diff);
                if(!empty($listSupplier)) {
                    if(isset($listSupplierByDate[$nextDate->format('Ymd')])) {
                        $suppliers = $listSupplierByDate[$nextDate->format('Ymd')];
                        $listSupplier = array_merge($listSupplier, $suppliers);
                    }
                    $listSupplierByDate[$nextDate->format('Ymd')] = $listSupplier;
                }
            }
        }
        unset($listMarket, $listSupplier, $listMarketByDate);

        if(empty($listSupplierByDate)) {
            throw new MPException('', MPException::$codeNoSupplier);
        }
        // on ne garde que les 7 premiers marches
        if(count($listSupplierByDate)>7) {
            array_pop($listSupplierByDate);
        }
        
        // a partir des idSupplier on recupere, par jour, les categories de leurs produits
        $listCategoryByDate = array();
        $category = new Category();
        foreach($listSupplierByDate as $date=>$listSupplier) {
            $listCategoryForIdSupplier = array();
            foreach ($listSupplier as $supplierDto) {
                $categoryDto = $category->getCategoryFromIdSupplier($supplierDto->getIdSupplier());
                $listCategoryForIdSupplier[] = $categoryDto;
            }
            $listCategoryByDate[$date] = $this->deleteDoublon($listCategoryForIdSupplier);
        }
        unset($listSupplierByDate, $listCategoryForIdSupplier);
        
        foreach($listCategoryByDate as $date=>$listCategory) {
            $tmp = array();
            $nextDate = new DateTime($date);
            $dateTime = new DateTime();
            $interval = $dateTime->diff($nextDate, true);
            $diff = $interval->format('%a');
            $stringDay = strtolower(MPTools::$listJour[$nextDate->format("w")]);
            foreach($listCategory as $categoryDto) {
                $result = $category->hasBioForThisDay($categoryDto->getIdCategory(), $stringDay, $diff);
                $categoryDto->setIsBio(!empty($result));
                $tmp[] = $categoryDto;
            }
            $listCategoryByDate[$date] = $tmp;
        }
        
        if(empty($listCategoryByDate)) {
            throw new MPException('', MPException::$codeNoCateggory);
        }
        ksort($listCategoryByDate);
        return $listCategoryByDate;
    }
    
    /**
     * Supprimer les doublons de categories
     * @param array $listCategoryForIdSupplier
     * @return CategoryDto[]
     */
    private function deleteDoublon(array $listCategoryForIdSupplier) {
        $newListCategory = array();
        /*@var $categoryDto CategoryDto*/
        foreach($listCategoryForIdSupplier as $k=>$listCategoryDto) {
            if(!empty($listCategoryDto)) {
                foreach($listCategoryDto as $k=>$categoryDto) {
                    if($categoryDto instanceOf CategoryDto) {
                        $newListCategory[$categoryDto->getIdCategory()] = $categoryDto;
                    }
                }
            }
        }
        
        return $newListCategory;
    }
    
    /**
     * Recupere la liste des produits (et leur attributs) pour chaque supplier
     * @param string $day
     * @return array
     * @throws MPException
     */
    public function getProductsBySupplierByDayAndCategoryId($day, $categoryId, $date) {
        $dateMarket = new DateTime($date);
        $dateTime = new DateTime();
        $dateMarket->setTime($dateTime->format('H'), $dateTime->format('i'), $dateTime->format('s'));
        $interval = $dateTime->diff($dateMarket, true);
        $diff = $interval->format('%a');
        // on recupere la liste des paysans present sur les marches le jour $day
        $supplier = new Supplier();
        $listSupplier = $supplier->getSupplierFromDayAndCategoryIdWithDelay($day, $categoryId, $diff);
        if(empty($listSupplier)) {
            throw new MPException('', MPException::$codeNoSupplier);
        }

        $listProductBySupplier = array();
        $product = new Product();
        foreach($listSupplier as $supplierDto) {
            $listProducts = $product->getProductsByIdSupplierAndCategoryId($supplierDto->getIdSupplier(), $categoryId, $diff);
            if(!empty($listProducts)) {
                $listProductBySupplier[] = array('supplierDto' => $supplierDto, 'listProducts'=>$listProducts);
            }
        }

        return $listProductBySupplier;
    }

    /*
    public function getProductsBySupplierCategoryId($numDep, $supplierId) {
        $listProducts = $product->getProductsByIdSupplier($supplierDto->getIdSupplier());
        
        return $listProducts;
    }*/
    
    /**
     * Retourne les 5 dernieres recettes
     * @param int $nbRecettes
     * @return CMSDto[]
     */
    public function getNbRecettes($nbRecettes = 5) {
        $cms = new CMS();
        $listRecettes = $cms->getNbRecettes($nbRecettes);
        
        return $listRecettes;
    }
    
    /**
     * Retourne les 1à dernieres recettes par type de plat (entree, plat etc...)
     * @return CMSDto[]
     */
    public function getLastTenRecettesByTypePlat() {
        $listRecettes = array();
        $cms = new CMS();
        $listRecettes['Entrée']         = $cms->getLastTenRecettesByTypePlat(CMS::CMS_KEYWORD_ENTREE);
        $listRecettes['Plat']           = $cms->getLastTenRecettesByTypePlat(CMS::CMS_KEYWORD_PLAT);
        $listRecettes['Accompagnement'] = $cms->getLastTenRecettesByTypePlat(CMS::CMS_KEYWORD_ACCOMPAGNEMENT);
        $listRecettes['Dessert']        = $cms->getLastTenRecettesByTypePlat(CMS::CMS_KEYWORD_DESSERT);
        
        return $listRecettes;
    }
    
    /**
     * Retourne une liste de recette en fonction des $keywords et $typePlatCode
     * @param string $keywords
     * @param int $typePlatCode
     * @return CMSDto[]
     */
    public function getRecettesBySearch($keywords, $typePlatCode) {
        $listSearch = array();
        if(!empty($keywords)) {
            $listSearchComma = str_replace(' ', ',', $keywords);
            $listSearch = explode(',', $listSearchComma);
        }
        switch ($typePlatCode) {
            case CMS::CMS_KEYWORD_ENTREE_CODE :
                array_push($listSearch, CMS::CMS_KEYWORD_ENTREE);
                break;
            case CMS::CMS_KEYWORD_PLAT_CODE :
                array_push($listSearch, CMS::CMS_KEYWORD_ENTREE);
                break;
            case CMS::CMS_KEYWORD_ACCOMPAGNEMENT_CODE :
                array_push($listSearch, CMS::CMS_KEYWORD_ACCOMPAGNEMENT);
                break;
            case CMS::CMS_KEYWORD_DESSERT_CODE :
                array_push($listSearch, CMS::CMS_KEYWORD_DESSERT);
                break;
            default :
                break;
        }
        $cms = new CMS();
        $listRecettesSearch = $cms->getRecettesByMetaKeywords($listSearch);
        
        return $listRecettesSearch;
    }
    
    /**
     * Retourne l'article ayant comme id $cmsId
     * @param int $cmsId
     * @return CMSDto
     */
    public function getCmsById($cmsId) {
        $cms = new CMS();
        return $cms->getById($cmsId);
    }
    
    public function addAddress($customerID, $firstname, $name, $tel, $mobile, $address, $address2, $cp, $city, $infos, $addressName) {
        $serviceAddress = new AddressCore();
        $serviceAddress->id_country = 8;
        $serviceAddress->address1 = strip_tags($address);
        $serviceAddress->address2 = strip_tags($address2);
        $serviceAddress->alias = strip_tags($addressName);
        $serviceAddress->city = strip_tags($city);
        $dateTime = new DateTime();
        $serviceAddress->date_add = $dateTime->format('Y-m-d');
        $serviceAddress->dni = 'voisin';
        $serviceAddress->firstname = strip_tags($firstname);
        $serviceAddress->id_customer = (int)$customerID;
        $serviceAddress->lastname = strip_tags($name);
        $serviceAddress->other = strip_tags($infos);
        $serviceAddress->phone = strip_tags($tel);
        $serviceAddress->phone_mobile = strip_tags($mobile);
        $serviceAddress->postcode = (int)$cp;
             
        return $serviceAddress->add(true);
    }
    
    public function checkAddressVoisin($customerID) {
        $serviceAddress = new Address();
        $result = $serviceAddress->checkAddressVoisin($customerID);
        if(!empty($result)) {
            return true;
        }
    }
    
    public function getCMSByContent($idCms, $idLang = null, $idShop = null) {
        $cms = new CMS();
        return $cms->getCMSContent($idCms, $idLang, $idShop);
    }
    
    /**
     * Recupere les recettes par $idCMSCategory et type de plat 
     * @param int $idCMSCategory
     * @return array
     */
    public function getCMSArticlesByIdCMSCategory($idCMSCategory) {
        $cms = new CMS();
        $listRecettes = array();
        $listRecettes['Entrée']         = $cms->getArticlesByIdCMSCategoryAndTypePlat($idCMSCategory, CMS::CMS_KEYWORD_ENTREE);
        $listRecettes['Plat']           = $cms->getArticlesByIdCMSCategoryAndTypePlat($idCMSCategory, CMS::CMS_KEYWORD_PLAT);
        $listRecettes['Accompagnement'] = $cms->getArticlesByIdCMSCategoryAndTypePlat($idCMSCategory, CMS::CMS_KEYWORD_ACCOMPAGNEMENT);
        $listRecettes['Dessert']        = $cms->getArticlesByIdCMSCategoryAndTypePlat($idCMSCategory, CMS::CMS_KEYWORD_DESSERT);
        
        return $listRecettes;
    }
    
    /**
     * Recupere le prochain jour de presence d'un supplier
     * @param int $idSupplier
     * 
     * @return string
     */
    public function getNextDateForSupplier($idSupplier) {
        $supplier = new Supplier();
        $listMarkets = $supplier->getMarkets($idSupplier);
        $market = new Market();
        foreach($listMarkets as $row) {
            $dto = $market->getMarketById($row['id_market']);
            $dateTime = $dto->getNextDateOpen();
            $marketDto[$dateTime->format('Ymd')] = $dateTime;
        }
        ksort($marketDto);
        $dateTime = array_shift($marketDto);
        $stringDay = MPTools::$listJour[$dateTime->format('w')];
        return strtolower($stringDay);
    }

    public function getListSupplier() {
        $supplier = new Supplier();
        $list = $supplier->getListSupplier();
        $category = new Category();
        $listSupplierByCategory = array();
        foreach ($list as $supplierDto) {
            $listCategory = $category->getCategoryFromIdSupplier($supplierDto->getIdSupplier());
            foreach($listCategory as $categoryDto) {
                $listSupplierByCategory[$categoryDto->getIdCategory()]['category'] = $categoryDto;
                $listSupplierByCategory[$categoryDto->getIdCategory()]['suppliers'][] = $supplierDto;
            }
        }
        
        return $listSupplierByCategory;
    }
}