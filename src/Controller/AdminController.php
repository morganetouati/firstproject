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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
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
    public function entriesAction()
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
        //$author = new Author();
        $author = $this->getDoctrine()->getRepository(Author::class)->find($authorId);
        //$form = $this->createForm($author);

        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Actor was updated!');
            return $this->redirectToRoute('list_author');
        }
        return $this->render(
            'admin/author/update_author.html.twig',
            ['form' => $form->createView()]
        );

//        $author = $this->authorRepository->findOneById($authorId);
    }


    /**
     * @Route("/delete-entry/{entryId}", name="admin_delete_entry")
     * @param $entryId
     * @return /Response
     */
    public function deleteEntryAction($entryId, RegistryInterface $registry): Response
    {
        $blogPost = $this->blogPostRepository->findOneById($entryId);
        $author = $this->authorRepository->findOneByLastname($this->getUser()->getUserName());
        if (!$blogPost || $author !== $blogPost->getAuthor()) {
            $this->addFlash('error', 'Unable to remove entry!');

            return $this->redirectToRoute('admin_entries');
        }
        $em = $registry->getEntityManagerForClass(BlogPost::class);
        $em->remove($blogPost);
        $em->flush();
        $this->addFlash('success', 'Entry was deleted!');

        return $this->redirectToRoute('admin_entries');
    }
}
