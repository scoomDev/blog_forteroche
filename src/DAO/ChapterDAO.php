<?php
namespace forteroche\DAO;

use forteroche\Domain\Chapter;

class ChapterDAO extends DAO {

    /**
     * @var forteroche\DAO\ArticleDAO
     */
    private $articleDAO;

    /**
     * @param ArticleDAO $articleDAO
     */
    public function setArtDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }

    /**
     * return a chapter mathing the supplied chapter id 
     *
     * @param integer $chaptId The chapter id   
     * @return \forteroche\Domain\Chapter | throws an exception if no matching article is found
     */
    public function find($chaptId) {
        $sql = "SELECT * FROM jf_chapters WHERE chapt_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($chaptId));

        if($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("Aucun chapitre ne correspond à votre demande ! " . $chaptId);
        }
    }

    /**
     * Returns a list of all chapters
     *
     * @return array
     */
    public function findAll() {
        $sql = "SELECT * FROM jf_chapters ORDER BY chapt_id";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $chapters = array();
        foreach ($result as $row) {
            $chapterId = $row['chapt_id'];
            $chapters[$chapterId] = $this->buildDomainObject($row);
        }
        return $chapters;
    }

    /**
     * Saves chapter into the database
     *
     * @param Chapter $chapter
     */
    public function save(Chapter $chapter) {
        $chapterData = array(
            'chapt_number' => $chapter->getNumber(),
            'chapt_title' => $chapter->getTitle()
        );

        if($chapter->getId()) {
            $this->getDb()->update('js_chapters', $chapterData, array('chapt_id' => $chapter->getId()));
        } else {
            $this->getDb()->insert('jf_chapters', $chapterData);
            $id = $this->getDb()->lastInsertId();
            $chapter->setId($id);
        }
    }

    /**
     * Removes an existing chapter from the database
     *
     * @param [type] $id
     */
    public function delete($id) {
        $this->getDb()->delete('jf_chapters', array('chapt_id' => $id));
    }

    /**
     * Creates an Chapter object based on a DB row.
     *
     * @param array $row The DB row containing Chapter data.
     * @return \forteroche\Domain\Chapter
     */
    protected function buildDomainObject(array $row) {
        $chapter = new Chapter();
        $chapter->setId($row['chapt_id']);
        $chapter->setTitle($row['chapt_title']);
        $chapter->setNumber($row['chapt_number']);

        $artChapter = $row['chapt_number'];
        $article = $this->articleDAO->findByChapter($artChapter);
        $chapter->setArticle($article);

        return $chapter;
    }

}
