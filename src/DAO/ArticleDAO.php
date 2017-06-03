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
            throw new \Exception("Aucun n'article ne correspond à votre demande ! " . $id);
        }
    }

    /**
     * Return a list of all articles, sorted by date (most recent first).
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
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \forteroche\Domain\Article
     */
    protected function buildDomainObject(array $row) {
        $article = new Article();
        $article->setId($row['art_id']);
        $article->setTitle($row['art_title']);
        $article->setContent($row['art_content']);
        $article->setChapter($row['art_chapter']);
        $article->setAuthor($row['art_author']);
        $article->setDate($row['art_date']);
        return $article;
    }

    public function save(Article $article) {
        $articleData = array(
            'art_title' => $article->getTitle(),
            'art_content' => $article->getContent()
        );

        if($article->getId()) {
            $this->getDb()->update('jf_articles', $articleData, array('art_id' => $article->getId()));
        } else {
            $this->getDb()->insert('jf_articles', $articleData);
            $id = $this->getDb()->lastInsertId();
            $article->setId($id);
        }
    }

    public function delete($id) {
        $this->getDb()->delete('jf_articles', array('art_id' => $id));
    }

}