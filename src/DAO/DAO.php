<?php
namespace forteroche\DAO;

use Doctrine\DBAL\Connection;

abstract class DAO {

    /**
     * Database connection
     *
     * @var Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * Constructor
     *
     * @param Connection $db The database connection object
     */
    public function __construct(Connection $db) {
        $this->db = $db;
    }

    /**
     * Getter : grants access to the database connection object
     *
     * @return Doctrine\DBAL\Connection
     */
    protected function getDb() {
        return $this->db;
    }

    protected abstract function buildDomainObject(array $row);

}
