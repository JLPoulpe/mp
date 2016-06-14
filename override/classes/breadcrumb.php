<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of breadcrumb
 *
 * @author julien
 */
class breadcrumb {
    private $controller;
    private $action;
    
    private $originalMenu = array(
            'accueil'           => array('href'=>'/', 'name'=>'Accueil'), 
            'marchesemaine'     => array('href'=>'/marches-locaux/semaine', 'name'=>'Vos marchés de la semaine'),
            'paniersemaine'     => array('href'=>'/paniers/semaine', 'name'=>'Les paniers de vos marchés'), 
            'paysans'           => array('href'=>'/paysans/liste', 'name'=>'Vos paysans'),
            'marcheproximite'   => array('href'=>'/marches-locaux/carte', 'name'=>'Localisez vos marchés'), 
        );
    
    public function __construct($controller='', $action='') {
        $this->controller = $controller;
        $this->action = $action;
        $this->_init();
    }
    
    private function _init() {
        switch(strtoupper(MPTools::getCookie('loc'))) {
            case 'IDF' :
                $this->originalMenu = array(
                    'accueil'           => array('href'=>'/', 'name'=>'Accueil'), 
                    'paniersemaine'     => array('href'=>'/paniers/semaine/idf', 'name'=>'Les paniers de vos marchés'),
                    'zonelivraison'     => array('href'=>'/livraison/villes/idf', 'name'=>'Notre zone de livraison'),
                    'marchesemaine'     => array('href'=>'/marches-locaux/semaine', 'name'=>'Vos marchés de la semaine'),
                );
                break;
            default:
                break;
        }
    }
    
    public function getMenuFromController() {
        switch ($this->controller) {
            case 'product' :
                $this->originalMenu['paniersemaine']['css'] = 'used';
                break;
            case 'cms' :
                break;
            default :
                break;
        }
        
        return $this->originalMenu;
    }
    
    public function getMenu() {
        switch ($this->action) {
            case 'carte' :
                $this->originalMenu['marcheproximite']['css'] = 'used';
                break;
            case 'marchesemaine' :
                $this->originalMenu['marchesemaine']['css'] = 'used';
                break;
            case 'paniersemaine' :
            case 'paniersjour' :
                $this->originalMenu['paniersemaine']['css'] = 'used';
                break;
            case 'paysans' :
                $this->originalMenu['paysans']['css'] = 'used';
                break;
            case 'fullnature' :
            case 'unpetitcreux' :
            case 'testVille' :
                break;
            case 'listeVilles' :
                if(isset($this->originalMenu['zonelivraison'])) {
                    $this->originalMenu['zonelivraison']['css'] = 'used';
                }
                break;
            default :
                break;
        }
        return $this->originalMenu;
    }
}
