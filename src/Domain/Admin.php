<?php
/**
 * Created by PhpStorm.
 * User: belou
 * Date: 01/06/17
 * Time: 17:23
 */

namespace writerBlog\Domain;


class Admin
{
    private $id;
    private $login;
    private $web_name;

    public function __construct($id)
    {
        $this->id =$id;
        $this->login = null;
        $this->web_name = null;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
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
    public function getWebName()
    {
        return $this->web_name;
    }

    /**
     * @param mixed $web_name
     */
    public function setWebName($web_name)
    {
        $this->web_name = $web_name;
    }



}