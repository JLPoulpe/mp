<?php
class CategoryDto {
    private $idCategory;
    private $name;
    private $linkRewrite;
    private $position;
    private $isBio;
    
    function getIsBio() {
        return $this->isBio;
    }

    function setIsBio($isBio) {
        $this->isBio = $isBio;
    }

    function getPosition() {
        return $this->position;
    }

    function setPosition($position) {
        $this->position = $position;
    }

    function getIdCategory() {
        return $this->idCategory;
    }

    function getName() {
        return $this->name;
    }

    function getLinkRewrite() {
        return $this->linkRewrite;
    }

    function setIdCategory($idCategory) {
        $this->idCategory = $idCategory;
    }

    function setName($name) {
        $this->name = $name;
    }
    
    function setLinkRewrite($linkRewrite) {
        $this->linkRewrite = $linkRewrite;
    }
}