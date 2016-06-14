<?php
/**
 * Description of CMS
 *
 * @author Julien
 */
class CMS extends CMSCore{
    const CMS_CATEGORY_RECETTES = 2;
    const CMS_CATEGORY_RECETTES_CHEF_JESUS = 3;
    const CMS_KEYWORD_ENTREE_CODE = '1';
    const CMS_KEYWORD_ENTREE = 'entree';
    const CMS_KEYWORD_PLAT_CODE = 2;
    const CMS_KEYWORD_PLAT = 'plat';
    const CMS_KEYWORD_ACCOMPAGNEMENT_CODE = 3;
    const CMS_KEYWORD_ACCOMPAGNEMENT = 'accompagnement';
    const CMS_KEYWORD_DESSERT_CODE = 4;
    const CMS_KEYWORD_DESSERT = 'dessert';
    
    const CMS_ARTICLE_CHEF_JESUS = 12;
    const CMS_ARTICLE_LIVRAISON = 13;
    const CMS_ARTICLE_PARTENAIRE = 15;
    const CMS_ARTICLE_VINSBIO = 14;
    /**
     * Retourne le nombre $nbRecettes de recette a partir des derniers articles
     * @param int $nbRecettes
     * @return CMSDto[]
     */
    public function getNbRecettes($nbRecettes) {
        $sql = 'SELECT cl.*, c.id_cms_category FROM ' . _DB_PREFIX_ . 'cms_lang cl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cms c ON c.id_cms=cl.id_cms '
                . 'WHERE cl.id_lang=' . (int) Context::getContext()->language->id . ' AND (c.id_cms_category=' . self::CMS_CATEGORY_RECETTES . ' OR c.id_cms_category=' . self::CMS_CATEGORY_RECETTES_CHEF_JESUS . ') '
                . 'ORDER BY cl.id_cms DESC LIMIT ' . (int) $nbRecettes;
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Retourne les 10 dernieres recettes pour une type de plat (entre, plat, etc ...)
     * @param string $typePlat
     * @return CMSDto[]
     */
    public function getLastTenRecettesByTypePlat($typePlat) {
        $sql = 'SELECT cl.*, c.id_cms_category FROM ' . _DB_PREFIX_ . 'cms_lang cl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cms c ON c.id_cms=cl.id_cms '
                . 'WHERE cl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND (c.id_cms_category=' . self::CMS_CATEGORY_RECETTES . ' OR c.id_cms_category=' . self::CMS_CATEGORY_RECETTES_CHEF_JESUS . ') '
                . 'AND cl.meta_keywords LIKE "%' . $typePlat . '%" '
                . 'ORDER BY cl.id_cms DESC LIMIT 10';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Retourne une liste de recette en fonction de $listKeywords
     * @param array $listKeywords
     * @return CMSDto[]
     */
    public function getRecettesByMetaKeywords(array $listKeywords) {
        $sql = 'SELECT cl.*, c.id_cms_category FROM ' . _DB_PREFIX_ . 'cms_lang cl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cms c ON c.id_cms=cl.id_cms '
                . 'WHERE cl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND (c.id_cms_category=' . self::CMS_CATEGORY_RECETTES . ' OR c.id_cms_category=' . self::CMS_CATEGORY_RECETTES_CHEF_JESUS . ') AND (';
        foreach($listKeywords as $key=>$keyword) {
            if(!empty($key)) {
                $sql .= ' OR ';
            }
            $sql .= 'cl.meta_keywords LIKE \'%' . $keyword . '%\' ';
        }
        $sql .= ') '
                . 'ORDER BY cl.id_cms DESC LIMIT 10';
        $result = Db::getInstance()->executeS($sql);
        return $this->prepareDto($result, true);
    }
    
    /**
     * Retourne l'article ayant comme id $cmsId
     * @param int $cmsId
     * @return CMSDto
     */
    public function getById($cmsId) {
        $sql = 'SELECT cl.*, c.id_cms_category FROM ' . _DB_PREFIX_ . 'cms_lang cl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cms c ON c.id_cms=cl.id_cms '
                . 'WHERE cl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND cl.id_cms=' . (int) $cmsId;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result);
    }
    
    public function getArticlesByIdCMSCategoryAndTypePlat($idCMSCategory, $typePlat) {
        $sql = 'SELECT cl.*, c.id_cms_category FROM ' . _DB_PREFIX_ . 'cms_lang cl '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'cms c ON c.id_cms=cl.id_cms '
                . 'WHERE cl.id_lang=' . (int) Context::getContext()->language->id . ' '
                . 'AND c.id_cms_category=' . (int)$idCMSCategory . ' '
                . 'AND cl.meta_keywords LIKE "%' . $typePlat . '%" '
                . 'ORDER BY cl.id_cms DESC';
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
        $cms = null;
        if($result) {
            $cms = new CMSDto();
            $cms->setCmsId(isset($result['id_cms']) ? $result['id_cms'] : 0);
            $cms->setContent(isset($result['content']) ? $result['content'] : '');
            $cms->setLinkRewrite(isset($result['link_rewrite']) ? $result['link_rewrite'] : '');
            $cms->setMetaKeywords(isset($result['meta_keywords']) ? $result['meta_keywords'] : '');
            $cms->setMetaTitle(isset($result['meta_title']) ? $result['meta_title'] : '');
            $cms->setIdCategory(isset($result['id_cms_category']) ? $result['id_cms_category'] : 0);
        }
        return $cms;
    }
}
