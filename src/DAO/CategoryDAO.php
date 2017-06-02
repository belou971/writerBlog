<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 18/05/17
 * Time: 17:07
 */

namespace writerBlog\DAO;

use Doctrine\DBAL\Connection;
use writerBlog\Domain\Category;


class CategoryDAO
{
    /**
     * Database connection
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * Constructor
     *
     * @param \Doctrine\DBAL\Connection The database connection object
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function findAll() {
        $queryBuilder = $this->db->createQueryBuilder();
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
            $categories[$cat_Id] = $this->build($row);
        }
        return $categories;
    }

    private function build($table_row)
    {
        $category = new Category($table_row['cat_id']);
        $category->setSName($table_row['cat_name']);

        return $category;
    }
}