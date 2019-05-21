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

    public function findOneByUsername(string $username): ?Author
    {
        return $result = $this->entityManager->createQueryBuilder()
          ->select('author')
          ->from(Author::class, 'author')
          ->where('author.username = :username')
          ->setParameter('author', $username)
          ->getQuery()
          ->getOneOrNullResult();
    }
}
