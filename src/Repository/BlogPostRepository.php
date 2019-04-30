<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

class BlogPostRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findByAuthor(string $author): ?Article
    {
        return $result = $this->entityManager->createQueryBuilder()
            ->select('bp')
            ->from(Article::class, 'bp')
            ->where('bp.author = :author')
            ->setParameter('author', $author)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySlug(string $slug): ?Article
    {
        return $result = $this->entityManager->createQueryBuilder()
            ->select('bp')
            ->from(Article::class, 'bp')
            ->where('bp.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getAllPosts($page = 1, $limit = 5): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('bp')
            ->from(Article::class, 'bp')
            ->orderBy('bp.id', 'DESC')
            ->setFirstResult($limit * (--$page))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getPostCount(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('count(bp)')
            ->from(Article::class, 'bp')
            ->getQuery()
            ->getSingleResult();
    }
}
