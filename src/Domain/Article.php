<?php
namespace forteroche\Domain;

class Article {

    /**
     * Article id
     *
     * @var integer
     */
    private $id;

    /**
     * Article title
     *
     * @var string
     */
    private $title;

    /**
     * Article content
     *
     * @var string
     */
    private $content;

    /**
     * Article chapter
     *
     * @var integer
     */
    private $chapter;

    /**
     * Article author
     *
     * @var string
     */
    private $author;

    /**
     * Article date time
     *
     * @var string
     */
    private $date;

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

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getChapter() {
        return $this->chapter;
    }

    public function setChapter($chapter) {
        $this->chapter = $chapter;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

}