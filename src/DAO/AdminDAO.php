<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 24/05/2017
 * Time: 15:38
 */

namespace writerBlog\DAO;

use DAO\DAO;
use Doctrine\DBAL\Connection;
use writerBlog\Domain\Admin;
use writerBlog\Domain\Blog;

class AdminDAO extends DAO
{
    public function get()
    {
        $queryBuilder = $this->getDB()->createQueryBuilder();

        $queryBuilder->select('adm_id', 'adm_login', 'adm_web_name')
                     ->from('t_admin');

        $statement = $queryBuilder->execute();
        if(is_int($statement)) {
            return NULL;
        }

        $result = $statement->fetch();
        return $this->buildDomainObject($result);
    }

    protected function buildDomainObject($row)
    {
        if(is_null($row)|| !isset($row)) { return NULL; }

        $admin = new Admin($row['adm_id']);
        $admin->setUsername($row['adm_login']);
        $admin->setWebName($row['adm_web_name']);

        return $admin;
    }
}