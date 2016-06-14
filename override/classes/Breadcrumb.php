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
class Breadcrumb {
    private $action;

    public function __construct($action='') {
        $this->action = $action;
    }

    public function getMenu($name, $numDep = 0) {
        $breadcrumb = array();
        switch ($this->action) {
            case 'lesproduitsdujour' :
            case 'lesproduitsdujourbio' :
            case 'paniersjour' :
                if($numDep!=DepartementLivraison::VALDOISE) {
                    $breadcrumb[0]['href'] = '/je-fais-mon-marche';
                    $breadcrumb[0]['name'] = 'Je fais mon marché';
                }
                $breadcrumb[1]['name'] = $name;
                break;
            case 'jefaismonmarche' :
                $breadcrumb[0]['name'] = 'Je fais mon marché';
                break;
            case 'chefjesus' :
            case 'livraison' :
            case 'partenaire' :
            case 'vinsbio' :
            case 'listePaysans' : 
                $breadcrumb[0]['name'] = $name;
                break;
            default :
                break;
        }
        return $breadcrumb;
    }
}