<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 18/05/17
 * Time: 17:07
 */

namespace writerBlog\DAO;

use writerBlog\DAO\Dao;
use Doctrine\DBAL\Connection;
use writerBlog\Domain\Category;


class CategoryDAO extends Dao
{

    public function findAll() {
        $queryBuilder = $this->getDB()->createQueryBuilder();
        $queryBuilder->select('cat_id', 'cat_name')
            ->from('t_categorie', 'c');

        $statement = $queryBuilder->execute();
        if (is_int($statement)) {
            return null;
        }

        $results = $statement->fetchAll();

        // Convert query result to an array of domain objects
        $categories = array();
        foreach ($results as $row) {
            $cat_Id = $row['cat_id'];
            $categories[$cat_Id] = $this->buildDomainObject($row);
        }
        return $categories;
    }

    protected function buildDomainObject($table_row)
    {
        $category = new Category($table_row['cat_id']);
        $category->setSName($table_row['cat_name']);

        return $category;
    }
}