<?php
class statisticsNewsDto {
    private $idStatisticsNews;
    private $dateNews;
    private $idUser;
    private $dateTimeAction;
    private $type;
    
    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
    }

    function getIdStatisticsNews() {
        return $this->idStatisticsNews;
    }

    function getDateNews() {
        return $this->dateNews;
    }

    function getIdUser() {
        return $this->idUser;
    }

    function getDateTimeAction() {
        return $this->dateTimeAction;
    }

    function setIdStatisticsNews($idStatisticsNews) {
        $this->idStatisticsNews = $idStatisticsNews;
    }

    function setDateNews($dateNews) {
        $this->dateNews = $dateNews;
    }

    function setIdUser($idUser) {
        $this->idUser = $idUser;
    }

    function setDateTimeAction($dateTimeAction) {
        $this->dateTimeAction = $dateTimeAction;
    }
}