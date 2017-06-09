<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 08/06/2017
 * Time: 18:16
 */

namespace writerBlog\DAO;

use DAO\DAO;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use writerBlog\Domain\Subscriber;

class SubscriberDAO extends DAO
{
    public function addSubscriber(Request $requestForm)
    {
        $subscriber = new Subscriber();
        $subscriber->setPseudo($requestForm->get('pseudo'));
        $subscriber->setEmail($requestForm->get('email'));

        return $this->save($subscriber);
    }

    private function save(Subscriber $subscriber)
    {
        $subscriberData = array('sub_pseudo' => '?',
                                'sub_email' => '?',
                                'sub_date_creation' => '?');

        $queryBuilder = $this->getDB()->createQueryBuilder();
        $queryBuilder->insert('t_subscriber')
            ->values($subscriberData)
            ->setParameter(0,$subscriber->getPseudo())
            ->setParameter(1,$subscriber->getEmail())
            ->setParameter(2,date("Y-m-d"));

        return $queryBuilder->execute();
    }

    protected function buildDomainObject($row)
    {
    }
}