<?php
class CustomerDto {
    private $idCustomer;
    private $firstname;
    private $lastname;
    private $email;
    private $gender;
    
    function getGender() {
        return $this->gender;
    }

    function setGender($gender) {
        $this->gender = $gender;
    }
    
    function getIdCustomer() {
        return $this->idCustomer;
    }

    function getFirstname() {
        return $this->firstname;
    }

    function getLastname() {
        return $this->lastname;
    }

    function getEmail() {
        return $this->email;
    }

    function setIdCustomer($idCustomer) {
        $this->idCustomer = $idCustomer;
    }

    function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    function setEmail($email) {
        $this->email = $email;
    }
}
