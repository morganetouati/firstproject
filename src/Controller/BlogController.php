<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Entity\BlogPost;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /** @var int */
    const POST_LIMIT = 5;

    /**
     * @var ObjectRepository
     */
    private $authorRepository;

    /**
     * @var ObjectRepository
     */
    private $blogPostRepository;

    public function __construct(RegistryInterface $registry)
    {
        $this->blogPostRepository = $registry->getEntityManagerForClass(BlogPost::class)->getRepository(BlogPost::class);
        $this->authorRepository = $registry->getEntityManagerForClass(Author::class)->getRepository(Author::class); // getManagerForClass
    }

    /**
     * @Route("/", name="homepage")
     * @Route("/entries", name="entries")
     */
    public function entriesAction(Request $request)
    {
        $page = 1;
        if ($request->get('page')) {
            $page = $request->get('page');
        }

        return $this->render('blog/entries.html.twig', [
            'blogPosts' => $this->blogPostRepository->getAllPosts($page, self::POST_LIMIT),
            'totalBlogPosts' => $this->blogPostRepository->getPostCount(),
            'page' => $page,
            'entryLimit' => self::POST_LIMIT,
        ]);
    }

    /**
     * @Route("/entry/{slug}", name="entry")
     */
    public function entryAction($slug)
    {
        $blogPost = $this->blogPostRepository->findOneBySlug($slug);
        if (!$blogPost) {
            $this->addFlash('error', 'Unable to find entry!');

            return $this->redirectToRoute('entries');
        }

        return $this->render('blog/entry.html.twig', [
            'blogPost' => $blogPost,
        ]);
    }

    /**
     * @Route("/author/{name}", name="author")
     */
    public function authorAction($name)
    {
        $author = $this->authorRepository->findOneByUsername($name);
        if (!$author) {
            $this->addFlash('error', 'Unable to find author!');

            return $this->redirectToRoute('entries');
        }

        return $this->render('blog/author.html.twig', [
            'author' => $author,
        ]);
    }
}
