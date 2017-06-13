<?php
namespace forteroche\Domain;

class Comment {

    /**
     * Comment id.
     *
     * @var integer
     */
    private $id;

    /**
     * Comment author.
     *
     * @var string
     */
    private $author;

    /**
     * Comment content.
     *
     * @var integer
     */
    private $content;

    /**
     * Associated comment
     *
     * @var integer
     */
    private $parentId;


    /**
     * Comment date
     *
     * @var string
     */
    private $date;

    /**
     * Associated comment
     *
     * @var forteroche\Domain\Comment
     */
    private $children;

    /**
     * Associated article
     *
     * @var forteroche\Domain\Article
     */
    private $article;

    private $depth;

    // GETTERS & SETTERS
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function setParentId($parentId) {
        $this->parentId = $parentId;
        return $this;
    }


    public function getChildren() {
        return $this->children;
    }

    public function setChildren(array $children) {
        $this->children = $children;
        return $this;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setArticle($article) {
        $this->article = $article;
        return $this;
    }

    public function getDepth() {
        return $this->depth;
    }

    public function setDepth($depth) {
        $this->depth = $depth;
        return $this;
    }
}