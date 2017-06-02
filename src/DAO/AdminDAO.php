<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 24/05/2017
 * Time: 15:38
 */

namespace writerBlog\DAO;

use Doctrine\DBAL\Connection;
use writerBlog\Domain\Admin;
use writerBlog\Domain\Blog;

class AdminDAO
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function get() {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('adm_id', 'adm_login', 'adm_web_name')
                     ->from('t_admin');

        $statement = $queryBuilder->execute();
        if(is_int($statement)) {
            return NULL;
        }

        $result = $statement->fetch();
        return $this->build($result);


    }

    private function build($row)
    {
        if(is_null($row)|| !isset($row)) { return NULL; }

        $admin = new Admin($row['adm_id']);
        $admin->setLogin($row['adm_login']);
        $admin->setWebName($row['adm_web_name']);

        return $admin;
    }
}