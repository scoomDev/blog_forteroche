<?php

namespace forteroche\DAO;

use forteroche\Domain\Article;

class ArticleDAO extends DAO {

    /**
     * return an article mathing the supplied id
     *
     * @param integer $id The article id
     * @return \forteroche\Domain\Article | throws an exception if no matching article is found
     */
    public function find($id) {
        $sql = "SELECT * FROM jf_articles WHERE art_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("Aucun article ne correspond à votre demande ! " . $id);
        }
    }

    /**
     * returns an article mathing the supplied chapter
     *
     * @param integer $chapter The article chapter
     * @return \forteroche\Domain\Article | throws an exception if no matching article is found
     */
    public function findByChapter($chapter) {
        $sql = "SELECT * FROM jf_articles WHERE chapt_number=?";
        $result = $this->getDb()->fetchAll($sql, array($chapter));

        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

    /**
     * Returns a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "SELECT * FROM jf_articles ORDER BY art_id DESC";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

    /**
     * Uploads an image for the article
     *
     * @param Article $article
     */
    public function upImg(Article $article) {
        $image = $article->getImage();
        $url = '../web/img';
        $fileName = md5(uniqid()).'.'.$image->guessExtension();
        $image->move($url, $fileName);
        $article->setImage($fileName);
    }

    /**
     * Saves an article intro the database
     *
     * @param Article $article
     */
    public function save(Article $article) {
        $articleData = array(
            'art_title' => $article->getTitle(),
            'art_content' => $article->getContent(),
            'art_image' => $article->getImage(),
            'chapt_number' => $article->getChapter()
        );

        if($article->getImage() === null) {
            $articleData['art_image'] = 'default.jpg';
        }

        $this->getDb()->insert('jf_articles', $articleData);
        $id = $this->getDb()->lastInsertId();
        $article->setId($id);
    }

    /**
     * Updates an existing article from the database
     *
     * @param Article $article
     */
    public function update(Article $article) {
        $articleData = array(
            'art_title' => $article->getTitle(),
            'art_content' => $article->getContent(),
            'chapt_number' => $article->getChapter()
        );

        if($article->getImage()) {
            $articleData['art_image'] = $article->getImage();
        }
        $this->getDb()->update('jf_articles', $articleData, array('art_id' => $article->getId()));
    }

    /**
     * Removes an article from the database
     *
     * @param integer $id The article id
     */
    public function delete($id) {
        $this->getDb()->delete('jf_articles', array('art_id' => $id));
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \forteroche\Domain\Article
     */
    protected function buildDomainObject(array $row) {
        $article = new Article();
        $article->setId($row['art_id']);
        $article->setImage($row['art_image']);
        $article->setTitle($row['art_title']);
        $article->setContent($row['art_content']);
        $article->setChapter($row['chapt_number']);
        $article->setAuthor($row['art_author']);
        $article->setDate($row['art_date']);

        return $article;
    }

}
