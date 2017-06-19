<?php
namespace forteroche\DAO;

use forteroche\Domain\Header;

class HeaderDAO extends DAO {

    public function find() {
        $sql = 'SELECT * FROM jf_header';
        $row = $this->getDb()->fetchAssoc($sql);
        return $this->buildDomainObject($row);
    }

    public function save(Header $header) {
        $headerData = array(
            'head_img_1' => $header->getImage1(),
            'head_img_2' => $header->getImage2(),
            'head_title' => $header->getTitle(),
            'head_subtitle' => $header->getSubtitle()
        );

        if($header->getId()) {
            $this->getDb()->update('jf_header', $headerData, array('head_id' => $header->getId()));
        } else {
            $this->getDb()->insert('jf_header', $headerData);
            $id = $this->getDb()->lastInsertId();
            $header->setId($id);
        }
    }

    /**
     * Creates a Header object based on a DB row.
     *
     * @param array $row The DB row containing Header data.
     * @return \forteroche\Domain\Header
     */
    protected function buildDomainObject(array $row) {
        $header = new Header();
        $header->setId($row['head_id']);
        $header->setImage1($row['head_img_1']);
        $header->setImage2($row['head_img_2']);
        $header->setTitle($row['head_title']);
        $header->setSubtitle($row['head_subtitle']);
        return $header;
    }

    public function upImg1($header) {
        $image1 = $header->getImage1();
        $url = '../web/img';
        $fileName = md5(uniqid()).'.'.$image1->guessExtension();
        $image1->move($url, $fileName);
        $header->setImage1($fileName);
    }

    public function upImg2($header) {
        $image2 = $header->getImage2();
        $url = '../web/img';
        $fileName = md5(uniqid()).'.'.$image2->guessExtension();
        $image2->move($url, $fileName);
        $header->setImage2($fileName);
    }

}