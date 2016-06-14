<?php
class Image extends ImageCore {
    function getListIdImageForIdProduct($idProduct) {
        $sql = 'SELECT id_image FROM ' . _DB_PREFIX_ . 'image i WHERE id_product=' . (int) $idProduct . ' ORDER BY position';
        return Db::getInstance()->executeS($sql);
    }
}