<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Address
 *
 * @author Julien
 */
class Address extends AddressCore {
    function checkAddressVoisin($customerID) {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'address '
                . 'WHERE id_customer=' . (int) $customerID . ' '
                . 'AND dni=\'voisin\'';
        return Db::getInstance()->executeS($sql);
    }
}
