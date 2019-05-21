<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorFormType;
use App\Repository\AuthorRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAuthorController extends AbstractController
{
    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    public function __construct(AuthorRepository $authorRepository, ManagerRegistry $registry)
    {
        $this->em = $registry->getManager();
        $this->authorRepository = $authorRepository;
    }

    /**
     * @Route("admin/author/authors", name="admin_authors")
     */
    public function authors()
    {
        return $this->render('admin/author/authors.html.twig', [
            'authors' => $this->authorRepository->getAllAuthor(), ]);
    }

    /**
     * @Route("admin/author/new", name="new-author")
     */
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($author);
            $this->em->flush();

            return $this->redirectToRoute('admin_authors');
        }

        return $this->render('admin/author/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/author/{authorId}/delete/", name="delete-author")
     * @ParamConverter("author", options={"mapping": {"authorId": "id"}})
     */
    public function delete(Author $author): Response
    {
//        $request->attributes->get('author');
        $this->em->remove($author);
        $this->em->flush();

        return $this->redirectToRoute('admin_authors');
    }

    /**
     * @Route("admin/author/{authorId}/edit/", name="edit-author")
     * @ParamConverter("author", options={"mapping": {"authorId": "id"}})
     */
    public function update(Author $author, Request $request): Response
    {
//        $request->attributes->get('author');
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('admin_authors');
        }

        return $this->render(
            'admin/author/edit.html.twig', ['form' => $form->createView()]);
    }
}
