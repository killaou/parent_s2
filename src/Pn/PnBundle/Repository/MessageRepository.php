<?php

namespace Pn\PnBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
    /**
     * Get messages of user $user
     *
     * @param $user
     * @return array
     */
    public function getConversations($user)
    {
        // get messages of $user
        $qb = $this->createQueryBuilder('m')
            ->where('m.sender = :user')
            ->setParameter('user', $user)
            ->orWhere('m.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('m.created_at', 'ASC');

        $rawResults = $qb->getQuery()->getResult();

        return $rawResults;
    }
}
