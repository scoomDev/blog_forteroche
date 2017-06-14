<?php
namespace forteroche\DAO;

use forteroche\Domain\Comment;

class CommentDAO extends DAO {

    /**
     *
     * @var forteroche\DAO\ArticleDAO
     */
    private $articleDAO;

    public function setArticleDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }

    public function findAllByArticle($articleId) {
        $article = $this->articleDAO->find($articleId);
        $sql = "SELECT * FROM jf_comments WHERE art_id=?";
        $result = $this->getDb()->fetchAll($sql, array($articleId));

        $comments = [];
        foreach($result as $row) {
            $comId = $row['com_id'];
            $comment = $this->buildDomainObject($row);
            $comment->setArticle($article);
            $comments[$comId] = $comment;
        }

        $parentComments = array_filter($comments, function($comment) {
            return $comment->getParentId() === NULL;
        });

        foreach ($parentComments as $parentComment) {
            $this->setChildComments($comments, $parentComment);
        }
        return $parentComments;
    }

    public function setChildComments($allComments, Comment $comment) {
        $childComments = array_filter($allComments, function($childComment) use($comment) {
            return $childComment->getParentId() === $comment->getId();
        });

        $comment->setChildren($childComments);
        foreach ($childComments as $childComment) {
            $this->setChildComments($allComments, $childComment);
        }
    }

    /**
     * Find a comment
     *
     * @param integer $id
     * @return forteroche\Domain\Comment
     */
    public function find($id) {
        $sql = "SELECT * FROM jf_comments WHERE com_id = ?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new \Excepetion('Aucun commentaires ne correspond à l\'ID ' . $id);
        }
    }

    /**
     * Return a list of all comments, sorted by date (most recent first).
     *
     * @return array A list of all comments.
     */
    public function findAll() {
        $sql = "SELECT * FROM jf_comments ORDER BY com_id DESC";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $commentId = $row['com_id'];
            $comments[$commentId] = $this->buildDomainObject($row);
        }
        return $comments;
    }

    public function buildDomainObject(array $row) {
        $comment = new Comment();
        $comment->setId($row['com_id']);
        $comment->setAuthor($row['com_author']);
        $comment->setContent($row['com_content']);
        $comment->setParentId($row['com_parent']);
        $comment->setDate($row['com_date']);
        $comment->setArticle($row['art_id']);
        $comment->setDepth($row['com_depth']);
        $comment->setReporting($row['com_reporting']);

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
            'com_content' => $comment->getContent(),
            'com_date' => $comment->getDate(),
            'com_parent' => $comment->getParentId(),
            'com_depth' => $comment->getDepth(),
            'com_reporting' => $comment->getReporting(),
        );
        
        if($comment->getId()) {
            $this->getDb()->update('jf_comments', $commentData, array('com_id' => $comment->getId()));
        } else {
            $this->getDb()->insert('jf_comments', $commentData);
            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

    public function deleteAllByArticle($articleId) {
        $this->getDb()->delete('jf_comments', array('art_id' => $articleId));
    }

    public function delete($id) {
        $this->getDb()->delete('jf_comments', array('com_id' => $id));
    }

    public function report($comment) {
        $commentData = [
            'com_reporting' => 1
        ];

        $this->getDb()->update('jf_comments', $commentData, array('com_id' => $comment->getId()));
    }

    public function countComments($id){
        return count($this->findAllByArticle($id));
    }
}