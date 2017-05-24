<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 24/05/2017
 * Time: 15:25
 */

namespace writerBlog\Domain;


class Blog
{
    private $i_id;
    private $s_title;
    private static $instance = NULL;

    /**
     * Blog constructor.
     * @param $i_id
     * @param $s_title
     */
    private function __construct()
    {

    }

    public static function getInstance()
    {
        if(!is_null(Blog::$instance)) { return Blog::$instance; }

        Blog::$instance = new Blog();
        return Blog::$instance;
    }

    /**
     * @return mixed
     */
    public function getSTitle()
    {
        return $this->s_title;
    }

    /**
     * @param mixed $s_title
     */
    public function setSTitle($s_title)
    {
        $this->s_title = $s_title;
    }

    /**
     * @return mixed
     */
    public function getIId()
    {
        return $this->i_id;
    }

    /**
     * @param $i_id
     */
    public function setIId($i_id)
    {
        $this->i_id = $i_id;
    }

}