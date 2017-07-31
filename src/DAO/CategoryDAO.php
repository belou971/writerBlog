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

    public function existCategory($categoryName)
    {
        $queryBuilder = $this->getDB()->createQueryBuilder();
        $queryBuilder->select('cat_name')
            ->from('t_categorie', 'c')
            ->where('cat_name = ?')
            ->setParameter(0, $categoryName);

        $statement = $queryBuilder->execute();
        if (is_int($statement)) {
            return null;
        }

        $statement->fetchAll();

        return ($statement->rowCount() != 0);
        /*if($count < 0) {
            $message = "$categoryName existe déjà";
        }

        return array('cat_name' => $categoryName,
                     'cat_counter' => $count,
                     'message' => $message);*/

    }

    public function createCategory($categoryName)
    {
        $message = "";
        if($this->existCategory($categoryName)) {
            $message = "$categoryName existe déjà";

            return array('cat_name' => $categoryName,
                         'cat_found' => true,
                         'message' => $message);
        }

        $new_category = new Category();
        $new_category->setSName($categoryName);

        $statement = $this->save($new_category);
        if(is_int($statement)) {
            return null;
        }

        // Insertion of the new category succeeded
        //if($statement->rowCount() > 0)
        return null;
    }

    protected function buildDomainObject($table_row)
    {
        $category = new Category($table_row['cat_id']);
        $category->setSName($table_row['cat_name']);

        return $category;
    }

    private function save(Category $category)
    {
        $data = array('cat_name' => '?');

        $queryBuilder = $this->getDB()->createQueryBuilder();
        $queryBuilder->insert('t_category')
            ->values($data)
            ->setParameter(0,$category->getSName());

        return $queryBuilder->execute();
    }
}