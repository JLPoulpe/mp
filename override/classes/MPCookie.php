<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cookie
 *
 * @author Julien
 */
class MPCookie {
    public function writeCookie($name, $content = array()) {
        $contentCrypt = base64_encode($content);
        $nameHashed = self::getName($name);
        return setcookie($nameHashed, $contentCrypt, time() + 1728000, '/');
    }

    public function readCookie($name) {
        $nameCrypt = self::getName($name);
        $contentCrypt = filter_input(INPUT_COOKIE, $nameCrypt);
        $content = base64_decode($contentCrypt);
        return $content;
    }

    private static function getName($name) {
        $nameHash = 'mespaysans-' . md5($name);
        return $nameHash;
    }
}
