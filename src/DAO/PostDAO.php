<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 12/05/2017
 * Time: 14:31
 */


namespace writerBlog\DAO;


    use writerBlog\DAO\Dao;
    use Doctrine\DBAL\Connection;
    use Symfony\Component\HttpFoundation\Request;
    use writerBlog\Domain\EPostStatus;
    use writerBlog\Domain\Post;
    use writerBlog\Domain\Admin;

    class PostDAO extends Dao
    {
        /**
         * Return a list of all posts, sorted by publication date (most recent first).
         *
         * @return array A list of all posts.
         */
        public function findAll()
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('p.post_id', 'p.post_title', 'p.post_content',
                'p.post_extract', 'p.post_nb_visit', 'p.post_date_modification',
                'p.post_image', 'p.post_date_creation', 'p.post_id_author', 'p.post_category_id')
                ->from('t_post', 'p')
                ->where('p.post_status = ?')
                ->setParameter(0, EPostStatus::PUBLISHED)
                ->orderBy('p.post_date_creation', 'DESC');

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return null;
            }

            $results = $statement->fetchAll();

            // Convert query result to an array of domain objects
            $posts = array();
            foreach ($results as $row) {
                $post_Id = $row['post_id'];
                $posts[$post_Id] = $this->buildDomainObject($row);
            }
            return $posts;

        }

        /**
         * @param $idx_to_start
         * @param $number_row_max
         * @return array|null
         */
        public function find($idx_to_start ,$number_row_max)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('p.post_id', 'p.post_title', 'p.post_content',
                'p.post_extract', 'p.post_nb_visit', 'p.post_date_modification',
                'p.post_image', 'p.post_date_creation', 'p.post_id_author', 'p.post_category_id')
                ->from('t_post', 'p')
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
                $posts[$post_Id] = $this->buildDomainObject($row);
            }
            return $posts;
        }

        /**
         * @param int $id Takes the post Id in parameter
         * @return null|Post Gets the Post Object corresponding to the given Id
         */
        public function getPost($id)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('p.post_id', 'p.post_title', 'p.post_content',
                'p.post_extract', 'p.post_nb_visit', 'p.post_date_modification',
                'p.post_image', 'p.post_date_creation', 'p.post_id_author', 'p.post_category_id')
                ->from('t_post', 'p')
                ->where('p.post_id = ?')
                ->setParameter(0, $id);

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return null;
            }

            $result = $statement->fetch();

            return $this->buildDomainObject($result);
        }

        /**
         * @param Request $request
         * @param Admin $author
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        public function createPost(Request $request, Admin $author)
        {
            $nb_row = 0;

            $post = new Post();
            $post->setAuthor($author->getId());

            $this->fillData($post, $request);

            return $this->save($post);
        }

        /**
         * @param Request $request
         * @return int
         */
        public function updatePost(Request $request)
        {
            $nb_row = 0;
            $post = new Post($request->get('post_id'));

            $this->fillData($post, $request);

            return $this->update($post);
        }


        public function publishPost($id)
        {
            return $this->updatePostStatus($id, EPostStatus::PUBLISHED);
        }

        public function hidePost($id)
        {
            return $this->updatePostStatus($id, EPostStatus::NOT_PUBLISHED);
        }

        /**
         * @param $id
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        public function deletePost($id) {

            return $this->updatePostStatus($id, EPostStatus::DISABLED);
        }


        private function updatePostStatus($id, $new_status)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->update('t_post', 'p')
                ->set('p.post_status', '?')
                ->where('post_id = ?')
                ->setParameter(0, $new_status)
                ->setParameter(1, $id);

            return $queryBuilder->execute();
        }

        /**
         * @param Post $post
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        private function save(Post $post)
        {
            $postData = array('post_title' => '?',
                              'post_content' => '?',
                              'post_date_creation' => '?',
                              'post_category_id' => '?',
                              'post_extract' => '?',
                              'post_id_author' => '?');

            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->insert('t_post')
                         ->values($postData)
                         ->setParameter(0,$post->getSTitle())
                         ->setParameter(1,$post->getSContent())
                         ->setParameter(2,date("Y-m-d"))
                         ->setParameter(3,$post->getIdCategory())
                         ->setParameter(4,$post->getSExtract())
                         ->setParameter(5,$post->getAuthor());

            return $queryBuilder->execute();
        }


        private function update(Post $post)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->update('t_post', 'p')
                ->set('p.post_title','?')
                ->set('p.post_content', '?')
                ->set('p.post_date_modification', '?')
                ->set('p.post_category_id' , '?')
                ->set('p.post_extract' , '?')
                ->where('p.post_id = ?')
                ->setParameter(0,$post->getSTitle())
                ->setParameter(1,$post->getSContent())
                ->setParameter(2,date("Y-m-d"))
                ->setParameter(3,$post->getIdCategory())
                ->setParameter(4,$post->getSExtract())
                ->setParameter(5,$post->getIId());

            return $queryBuilder->execute();
        }

        /**
         * Creates an Post object based on a DB row.
         *
         * @param array $row The DB row containing Post data.
         * @return Post
         */
        protected function buildDomainObject($row)
        {
            $Post = new Post($row['post_id']);

            $Post->setSTitle($row['post_title']);
            $Post->setSContent($row['post_content']);
            $Post->setSExtract($row['post_extract']);
            $Post->setCreationDate($row['post_date_creation']);
            $Post->setAuthor($row['post_id_author']);
            $Post->setIdCategory($row['post_category_id']);

            return $Post;
        }

        /**
         * @param Post $post
         * @param Request $request
         */
        private function fillData(Post $post, Request $request)
        {
            $post->setSTitle($request->get('title'));
            $post->setSContent($request->get('content'));
            $post->setIdCategory($request->get('category'));
        }


    }
