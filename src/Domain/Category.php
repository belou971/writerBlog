<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 18/05/17
 * Time: 17:00
 */

namespace writerBlog\Domain;


class Category
{
    private $i_id;
    private $s_name;

    const DEFAULT_NAME = "default";
    const UNDEFINED = -1;

    /**
     * Category constructor.
     * @param $i_id
     */
    public function __construct($i_id=Post::UNDEFINED)
    {
        $this->i_id = $i_id;
        $this->s_name = Category::DEFAULT_NAME;
    }


    /**
     * @return mixed
     */
    public function getIId()
    {
        return $this->i_id;
    }


    /**
     * @return mixed
     */
    public function getSName()
    {
        return $this->s_name;
    }

    /**
     * @param mixed $s_name
     */
    public function setSName($s_name)
    {
        $this->s_name = $s_name;
    }

}