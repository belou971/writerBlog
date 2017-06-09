<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 03/06/2017
 * Time: 00:56
 */


namespace writerBlog\DAO;


    use DAO\DAO;
    use Doctrine\DBAL\Connection;
    use Symfony\Component\HttpFoundation\Request;
    use writerBlog\Domain\ECommentStatus;
    use writerBlog\Domain\Comment;
    use writerBlog\Domain\EPostStatus;

    class CommentDAO extends DAO
    {

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
                ->andWhere('com_status = ?')
                ->setParameter(0, $post_id)
                ->setParameter(1, ECommentStatus::PUBLISHED)
                ->orderBy('com_date_creation', 'ASC');

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
         * @param array $table with result rows of SQL request
         * @return array of Comments objects
         */
        private function buildComments(array $table)
        {
            $comments = array();
            foreach ($table as $row) {
                $com_id = $row['com_id'];
                $comments[$com_id] = $this->buildComment($row);
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
                    echo "At level[$level]: comment Id[".$comment->getId()."]\n";
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


        public function publishComment($id)
        {
            return $this->updateCommentStatus($id, ECommentStatus::PUBLISHED);
        }

        public function hideComment($id)
        {
            return $this->updateCommentStatus($id, ECommentStatus::NOT_PUBLISHED);
        }

        /**
         * @param $id
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        public function deleteComment($id)
        {
            return $this->updateCommentStatus($id, EPostStatus::DISABLED);
        }

        public function alertComment(Request $requestForm)
        {
            return $this->updateCommentStatus($requestForm->get('comment_id'), ECommentStatus::MALICIOUS);

            //sent a message to admin by notifierManager
            //sent a message to the reader by mail
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
         * @param Comment $comment
         * @return \Doctrine\DBAL\Driver\Statement|int
         */
        private function save(Comment $comment)
        {
            $commentData = array('com_post_id' => '?',
                              'com_pseudo' => '?',
                              'com_email' => '?',
                              'com_message' => '?',
                              'com_status' => '?',
                              'com_date_creation' => '?',
                              'com_parent_id' => '?');

            $queryBuilder = $this->getDB()->createQueryBuilder();
            $queryBuilder->insert('t_comment')
                         ->values($commentData)
                         ->setParameter(0,$comment->getPostId())
                         ->setParameter(1,$comment->getPseudo())
                         ->setParameter(2,$comment->getEmail())
                         ->setParameter(3,$comment->getMessage())
                         ->setParameter(4,$comment->getStatus())
                         ->setParameter(5,date("Y-m-d"))
                         ->setParameter(6,$comment->getParentId());

            return $queryBuilder->execute();
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

            if( !is_null($row['com_parent_id']) ) {
                $comment->setParentId($row['com_parent_id']);
            }

            return $comment;
        }

        /**
         * @param Comment $comment
         * @param Request $request
         */
        private function fillData(Comment $comment, Request $request)
        {
            $comment->setEmail($request->get('email'));
            $comment->setPseudo($request->get('pseudo'));
            $comment->setMessage($request->get('message'));
            $comment->setParentId($request->get('parent'));
        }


    }
