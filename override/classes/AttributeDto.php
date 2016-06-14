<?php
class AttributeDto {
    private $idAttribute;
    private $name;
    
    function getIdAttribute() {
        return $this->idAttribute;
    }

    function getName() {
        return $this->name;
    }

    function setIdAttribute($idAttribute) {
        $this->idAttribute = $idAttribute;
    }

    function setName($name) {
        $this->name = $name;
    }
}