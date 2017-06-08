<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 08/06/2017
 * Time: 18:02
 */

namespace writerBlog\Domain;


class Subscriber
{
    private $id;
    private $pseudo;
    private $email;
    private $date_creation;

    /**
     * Subscribe constructor.
     */
    public function __construct()
    {
        $this->id     = null;
        $this->pseudo = null;
        $this->email  = null;
        $this->date_creation = null;
    }

    /**
     * @return null
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * @param null $date_creation
     */
    public function setDateCreation($date_creation)
    {
        $this->date_creation = $date_creation;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

}