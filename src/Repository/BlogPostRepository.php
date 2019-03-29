<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BlogPostRepository
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(RegistryInterface $registry)
    {
        $this->manager = $registry->getManagerForClass(BlogPost::class);
    }

    /**bp
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getAllPosts($page = 1, $limit = 5): array
    {
        return $this->manager->createQueryBuilder()
            ->select('bp')
            ->from(BlogPost::class, 'bp')
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
        return $this->manager->createQueryBuilder()
            ->select('count(bp)')
            ->from(BlogPost::class, 'bp')
            ->getQuery()
            ->getSingleResult();
    }

    public function findOneBySlug(string $slug): ?BlogPost
    {
        //  $slug = $slug['slug'];
        return $result = $this->manager->createQueryBuilder()
            ->select('bp')
            ->from(BlogPost::class, 'bp')
            ->where('bp.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
        //dump($result); exit();
    }

    public function findByAuthor(string $author): ?BlogPost
    {
        return $result = $this->manager->createQueryBuilder()
            ->select('bp')
            ->from(BlogPost::class, 'bp')
            ->where('bp.author = :author')
            ->setParameter('author', $author)
            ->getQuery()
            ->getOneOrNullResult();
    }


}
