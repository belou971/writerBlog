<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 12/05/2017
 * Time: 14:31
 */


namespace writerBlog\DAO;


    use Doctrine\DBAL\Connection;
    use writerBlog\Domain\EPostStatus;
    use writerBlog\Domain\Post;

    class PostDAO
    {
        /**
         * Database connection
         *
         * @var \Doctrine\DBAL\Connection
         */
        private $db;
        private $numberOfPost;

        /**
         * Constructor
         *
         * @param \Doctrine\DBAL\Connection The database connection object
         */
        public function __construct(Connection $db)
        {
            $this->db = $db;
            $this->numberOfPost = 0;
        }

        /**
         * Return a list of all posts, sorted by publication date (most recent first).
         *
         * @return array A list of all posts.
         */
        public function findAll()
        {

            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('p.post_id', 'p.post_title', 'p.post_content',
                'p.post_extract', 'p.post_nb_visit', 'p.post_date_modification',
                'p.post_image', 'p.post_date_creation', 'a.adm_web_name', 'cat.cat_name')
                ->from('t_post', 'p')
                ->innerJoin('p', 't_admin', 'a', 'p.post_id_author = a.adm_id')
                ->innerJoin('p', 't_categorie', 'cat', 'p.post_category_id = cat.cat_id')
                ->where('p.post_status = ?')
                ->setParameter(0, EPostStatus::PUBLISHED)
                ->orderBy('p.post_date_creation', 'DESC');

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return null;
            }

            $results = $statement->fetchAll();
            $this->numberOfPost = $statement->rowCount();

            // Convert query result to an array of domain objects
            $posts = array();
            foreach ($results as $row) {
                $post_Id = $row['post_id'];
                $posts[$post_Id] = $this->buildPost($row);
            }
            return $posts;

        }

        /**
         * @param $number_row_max
         */
        public function find($idx_to_start ,$number_row_max)
        {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('p.post_id', 'p.post_title', 'p.post_content',
                'p.post_extract', 'p.post_nb_visit', 'p.post_date_modification',
                'p.post_image', 'p.post_date_creation', 'a.adm_web_name', 'cat.cat_name')
                ->from('t_post', 'p')
                ->innerJoin('p', 't_admin', 'a', 'p.post_id_author = a.adm_id')
                ->innerJoin('p', 't_categorie', 'cat', 'p.post_category_id = cat.cat_id')
                ->where('p.post_status = ?')
                ->setParameter(0, EPostStatus::PUBLISHED)
                ->orderBy('p.post_date_creation', 'DESC')
                ->setFirstResult($idx_to_start)
                ->setMaxResults($number_row_max);

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return null;
            }

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
         * @return int
         */
        public function getNumberOfPost()
        {
            return $this->numberOfPost;
        }

        /**
         * Creates an Post object based on a DB row.
         *
         * @param array $row The DB row containing Post data.
         * @return Post
         */
        private function buildPost(array $row)
        {
            $Post = new Post();

            $Post->setSTitle($row['post_title']);
            $Post->setSContent($row['post_content']);
            $Post->setSExtract($row['post_extract']);
            $Post->setSAuthor($row['adm_web_name']);
            $Post->setCreationDate($row['post_date_creation']);
            $Post->setSCategory($row['cat_name']);

            return $Post;
        }

    }
