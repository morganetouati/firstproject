<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\BlogPost;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @var integer
     */
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
        $this->authorRepository = $registry->getEntityManagerForClass(Author::class)->getRepository(Author::class);
    }


    /**
     * @Route("/", name="homepage")
     * @Route("/entries", name="entries")
     */
    public function entriesAction(Request $request)
    {
        $page = 1;
        if ($request->get('page')){
            $page = $request->get('page');
        }
        $author = $this->authorRepository->findOneByUsername($this->getUser()->getUserName());
        $blogPosts = [];
        if ($author){
            $blogPosts = $this->blogPostRepository->finddByAuthor($author);
        }

        return $this->render('blog/entries.html.twig', [
            'blogPosts' => $this->blogPostRepository->getAllPosts($page, self::POST_LIMIT),
            'totalBlogPosts' => $this->blogPostRepository->getPostCount(),
            'page' => $page,
            'entryLimit' => self::POST_LIMIT
        ]);
    }


    /**
     * @Route("/entry/{slug}", name="entry")
     */
    public function entryAction($slug)
    {
        $blogPost = $this->blogPostRepository->findOneBySlug($slug);

        if ()
        return $this->render('blog/entry.html.twig', array(
            'blogPost' => $blogPost
        ));
    }

}
