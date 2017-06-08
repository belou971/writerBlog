<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 03/06/17
 * Time: 00:20
 */

namespace writerBlog\Domain;


class Comment
{
    const UNDEFINED = -1;

    private $id;
    private $parent_id;
    private $post_id;
    private $pseudo;
    private $email;
    private $status;
    private $creation_date;
    private $message;

    /**
     * Comment constructor.
     * @param $id
     */
    public function __construct($post_id, $id=Comment::UNDEFINED)
    {
        $this->id            = $id;
        $this->post_id       = $post_id;
        $this->parent_id     = Comment::UNDEFINED;
        $this->pseudo        = null;
        $this->email         = null;
        $this->status        = ECommentStatus::PUBLISHED;
        $this->creation_date = null;
        $this->message       = null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    /**
     * @param mixed $post_id
     */
    public function setPostId($post_id)
    {
        $this->post_id = $post_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * @param mixed $creation_date
     */
    public function setCreationDate($creation_date)
    {
        $this->creation_date = $creation_date;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
}