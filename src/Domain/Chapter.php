<?php
namespace forteroche\Domain;

class Chapter {

    /**
     * Chapter id
     *
     * @var integer
     */
    private $id;

    /**
     * Chapter title
     *
     * @var string
     */
    private $title;

    /**
     * Chapter number
     *
     * @var integer
     */
    private $number;

    /**
     * Associated article
     *
     * @var forteroche\Domain\Article object array
     */
    private $article;

    // GETTERS & SETTERS
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getNumber() {
        return $this->number;
    }

    public function setNumber($number) {
        $this->number = $number;
        return $this;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setArticle($article) {
        $this->article = $article;
        return $this;
    }
}