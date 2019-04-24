<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;

class AuthorRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllAuthor()
    {
        return $this->entityManager->createQueryBuilder()
            ->select('author')
            ->from(Author::class, 'author')
            ->getQuery()
            ->getResult();
    }

    public function findOneById($authorId)
    {
        return $this->entityManager->getRepository(Author::class)->find($authorId);
    }

    public function findOneByName($name){
        return $this->entityManager->getRepository(Author::class)->find($name);
    }

    public function findOneByEmail($email)
    {
        return $this->entityManager->getRepository(Author::class)->find($email);
        /*return $this->entityManager->createQuery()
            ->where('aut.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();*/
    }
}
