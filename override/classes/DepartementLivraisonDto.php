<?php
class DepartementLivraisonDto {
    private $codePostal;
    private $ville;
    private $departement;
    private $CodeDepartement;
    private $latitude;
    private $longitude;
    
    function getLatitude() {
        return $this->latitude;
    }

    function getLongitude() {
        return $this->longitude;
    }

    function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    function getDepartement() {
        return $this->departement;
    }

    function setDepartement($departement) {
        $this->departement = $departement;
    }

    function getCodeDepartement() {
        return $this->CodeDepartement;
    }

    function setCodeDepartement($CodeDepartement) {
        $this->CodeDepartement = $CodeDepartement;
    }

    function getCodePostal() {
        return $this->codePostal;
    }

    function getVille() {
        return $this->ville;
    }

    function setCodePostal($codePostal) {
        $this->codePostal = $codePostal;
    }

    function setVille($ville) {
        $this->ville = $ville;
    }
}
