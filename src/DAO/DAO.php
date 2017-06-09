<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 09/06/17
 * Time: 11:53
 */

namespace DAO;

use Doctrine\DBAL\Connection;

abstract class DAO
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    protected function getDB()
    {
        return $this->db;
    }

    protected abstract function buildDomainObject($row);
}