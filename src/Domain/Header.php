<?php
namespace forteroche\Domain;

class Header {

    private $id;
    private $image1;
    private $image2;
    private $title;
    private $subtitle;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getImage1() {
        return $this->image1;
    }

    public function setImage1($image1) {
        $this->image1 = $image1;
        return $this;
    }

    public function getImage2() {
        return $this->image2;
    }

    public function setImage2($image2) {
        $this->image2 = $image2;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getSubtitle() {
        return $this->subtitle;
    }

    public function setSubtitle($subtitle) {
        $this->subtitle = $subtitle;
        return $this;
    }


}
