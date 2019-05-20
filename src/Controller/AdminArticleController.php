<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Author;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\AuthorRepository;
use App\Service\ImgUploader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
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
     //* @Route("homepage", name="homepage")
     * @Route("admin/article/articles", name="articles")
     */
    public function articles(Request $request): Response
    {
        $page = 1;
        if ($request->get('page')) {
            $page = $request->query->get('page');
        }

        return $this->render('admin/article/articles.html.twig', [
            'articles' => $this->articleRepository->getAllPosts($page, self::POST_LIMIT),
            'totalArticles' => $this->articleRepository->getPostCount(),
            'page' => $page,
            'articleLimit' => self::POST_LIMIT,
        ]);
    }

    /**
     * @Route("admin/article/articles/{slug}", name="article")
     */
    public function article(String $slug): Response
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

        return $this->render('admin/article/article.html.twig', [
            'article' => $article,
            'author' => $author,
        ]);
    }

    /**
     * @Route("admin/article/new", name="new")
     */
    public function new(Request $request, ImgUploader $imgUploader): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $imgUploader) {
                $article->setImgUploaded($imgUploader->upload($article->getImgUploaded()));
            }
            $this->em->persist($article);
            $this->em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('admin/article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/article/{articleId}/edit/", name="edit-article")
     * @ParamConverter("article", options={"mapping": {"articleId": "id"}})
     */
    public function edit(Article $article, Request $request, ImgUploader $imgUploader): Response
    {
        $request->attributes->get('article');
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $imgUploader) {
                $article->setImgUploaded($imgUploader->upload($article->getImgUploaded()));
            }
            $this->em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('admin/article/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("admin/article/{articleId}/delete/", name="delete-article")
     * @ParamConverter("article", options={"mapping": {"articleId": "id"}})
     */
    public function delete(Article $article, Request $request): Response
    {
        $request->attributes->get('article');
        $this->em->remove($article);
        $this->em->flush();

        return $this->redirectToRoute('articles');
    }

    /**
     * @Route("admin/article/author/{authorId}", name="author")
     * @ParamConverter("author", options={"mapping": {"authorId": "id"}})
     */
    public function authorAction(Request $request, Author $author): Response
    {
        $request->attributes->get('author');

        return $this->render('admin/article/author.html.twig', [
            'author' => $author,
        ]);
    }
}
