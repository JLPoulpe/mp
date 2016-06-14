<?php
/**
 * Description of CMSDto
 *
 * @author Julien
 */
class CMSDto {
    private $cmsId;
    private $metaTitle;
    private $metaKeywords;
    private $content;
    private $linkRewrite;
    private $idCategory;

    function getIdCategory() {
        return $this->idCategory;
    }

    function setIdCategory($idCategory) {
        $this->idCategory = $idCategory;
    }

    function getCmsId() {
        return $this->cmsId;
    }

    function getMetaTitle() {
        return $this->metaTitle;
    }

    function getMetaKeywords() {
        return $this->metaKeywords;
    }

    function getContent() {
        return $this->content;
    }

    function getLinkRewrite() {
        return $this->linkRewrite;
    }

    function setCmsId($cmsId) {
        $this->cmsId = $cmsId;
    }

    function setMetaTitle($metaTitle) {
        $this->metaTitle = $metaTitle;
    }

    function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = $metaKeywords;
    }

    function setContent($content) {
        $this->content = $content;
    }

    function setLinkRewrite($linkRewrite) {
        $this->linkRewrite = $linkRewrite;
    }
}
