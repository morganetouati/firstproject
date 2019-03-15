<?php

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAllPosts($page = 1, $limit = 5)
    {
        return $this->createQueryBuilder('bp')
            ->select('bp')
            ->orderBy('bp.id', 'DESC')
            ->setFirstResult($limit *(--$page))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getPostCount()
    {
        return $this->createQueryBuilder('bp')
            ->select('count(bp)')
            ->getQuery()
            ->getSingleScalarResult();
    }


}
