<?php

declare(strict_types = 1);


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Author;
use App\Repository\ArticleRepository;
use App\Repository\AuthorRepository;
use App\Service\ImgUploader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{
    const POST_LIMIT = 5;

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository, AuthorRepository $authorRepository, ManagerRegistry $registry)
    {
        $this->em = $registry->getManager();
        $this->articleRepository = $articleRepository;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route("homepage", name="homepage")
     * @Route("all/", name="all_articles")
     */
    public function articles(Request $request): Response
    {
        $page = 1;
        if ($request->get('page')) {
            $page = $request->query->get('page');
        }

        return $this->render('all_articles.html.twig', [
            'articles' => $this->articleRepository->getAllPosts($page, self::POST_LIMIT),
            'totalArticles' => $this->articleRepository->getPostCount(),
            'page' => $page,
            'articleLimit' => self::POST_LIMIT,
        ]);
    }

    /**
     * @Route("articles/{slug}", name="slug")
     */
    public function article(string $slug): Response
    {
        $author = $this->authorRepository->findOneByLastname($this->getUser()->getUserName());
        $article = $this->articleRepository->findOneBySlug($slug);
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Unable to find article!');

            return $this->redirectToRoute('articles');
        }
        if ($author) {
            $article = $this->articleRepository->findByAuthor('author');
        }

        return $this->render('user/article/showone.html.twig', [
            'article' => $article,
            'author' => $author,
        ]);
    }

    /**
     * @Route("article/author/{authorId}", name="author")
     * @ParamConverter("author", options={"mapping": {"authorId": "id"}})
     */
    public function authorAction(Request $request, Author $author): Response
    {
        $request->attributes->get('author');

        return $this->render('user/article/author.html.twig', [
            'author' => $author,
        ]);
    }

}