<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 12/05/2017
 * Time: 14:31
 */


namespace writerBlog\DAO;


use Doctrine\DBAL\Connection;
use writerBlog\Domain\Post;

class PostDAO
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
    public function __construct(Connection $db) {
        $this->db = $db;
    }

    /**
     * Return a list of all posts, sorted by publication date (most recent first).
     *
     * @return array A list of all posts.
     */
    public function findAll() {

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('p.post_id', 'p.post_title', 'p.post_content',
                               'p.post_extract', 'p.post_nb_visit', 'p.post_date_modification',
                              'p.post_image', 'p.post_date_creation', 'a.adm_web_name' )
                    ->from('t_post p')
                    ->innerJoin('p', 't_admin', 'a', 'p.post_id_author = a.admin_id')
                    ->where('p.post_status = PUBLISHED')
                    ->orderBy('p.post_date_creation', 'DESC');

        $statement = $queryBuilder->execute();
        if(is_int($statement)) { return null; }

        $results = $statement->fetchAll();

        // Convert query result to an array of domain objects
        $posts = array();
        foreach ($results as $row) {
            $post_Id = $row['post_id'];
            $posts[$post_Id] = $this->buildPost($row);
        }
        return $posts;

    }

    /**
     * Creates an Post object based on a DB row.
     *
     * @param array $row The DB row containing Post data.
     * @return Post
     */
    private function buildPost(array $row) {
        $Post = new Post();
        $Post->setSTitle($row['p.post_title']);
        $Post->setSContent($row['p.post_content']);
        $Post->setSExtract($row['p.post_extract']);
        $Post->setSAuthor($row['a.a.adm_web_name']);
        return $Post;
    }
}