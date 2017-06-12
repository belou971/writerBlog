<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 24/05/2017
 * Time: 15:38
 */

namespace writerBlog\DAO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use writerBlog\DAO\Dao;
use Doctrine\DBAL\Connection;
use writerBlog\Domain\Admin;
use writerBlog\Domain\Blog;
use writerBlog\Domain\ERoleType;

class AdminDAO extends Dao implements UserProviderInterface
{
    public function get()
    {
        $queryBuilder = $this->getDB()->createQueryBuilder();

        $queryBuilder->select('*')
                     ->from('t_admin');

        $statement = $queryBuilder->execute();
        if(is_int($statement)) {
            throw new \Exception("Admin information did not find into database");
        }

        $result = $statement->fetch();
        return $this->buildDomainObject($result);
    }

    protected function buildDomainObject($row)
    {
        if(is_null($row)|| !isset($row)) {
            throw new \Exception("Cannot build Admin data");
        }

        $admin = new Admin($row['adm_id']);
        $admin->setUsername($row['adm_login']);
        $admin->setWebName($row['adm_web_name']);
        $admin->setRole($row['adm_role']);
        $admin->setPassword($row['adm_pwd']);
        $admin->setSalt($row['adm_salt']);

        return $admin;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $queryBuilder = $this->getDB()->createQueryBuilder();

        $queryBuilder->select('*')
            ->from('t_admin')
            ->where('adm_login = ?')
            ->setParameter(0, $username);

        $statement = $queryBuilder->execute();
        if(is_int($statement)) {
            throw new UsernameNotFoundException(sprintf('Admin as user name %s not found', $username));
        }
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        $class_name = get_class($user);
        if(!$this->supportsClass($class_name)) {
            throw new UnsupportedUserException(sprintf('Instance of %s is not supported', $class_name));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return ($class === 'writerBlog\Domain\Admin');
    }

    public function existAdmin()
    {
        $queryBuilder = $this->getDB()->createQueryBuilder();

        $queryBuilder->select('*')
                     ->from('t_admin');

        $statement = $queryBuilder->execute();
        var_dump($statement->rowCount());
        echo "nbl = ".$statement->rowCount();
        return ($statement->rowCount() === 1);
    }

    public function updateAdmin(Request $request, BCryptPasswordEncoder $encoder)
    {
        $user = new Admin(-1);

        $this->fillData($user, $request, $encoder);

        return $this->update($user);
    }

    private function fillData(Admin $user, Request $request, BCryptPasswordEncoder $encoder) {
        $user->setUsername($request->get('username'));
        $user->setWebName($request->get('publicname'));
        $user->setEmail($request->get('email'));

        $password_encoder = $encoder->encodePassword($request->get('password'), $user->getSalt());
        $user->setPassword($password_encoder);
    }

    private function update(Admin $user)
    {
        $queryBuilder = $this->getDB()->createQueryBuilder();
        $queryBuilder->update('t_admin', 'a')
            ->set('a.adm_login','?')
            ->set('a.adm_email', '?')
            ->set('a.adm_pwd', '?')
            ->set('a.adm_web_name' , '?')
            ->set('a.adm_salt' , '?')
            ->set('a.adm_role', '?')
            ->where('a.adm_id = ?')
            ->setParameter(0,$user->getUsername())
            ->setParameter(1,$user->getEmail())
            ->setParameter(2,$user->getPassword())
            ->setParameter(3,$user->getWebName())
            ->setParameter(4,$user->getSalt())
            ->setParameter(5,ERoleType::ROLE_ADMIN)
            ->setParameter(6,1);

        return $queryBuilder->execute();
    }
}