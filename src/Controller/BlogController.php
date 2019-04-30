<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BlogPostRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /** @var int */
    const POST_LIMIT = 5;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * @var BlogPostRepository
     */
    private $blogPostRepository;

    public function __construct(RegistryInterface $registry, BlogPostRepository $blogPostRepository, AuthorRepository $authorRepository)
    {
        $this->blogPostRepository = $blogPostRepository;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route("/", name="homepage")
     * @Route("/articles", name="articles")
     */
    public function articlesAction(Request $request)
    {
        $page = 1;
        if ($request->get('page')) {
            $page = $request->get('page');
        }

        return $this->render('blog/articles.html.twig', [
            'blogPosts' => $this->blogPostRepository->getAllPosts($page, self::POST_LIMIT),
            'totalBlogPosts' => $this->blogPostRepository->getPostCount(),
            'page' => $page,
            'articleLimit' => self::POST_LIMIT,
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article")
     */
    public function articleAction(String $slug)
    {
        $blogPost = $this->blogPostRepository->findOneBySlug($slug);
        if (!$blogPost) {
            $this->addFlash('error', 'Unable to find article!');

            return $this->redirectToRoute('articles');
        }

        return $this->render('blog/article.html.twig', [
            'blogPost' => $blogPost,
        ]);
    }

    /**
     * @Route("/author/{authorId}", name="author")
     */
    public function authorAction($authorId)
    {
        $author = $this->authorRepository->findOneById($authorId);

        if (!$author) {
            $this->addFlash('error', 'Unable to find author!');

            return $this->redirectToRoute('articles');
        }

        return $this->render('blog/author.html.twig', [
            'author' => $author,
        ]);
    }
}
