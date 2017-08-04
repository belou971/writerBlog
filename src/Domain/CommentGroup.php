<?php
/**
 * Created by PhpStorm.
 * User: belou
 * Date: 04/08/17
 * Time: 04:32
 */

namespace writerBlog\Domain;


class CommentGroup
{
    private $post_id;
    private $post_title;
    private $comment_list;

    /**
     * CommentGroup constructor.
     * @param $post_id
     * @param $post_title
     */
    public function __construct($post_id, $post_title)
    {
        $this->post_id = $post_id;
        $this->post_title = $post_title;
        $this->comment_list = array();
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
     * @return mixed
     */
    public function getPostTitle()
    {
        return $this->post_title;
    }

    /**
     * @param mixed $post_title
     */
    public function setPostTitle($post_title)
    {
        $this->post_title = $post_title;
    }

    /**
     * @return mixed
     */
    public function getCommentList()
    {
        return $this->comment_list;
    }

    /**
     * @param mixed $comment_list
     */
    public function setCommentList($comment_list)
    {
        $this->comment_list = $comment_list;
    }

    public function appendInCommentList($element)
    {
        array_push($this->comment_list, $element);
    }


}