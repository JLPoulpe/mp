<?php
class Attribute extends AttributeCore {
    /**
     * @param int $idAttribute
     * @return attributeDto
     */
    public function getAttributeFromId($idAttribute) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'attribute_lang al '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON pac.id_attribute=al.id_attribute '
                . 'WHERE pac.id_product_attribute=' . (int) $idAttribute . ' '
                . 'AND al.id_lang=' . (int) Context::getContext()->language->id;
        $result = Db::getInstance()->getRow($sql);
        return $this->prepareDto($result);
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
        $attribute = null;
        if($result) {
            $attribute = new AttributeDto();
            $attribute->setIdAttribute(isset($result['id_attribute']) ? $result['id_attribute'] : 0);
            $attribute->setName(isset($result['name']) ? $result['name'] : '');
        }
        return $attribute;
    }
}