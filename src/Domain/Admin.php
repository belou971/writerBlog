<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 01/06/17
 * Time: 17:23
 */

namespace writerBlog\Domain;


use Symfony\Component\Security\Core\User\UserInterface;

class Admin implements UserInterface
{
    private $id;
    private $login;
    private $email;
    private $web_name;
    private $password;
    private $salt;
    private $role;

    public function __construct($id)
    {
        $this->id =$id;
        $this->login = null;
        $this->email = null;
        $this->web_name = null;
        $this->salt = substr(md5(random_int(25, 43)), 0, 23);
        $this->role = ERoleType::ROLE_USER;
    }

    /**
     * @return null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param null $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setUsername($login)
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

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getRoles()
    {
        return array($this->getRole());
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}