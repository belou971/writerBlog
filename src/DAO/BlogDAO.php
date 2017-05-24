<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 24/05/2017
 * Time: 15:38
 */

namespace writerBlog\DAO;

use Doctrine\DBAL\Connection;
use writerBlog\Domain\Blog;

class BlogDAO
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function find() {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('blo_id', 'blo_title')
                     ->from('t_blog');

        $statement = $queryBuilder->execute();
        if(is_int($statement)) {
            return NULL;
        }

        $result = $statement->fetch();
        return $this->buildBlogInfo($result);


    }

    private function buildBlogInfo($row)
    {
        if(is_null($row)|| !isset($row)) { return NULL; }

        Blog::getInstance()->setIId($row['blo_id']);
        Blog::getInstance()->setSTitle($row['blo_title']);

        return Blog::getInstance();
    }
}