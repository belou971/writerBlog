<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 03/06/2017
 * Time: 00:56
 */


namespace writerBlog\DAO;

    use writerBlog\Domain\CommentGroup;
    use writerBlog\Domain\LazyCaptcha;
    use writerBlog\DAO\Dao;
    use Doctrine\DBAL\Connection;
    use Symfony\Component\HttpFoundation\Request;
    use writerBlog\Domain\ECommentRead;
    use writerBlog\Domain\ECommentStatus;
    use writerBlog\Domain\Comment;
    use writerBlog\Domain\EPostStatus;

    class CommentDAO extends Dao
    {
        public function findReportedComments()
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('c.com_post_id', 'p.post_title', 'c.com_id', 'c.com_parent_id', 'c.com_pseudo', 'c.com_email','c.com_date_creation', 'c.com_message', 'c.com_status', 'c.com_read')
                ->addSelect('cc.com_parent_id AS ppid')
                ->from('t_comment', 'c')
                ->innerJoin('c', 't_post', 'p', 'c.com_post_id = p.post_id')
                ->leftJoin('c', 't_comment', 'cc', 'c.com_parent_id = cc.com_id')
                ->where('c.com_status = ?')
                ->andwhere('p.post_status = ?')
                ->orderBy('c.com_post_id', 'ASC')
                ->addOrderBy('c.com_date_creation', 'ASC')
                ->setParameter(0, ECommentStatus::MALICIOUS)
                ->setParameter(1, EPostStatus::PUBLISHED);

            $statement = $queryBuilder->execute();
            if(is_int($statement)) {
                return null;
            }

            $resultsRow = $statement->fetchAll();
            if(!is_array($resultsRow)) {
                return array();
            }

            return $this->extractData($resultsRow);
        }

        public function findNewComments($username)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('c.com_post_id', 'p.post_title', 'c.com_id', 'c.com_parent_id', 'c.com_pseudo', 'c.com_email','c.com_date_creation', 'c.com_message', 'c.com_status', 'c.com_read')
                ->addSelect('cc.com_parent_id AS ppid')
                ->from('t_comment', 'c')
                ->innerJoin('c', 't_post', 'p', 'c.com_post_id = p.post_id')
                ->leftJoin('c', 't_comment', 'cc', 'c.com_parent_id = cc.com_id')
                ->where('p.post_status = ?')
                ->andwhere('c.com_read = ?')
                ->andWhere('c.com_status = ?')
                ->andWhere('c.com_pseudo <> ?')
                ->orderBy('c.com_post_id', 'ASC')
                ->addOrderBy('c.com_date_creation', 'ASC')
                ->setParameter(0, EPostStatus::PUBLISHED)
                ->setParameter(1, ECommentRead::NOT_READ)
                ->setParameter(2, ECommentStatus::PUBLISHED)
                ->setParameter(3, $username);

            $statement = $queryBuilder->execute();
            if(is_int($statement)) {
                return null;
            }

            $resultsRow = $statement->fetchAll();
            if(!is_array($resultsRow)) {
                return array();
            }

            return $this->extractData($resultsRow);
        }

        public function findOldReadComments($username)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('c.com_post_id', 'p.post_title', 'c.com_id', 'c.com_parent_id', 'c.com_pseudo', 'c.com_email','c.com_date_creation', 'c.com_message', 'c.com_status', 'c.com_read')
                ->addSelect('cc.com_parent_id AS ppid')
                ->from('t_comment', 'c')
                ->innerJoin('c', 't_post', 'p', 'c.com_post_id = p.post_id')
                ->leftJoin('c', 't_comment', 'cc', 'c.com_parent_id = cc.com_id')
                ->where('p.post_status = ?')
                ->andwhere('c.com_read = ?')
                ->andWhere('c.com_status = ?')
                ->andWhere('c.com_pseudo <> ?')
                ->orderBy('c.com_post_id', 'ASC')
                ->addOrderBy('c.com_date_creation', 'ASC')
                ->setParameter(0, EPostStatus::PUBLISHED)
                ->setParameter(1, ECommentRead::READ)
                ->setParameter(2, ECommentStatus::PUBLISHED)
                ->setParameter(3, $username);

            $statement = $queryBuilder->execute();
            if(is_int($statement)) {
                return null;
            }

            $resultsRow = $statement->fetchAll();
            if(!is_array($resultsRow)) {
                return array();
            }

            return $this->extractData($resultsRow);
        }

        public function findCommentsByUsername($username)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();

            $queryBuilder->select('c.com_post_id', 'p.post_title', 'c.com_id', 'c.com_parent_id', 'c.com_pseudo', 'c.com_email','c.com_date_creation', 'c.com_message', 'c.com_status', 'c.com_read')
                ->addSelect('cc.com_parent_id AS ppid', 'cc.com_pseudo AS recipient')
                ->from('t_comment', 'c')
                ->innerJoin('c', 't_post', 'p', 'c.com_post_id = p.post_id')
                ->leftJoin('c', 't_comment', 'cc', 'c.com_parent_id = cc.com_id')
                ->where('p.post_status = ?')
                ->andWhere('cc.com_status = ?')
                ->andWhere('c.com_pseudo = ? and c.com_status = ?')
                ->orderBy('c.com_post_id', 'ASC')
                ->addOrderBy('c.com_date_creation', 'ASC')
                ->setParameter(0, EPostStatus::PUBLISHED)
                ->setParameter(1, ECommentStatus::PUBLISHED)
                ->setParameter(2, $username)
                ->setParameter(3, ECommentStatus::PUBLISHED);

            $statement = $queryBuilder->execute();
            if(is_int($statement)) {
                return null;
            }

            $resultsRow = $statement->fetchAll();
            if(!is_array($resultsRow)) {
                return array();
            }

            return $this->extractData($resultsRow);
        }

        private function extractData($sqlResults)
        {
            $resultData = array();

            $current_post_id = -1;
            $position = -1;

            foreach($sqlResults as $row) {
                if($current_post_id != $row['com_post_id']) {
                    $comment_group = $this->buildCommentGroup($row);
                    $current_post_id = $row['com_post_id'];
                    $position = $position + 1;
                    $resultData[$position] = $comment_group;
                }
                else {
                    $data['comment'] = $this->buildDomainObject($row);
                    $data['ppid']    = $row['ppid'];
                    if(array_key_exists('recipient', $row)) {
                        $data['recipient'] =$row['recipient'];
                    }
                    $resultData[$position]->appendInCommentList($data);
                }
            }

            return $resultData;
        }


        /**
         * @param int $id Takes the comment Id in parameter
         * @return null|Comment Gets the Comment Object corresponding to the given Id
         */
        public function getComment($id)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('*')
                ->from('t_comment')
                ->where('com_id = ?')
                ->setParameter(0, $id);

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return null;
            }

            $result = $statement->fetch();

            return $this->buildDomainObject($result);
        }

        /**
         * Return a list of comments which belong to a given post.
         *
         * @parameter int $post_id the post id
         *
         * @return array A list of comments.
         */
        public function getCommentsByPostId($post_id)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('*')
                ->from('t_comment')
                ->where('com_post_id = ?')
                ->andWhere('com_status = ? OR com_status = ?')
                ->setParameter(0, $post_id)
                ->setParameter(1, ECommentStatus::PUBLISHED)
                ->setParameter(2, ECommentStatus::MALICIOUS)
                ->orderBy('com_date_creation', 'DESC');

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return null;
            }

            $results = $statement->fetchAll();

            $comments = $this->buildComments($results);
            $commentsTree = $this->buildTree($comments);

            return $commentsTree;
        }

        /**
         * @return int number of comment not read
         */
        public function getNumberOfNewComments($username)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('COUNT(com_id) as nbNewComments')
                ->from('t_comment', 'c')
                ->innerJoin('c', 't_post', 'p', 'c.com_post_id = p.post_id')
                ->where('c.com_read = ? and c.com_status = ? and c.com_pseudo != ?')
                ->andWhere('p.post_status = ?')
                ->setParameter(0, ECommentRead::NOT_READ)
                ->setParameter(1, ECommentStatus::PUBLISHED)
                ->setParameter(2, $username)
                ->setParameter(3, EPostStatus::PUBLISHED)
                ->orderBy('com_date_creation', 'ASC');

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return 0;
            }

            $result = $statement->fetch();
            $test= $result['nbNewComments'];
            return $test;
        }

        public function getNumberOfReportedComments()
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('COUNT(com_id) as nbReportedComments')
                ->from('t_comment', 'c')
                ->innerJoin('c', 't_post', 'p', 'c.com_post_id = p.post_id')
                ->where('c.com_status = ?')
                ->andWhere('p.post_status = ?')
                ->setParameter(0, ECommentStatus::MALICIOUS)
                ->setParameter(1, EPostStatus::PUBLISHED);

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return 0;
            }

            $result = $statement->fetch();
            $test= $result['nbReportedComments'];
            return $test;
        }

        /**
         * @param array $table with result rows of SQL request
         * @return array of Comments objects
         */
        private function buildComments(array $table)
        {
            $comments = array();
            foreach ($table as $row) {
                $com_id = $row['com_id'];
                $comments[$com_id] = $this->buildDomainObject($row);
            }
            return $comments;
        }

        /**
         * @param array $comments Comments objects
         * @return array representation tree of comments to display on blog
         */
        private function buildTree(array $comments)
        {
            $tree = array();

            if(count($comments) === 0) return $tree;

            foreach ($comments as $comment) {
                //level 0 = Search roots comments, ie, those without parent id
                //level 1 = Search comments children of each root comment
                //level 2 = Search recursively children of each child found at level 1
                if($comment->getParentId() === Comment::UNDEFINED) {
                    $level = 0;
                    $tree[] = array("comment"=>$comment, "level"=>$level);
                    $this->buildChildren_recursive($comment, $comments, $tree, ++$level);
                }
            }

            return $tree;
        }

        /**
         * @param Comment $parent the comment object for which we search the children comments
         * @param array $comments_lst The array which contains all the comments of the post
         * @param array $tree representation tree of comments to display on blog
         * @param int $level level of the children in the tree
         */
        private function buildChildren_recursive(Comment $parent, array $comments_lst, array &$tree, $level)
        {
            if($level >= 3) return;

            foreach($comments_lst as $comment) {
                if($comment->getParentId() !== $parent->getId()) {
                    continue;
                }

                $tree[] = array("comment"=>$comment, "level"=>$level);

                $childLevel = $level + 1;
                $this->buildChildren_recursive($comment, $comments_lst, $tree, $childLevel);
            }
        }


        /**
         * @param Request $request
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        public function createComment(Request $request)
        {
            $comment = new Comment($request->get('post_id'));

            $this->fillData($comment, $request);

            return $this->save($comment);
        }

        public function getCommentInfos()
        {
            $resultsData = array();

            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->select('c.com_post_id', 'COUNT(c.com_post_id) as nbComment')
                         ->from('t_comment', 'c')
                         ->innerJoin('c', 't_post', 'p', 'c.com_post_id = p.post_id')
                         ->where('c.com_status <> ? AND c.com_status <> ?')
                         ->andWhere('p.post_status = ?')
                         ->groupBy('c.com_post_id')
                         -> setParameter(0, ECommentStatus::DISABLED)
                         ->setParameter(1, ECommentStatus::NOT_PUBLISHED)
                         ->setParameter(2, ECommentStatus::PUBLISHED);

            $statement = $queryBuilder->execute();
            if (is_int($statement)) {
                return null;
            }

            $results = $statement->fetchAll();

            if(!is_array($results)) {
                return $resultsData;
            }

            foreach ($results as $row) {
                $resultsData[ $row['com_post_id'] ] = $row['nbComment'];
            }

            return $resultsData;
        }


        public function publishComment($id)
        {
            $data['id'] = "";

            if($this->updateCommentStatus($id, ECommentStatus::PUBLISHED) > 0)
            {
                $data['id'] = $id;
            }

            return $data;
        }

        public function hideComment($id)
        {
            return $this->updateCommentStatus($id, ECommentStatus::NOT_PUBLISHED);
        }

        public function getForm()
        {
            session_start();

            $captcha = new LazyCaptcha();

            $_SESSION['rand_nbr'] = $captcha->getValidationCode();

            return $captcha->getImage();
        }

        /**
         * @param $id
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        public function deleteComment($id)
        {
            $data['id'] = "";

            if($this->updateCommentStatus($id, EPostStatus::DISABLED) > 0)
            {
                $data['id'] = $id;
            }

            return $data;
        }

        public function alertComment(Request $requestForm)
        {
            return $this->updateCommentStatus($requestForm->get('id'), ECommentStatus::MALICIOUS);

        }

        public function markAsRead($id)
        {
            $data['id'] = "";

            if($this->updateCommentRead($id, ECommentRead::READ) > 0)
            {
                $data['id'] = $id;
            }

            return $data;
        }

        public function markAsUnread($id)
        {
            $data['id'] = "";

            if($this->updateCommentRead($id, ECommentRead::NOT_READ) > 0)
            {
                $data['id'] = $id;
            }

            return $data;
        }

        private function updateCommentStatus($id, $new_status)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->update('t_comment')
                ->set('com_status', '?')
                ->where('com_id = ?')
                ->setParameter(0, $new_status)
                ->setParameter(1, $id);

            return $queryBuilder->execute();
        }

        /**
         * Update the reading stat of a given comment
         * @param $id
         * @param $read_type
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        private function updateCommentRead($id, $read_type)
        {
            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->update('t_comment')
                ->set('com_read', '?')
                ->where('com_id = ?')
                ->setParameter(0, $read_type)
                ->setParameter(1, $id);

            return $queryBuilder->execute();
        }

        /**
         * @param Comment $comment
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        private function save(Comment $comment)
        {
            $responseData = array('nb_row' => 0,
                'post_id' => $comment->getPostId(),
                'comment_id' => -1,
                'parent' => "-1");

            $commentData = array('com_post_id' => '?',
                              'com_pseudo' => '?',
                              'com_email' => '?',
                              'com_message' => '?',
                              'com_status' => '?',
                              'com_date_creation' => '?',
                              'com_read' => '?',
                              'com_parent_id' => '?');

            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->insert('t_comment')
                         ->values($commentData)
                         ->setParameter(0,$comment->getPostId())
                         ->setParameter(1,$comment->getPseudo())
                         ->setParameter(2,$comment->getEmail())
                         ->setParameter(3,$comment->getMessage())
                         ->setParameter(4,$comment->getStatus())
                         ->setParameter(5,date("Y-m-d H:i:s"))
                         ->setParameter(6,$comment->getRead())
                         ->setParameter(7,$comment->getParentId());

            $result = $queryBuilder->execute();

            if(is_int($result) and $result> 0){
                $responseData['nb_row'] = $result;
                $responseData['parent_id'] = $comment->getParentId();
                $responseData['comment_id'] = $queryBuilder->getConnection()->lastInsertId();
            }

            return $responseData;
        }


        /**
         * Creates a Comment object based on a DB row.
         *
         * @param array $row The DB row containing Comment data.
         * @return Comment
         */
        protected function buildDomainObject($row)
        {
            $comment = new Comment($row['com_post_id'], $row['com_id']);

            $comment->setPseudo($row['com_pseudo']);
            $comment->setEmail($row['com_email']);
            $comment->setStatus($row['com_status']);
            $comment->setCreationDate($row['com_date_creation']);
            $comment->setMessage($row['com_message']);
            $comment->setRead($row['com_read']);

            if( !is_null($row['com_parent_id']) ) {
                $comment->setParentId($row['com_parent_id']);
            }

            return $comment;
        }

        private function buildCommentGroup($row)
        {
            $commentGroup    = new CommentGroup($row['com_post_id'], $row['post_title']);

            $comment         = $this->buildDomainObject($row);

            $data['comment'] = $comment;
            $data['ppid']    = $row['ppid'];
            if(array_key_exists('recipient', $row)) {
                $data['recipient'] =$row['recipient'];
            }

            $commentGroup->appendInCommentList($data);

            return $commentGroup;
        }

        /**
         * @param Comment $comment
         * @param Request $request
         */
        private function fillData(Comment $comment, Request $request)
        {
            $comment->setEmail($request->get('email'));
            $comment->setPseudo($request->get('name'));
            $comment->setMessage($request->get('message'));
            if($request->get('pid')<0)
                $comment->setParentId(null);
            else
                $comment->setParentId($request->get('pid'));

        }

    }
