<?php
namespace forteroche\DAO;

use forteroche\Domain\Comment;

class CommentDAO extends DAO {

    private $articleDAO;

    public function setArticleDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }

    public function findAllByArticle($articleId) {
        $article = $this->articleDAO->find($articleId);
        $sql = "SELECT * FROM jf_comments WHERE art_id=?";
        $result = $this->getDb()->fetchAll($sql, array($articleId));

        $comments = array();
        foreach($result as $row) {
            $comId = $row['com_id'];
            $comment = $this->buildDomainObject($row);
            $comment->setArticle($article);
            $comments[$comId] = $comment;
        }
        return $comments;
    }

    public function buildDomainObject(array $row) {
        $comment = new Comment();
        $comment->setId($row['com_id']);
        $comment->setAuthor($row['com_author']);
        $comment->setContent($row['com_content']);
        $comment->setParentId($row['com_parent']);
        $comment->setArticle($row['art_id']);

        if(array_key_exists('art_id', $row)) {
            $articleId = $row['art_id'];
            $article = $this->articleDAO->find($articleId);
            $comment->setArticle($article);
        }

        return $comment;
    }

    public function save(Comment $comment) {
        $commentData = array(
            'art_id' => $comment->getArticle()->getId(),
            'com_author' => $comment->getAuthor(),
            'com_content' => $comment->getContent()
        );

        if($comment->getId()) {
            $this->getDb()->update('jf_comments', $commentData, array('com_id' => $comment->getId()));
        } else {
            $this->getDb()->insert('jf_comments', $commentData);
            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

}