<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 24/05/2017
 * Time: 15:38
 */

namespace writerBlog\DAO;

use writerBlog\DAO\Dao;
use Doctrine\DBAL\Connection;
use writerBlog\Domain\Blog;

class BlogDAO extends Dao
{

    public function find() {
        $queryBuilder = $this->getDB()->createQueryBuilder();

        $queryBuilder->select('blo_id', 'blo_title')
                     ->from('t_blog');

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

        Blog::getInstance()->setIId($row['blo_id']);
        Blog::getInstance()->setSTitle($row['blo_title']);

        return Blog::getInstance();
    }
}