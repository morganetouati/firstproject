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
     * @Route("/author/create", name="author_create")
     */
    public function createAuthorAction(Request $request, RegistryInterface $registry): Response
    {
        if ($this->authorRepository->findOneByUsername($this->getUser()->getUserName())) {
            // Redirect to dashboard.
            $this->addFlash('error', 'Unable to create author, author already exists!');

            return $this->redirectToRoute('homepage');
        }
        $author = new Author();
        $author->setUsername($this->getUser()->getUserName());
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $registry->getManagerForClass(Author::class);
            $em->persist($author);
            $em->flush($author);
            $request->getSession()->set('user_is_author', true);
            $this->addFlash('success', 'Congratulations! You are now an author.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('admin/create_author.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create-entry", name="admin_create_entry")
     *
     * @param Request $request
     */
    public function createEntryAction(Request $request, RegistryInterface $registry, ImgUploader $imgUploader): Response
    {
        $blogPost = new BlogPost();
        $author = $this->authorRepository->findOneByUsername($this->getUser()->getUserName());
        $blogPost->setAuthor($author);
        $form = $this->createForm(EntryFormType::class, $blogPost);
        $form->handleRequest($request);

        // Check is valid
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $imgUploader) {
                $blogPost->setImgUploaded($imgUploader->upload($blogPost->getImgUploaded()));
            }
            $em = $registry->getManagerForClass(BlogPost::class);
            $em->persist($blogPost);
            $em->flush();
            $this->addFlash('success', 'Congratulations! Your post is created');

            return $this->redirectToRoute('admin_entries');
        }

        return $this->render('admin/entry_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="admin_index")
     * @Route("/entries", name="admin_entries")
     */
    public function entriesAction()
    {
        $author = $this->authorRepository->findOneByUsername($this->getUser()->getUserName());
        $blogPosts = [];
        if ($author) {
            $blogPosts = $this->blogPostRepository->findByAuthor('author');
        }

        return $this->render('admin/entries.html.twig', [
            'blogPosts' => $blogPosts,
        ]);
    }

    /**
     * @Route("/delete-entry/{entryId}", name="admin_delete_entry")
     *
     * @param $entryId
     *
     * @return \Response
     */
    public function deleteEntryAction($entryId, RegistryInterface $registry): Response
    {
        $blogPost = $this->blogPostRepository->findOneById($entryId);
        $author = $this->authorRepository->findOneByUsername($this->getUser()->getUserName());
        if (!$blogPost || $author !== $blogPost->getAuthor()) {
            $this->addFlash('error', 'Unable to remove entry!');

            return $this->redirectToRoute('admin_entries');
        }
        $em = $registry->getManagerForClass(BlogPost::class);
        $em->remove($blogPost);
        $em->flush();
        $this->addFlash('success', 'Entry was deleted!');

        return $this->redirectToRoute('admin_entries');
    }

    private function generateUniqueImgName()
    {
        return \md5(\uniqid('', true));
    }
}
