<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 11/05/2017
 * Time: 13:36
 */
namespace writerBlog\Domain {

    use DateTime;
    use Doctrine\DBAL\Driver\PDOConnection;
    use writerBlog\Domain\Category;


    /**
     * Class Post
     *
     * Class definition of a Post of the blog
     *
     * @author Eve ODIN <belou.atlantis@gmail.com>
     * @version 1.0.0
     *
     * @package lightcms\Domain
     */
    class Post
    {
        /**
         *
         */
        const EXTRACT_SIZE = 150;
        const UNDEFINED = -1;

        /**
         * Post constructor.
         */
        public function __construct($id=Post::UNDEFINED)
        {
            $this->i_id = $id;
            $this->s_title = null;
            $this->id_author = null;
            $this->s_content = null;
            $this->s_extract = null;
            $this->e_status = EPostStatus::NOT_PUBLISHED;
            $this->s_url_image = null;
            $this->creation_date = null;
            $this->modification_date = null;
            $this->i_nb_visit = 0;
            $this->id_category = null;
        }

        /**
         * @var integer $i_id represents the post id
         */
        private $i_id;
        /**
         * @var string $s_title represents the post title
         */
        private $s_title;
        /**
         * @var int $id_author represents author id of the post
         */
        private $id_author;
        /**
         * @var string $s_content represents the post content
         */
        private $s_content;
        /**
         * @var string $s_extract represents an extract of the post content
         */
        private $s_extract;
        /**
         * @var EPostStatus Indicates the post status
         *
         * @see /Domain/Post.php
         * @see EPostStatus Defines post status types
         */
        private $e_status;
        /**
         * @var string $s_url_image url path of the image source
         */
        private $s_url_image;
        /**
         * @var integer $i_nb_visit number of view for the post
         */
        private $i_nb_visit;
        /**
         * @var DateTime $creation_date represents date and time the post is created
         */
        private $creation_date;
        /**
         * @var DateTime $modification_date represents date and time the post is modified
         */
        private $modification_date;

        /**
         * @var int $id_category represents  the category id of that post belongs to
         */
        private $id_category;


        /**
         * @return int
         */
        public function getIdCategory()
        {
            return $this->id_category;
        }

        /**
         * @param int $id_category
         */
        public function setIdCategory($id_category)
        {
            $this->id_category = $id_category;
        }

        /**
         * @return int Returns the post id
         */
        public function getIId()
        {
            return $this->i_id;
        }

        /**
         * @return string Returns the post title
         */
        public function getSTitle()
        {
            return $this->s_title;
        }

        /**
         * @param string $s_title Set the post title
         * @return Post
         */
        public function setSTitle($s_title)
        {
            $this->s_title = $s_title;
            return $this;
        }

        /**
         * @return the author id of the post
         */
        public function getAuthor()
        {
            return $this->id_author;
        }

        /**
         * @param int $authorId Sets the author id
         * @return Post
         */
        public function setAuthor($authorId)
        {
            $this->id_author = $authorId;
            return $this;
        }

        /**
         * @return string Returns the post content
         */
        public function getSContent()
        {
            return $this->s_content;
        }

        /**
         * @param string $s_content Sets the post content
         * @return Post
         */
        public function setSContent($s_content)
        {
            $this->s_content = $s_content;

            if( strlen($this->s_content) <= Post::EXTRACT_SIZE ) {
                $this->setSExtract($this->s_content);
            } else {
                $this->setSExtract(substr($this->s_content, 0, Post::EXTRACT_SIZE));
            }

            return $this;
        }

        /**
         * @return string Returns the extract of the post content
         */
        public function getSExtract()
        {
            return $this->s_extract;
        }

        /**
         * @param string $s_extract Sets the extract of the post content
         * @return Post
         */
        public function setSExtract($s_extract)
        {
            $this->s_extract = $s_extract;
            return $this;
        }

        /**
         * @return EPostStatus Returns the post status
         */
        public function getEStatus()
        {
            return $this->e_status;
        }

        /**
         * @param EPostStatus $e_status Sets the post status
         * @return Post
         */
        public function setEStatus($e_status)
        {
            $this->e_status = $e_status;
            return $this;
        }

        /**
         * @return string|null Returns the url path of the post image if it exists
         */
        public function getSUrlImage()
        {
            return $this->s_url_image;
        }

        /**
         * @param string $s_url_image Sets an url path of the post image
         * @return Post
         */
        public function setSUrlImage($s_url_image)
        {
            $this->s_url_image = $s_url_image;
            return $this;
        }

        /**
         * @return int Returns the number of visit for this post
         */
        public function countVisit()
        {
            return $this->i_nb_visit;
        }

        /**
         * Updates the number of visit with an incremental computation
         * @return Post
         */
        public function UpdateNbVisit()
        {
            ++$this->i_nb_visit;
            return $this;
        }

        /**
         * @return DateTime Returns the date of creation of the post
         */
        public function getCreationDate()
        {
            return $this->creation_date;
        }

        /**
         * @param DateTime $creation_date
         * @return Post
         */
        public function setCreationDate($creation_date)
        {
            $this->creation_date = $creation_date;
            return $this;
        }

        /**
         * @return DateTime Returns the date of the modification of the post
         */
        public function getModificationDate()
        {
            return $this->modification_date;
        }

        /**
         * @param DateTime $modification_date Updates the date of modification of the post
         * @return Post
         */
        public function setModificationDate($modification_date)
        {
            $this->modification_date = $modification_date;
            return $this;
        }

        public function __toString()
        {
            return "post_id[" . $this->i_id . "] title[" . $this->s_title . "]";
        }
    }
}