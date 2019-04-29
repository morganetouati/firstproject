<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Entity\BlogPost;
use App\Form\AuthorFormType;
use App\Form\EntryFormType;
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
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/create-entry", name="admin_create_entry")
     * @param Request $request
     */
    public function createEntryAction(Request $request, RegistryInterface $registry, ImgUploader $imgUploader): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(EntryFormType::class, $blogPost);
        $form->handleRequest($request);

        // Check is valid
        if ($form->isSubmitted() && $form->isValid()) {
            if ($imgUploader !== null) {
                $blogPost->setImgUploaded($imgUploader->upload($blogPost->getImgUploaded()));
            }
            $em = $registry->getEntityManagerForClass(BlogPost::class);
            $em->persist($blogPost);
            $em->flush();
            $this->addFlash('success', 'Congratulations! Your post is created');
            return $this->redirectToRoute('entries');
        }

        return $this->render('admin/article/entry_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list_author", name="list_author")
     */
    public function authorAction()
    {
        return $this->render('admin/author/list_author.twig', [
            'author' => $this->authorRepository->getAllAuthor()]);
    }


    /**
     * @Route("/delete-author/{authorId}", name="admin_delete_author")
     * @param $authorId
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
     * @param $authorId
     * @return /Response
     */
    public function updateAuthorAction(Author $authorId, Request $request, EntityManagerInterface $em): Response
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($authorId);
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Actor was updated!');
            return $this->redirectToRoute('list_author');
        }
        return $this->render(
            'admin/author/update_author.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/entries", name="admin_entries")
     */
    public function entriesAction(Request $request)
    {
        $page = 1;
        if ($request->get('page')) {
            $page = $request->get('page');
        }

        return $this->render('admin/article/entries.html.twig', [
            'blogPosts' => $this->blogPostRepository->getAllPosts($page, self::POST_LIMIT),
            'totalBlogPosts' => $this->blogPostRepository->getPostCount(),
            'page' => $page,
            'entryLimit' => self::POST_LIMIT,
        ]);
    }

    /**
     * @Route("/entry/{slug}", name="entry")
     */
    public function entryAction(String $slug)
    {
        $blogPost = $this->blogPostRepository->findOneBySlug($slug);
        if (!$blogPost) {
            $this->addFlash('error', 'Unable to find entry!');

            return $this->redirectToRoute('admin_entries');
        }
        return $this->render('blog/entry.html.twig', [
            'blogPost' => $blogPost,
        ]);
    }

    /**
     * @Route("/update-entry/{entryId}", name="admin_update_entry")
     * @param BlogPost $entryId
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function updateEntryAction(BlogPost $entryId, Request $request, EntityManagerInterface $em, ImgUploader $imgUploader): Response
    {
        $entry = $this->getDoctrine()->getRepository(BlogPost::class)->find($entryId);
        $form = $this->createForm(EntryFormType::class, $entry);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if ($imgUploader !== null) {
                $entry->setImgUploaded($imgUploader->upload($entry->getImgUploaded()));
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Entry was updated');
            return $this->redirectToRoute('admin_entries');
        }
        return $this->render('admin/article/update_entry.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete-entry/{entryId}", name="admin_delete_entry")
     * @param $entryId
     * @return /Response
     */
    public function deleteEntryAction($entryId, RegistryInterface $registry): Response
    {
        $entry = $this->getDoctrine()->getRepository(BlogPost::class)->find($entryId);
        $this->em->remove($entry);
        $this->em->flush();
        $this->addFlash('success', 'Entry was deleted!');
        return $this->redirectToRoute('admin_entries');
    }
}
