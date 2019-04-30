<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Author;
use App\Form\ArticleFormType;
use App\Form\AuthorFormType;
use App\Repository\AuthorRepository;
use App\Repository\BlogPostRepository;
use App\Service\ImgUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
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

    public function __construct(RegistryInterface $registry, BlogPostRepository $blogPostRepository, AuthorRepository $authorRepository, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->blogPostRepository = $blogPostRepository;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route("/author/create", name="author_create")
     */
    public function createAuthorAction(Request $request, RegistryInterface $registry): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $registry->getEntityManagerForClass(Author::class);
            $em->persist($author);
            $em->flush();

            $request->getSession()->set('user_is_author', true);
            $this->addFlash('success', 'Congratulations! You are now an author.');

            return $this->redirectToRoute('list_author');
        }

        return $this->render('admin/author/create_author.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create_article", name="admin_create_article")
     *
     * @param Request $request
     */
    public function createEntryAction(Request $request, RegistryInterface $registry, ImgUploader $imgUploader): Response
    {
        $blogPost = new Article();
        $form = $this->createForm(ArticleFormType::class, $blogPost);
        $form->handleRequest($request);

        // Check is valid
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $imgUploader) {
                $blogPost->setImgUploaded($imgUploader->upload($blogPost->getImgUploaded()));
            }
            $em = $registry->getEntityManagerForClass(Article::class);
            $em->persist($blogPost);
            $em->flush();
            $this->addFlash('success', 'Congratulations! Your post is created');

            return $this->redirectToRoute('articles');
        }

        return $this->render('admin/article/create_article.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list_author", name="list_author")
     */
    public function authorAction()
    {
        return $this->render('admin/author/list_author.twig', [
            'author' => $this->authorRepository->getAllAuthor(), ]);
    }

    /**
     * @Route("/delete-author/{authorId}", name="admin_delete_author")
     *
     * @param $authorId
     *
     * @return /Response
     */
    public function deleteAuthorAction($authorId): Response
    {
        $author = $this->authorRepository->findOneById($authorId);
        $this->em->remove($author);
        $this->em->flush();

        $this->addFlash('success', 'Actor was deleted!');

        return $this->redirectToRoute('list_author');
    }

    /**
     * @Route("/update-author/{authorId}", name="admin_update_author")
     *
     * @param $authorId
     *
     * @return /Response
     */
    public function updateAuthorAction(Author $authorId, Request $request, EntityManagerInterface $em): Response
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($authorId);
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Actor was updated!');

            return $this->redirectToRoute('list_author');
        }

        return $this->render(
            'admin/author/update_author.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/articles", name="admin_articles")
     */
    public function articlesAction(Request $request)
    {
        $page = 1;
        if ($request->get('page')) {
            $page = $request->get('page');
        }

        return $this->render('admin/article/articles.html.twig', [
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

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('blog/article.html.twig', [
            'blogPost' => $blogPost,
        ]);
    }

    /**
     * @Route("/update-article/{articleId}", name="admin_update_article")
     *
     * @param Article                $articleId
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function updateArticleAction(Article $articleId, Request $request, EntityManagerInterface $em, ImgUploader $imgUploader): Response
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($articleId);
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $imgUploader) {
                $article->setImgUploaded($imgUploader->upload($article->getImgUploaded()));
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Article was updated');

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/article/update_article.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete-article/{articleId}", name="admin_delete_article")
     *
     * @param $articleId
     *
     * @return /Response
     */
    public function deleteArticleAction($articleId, RegistryInterface $registry): Response
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($articleId);
        $this->em->remove($article);
        $this->em->flush();
        $this->addFlash('success', 'Article was deleted!');

        return $this->redirectToRoute('admin_articles');
    }
}
