<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}