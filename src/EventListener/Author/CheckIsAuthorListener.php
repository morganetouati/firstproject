<?php
/**
 * Created by PhpStorm.
 * User: morgane
 * Date: 13/03/19
 * Time: 15:56
 */

namespace App\EventListener\Author;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CheckIsAuthorListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    /**
     * CheckIsAuthorListener constructor.
     * @param RouterInterface $router
     * @param SessionInterface $session
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RouterInterface $router, SessionInterface $session,
                                TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }


    /**
     * On Kernel.controller
     * @param FilterControllerEvent $event
     * @return void
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!preg_match('/^\/admin/i', $event->getRequest()->getPathInfo())){
            return;
        }

        if (null === $user = $this->tokenStorage->getToken()->getUser()){
            return;
        }
        if(true === $this->session->get('user_is_author')){
            return;
        }

        $route = $this->router->generate('author_create');
        if (0 === strpos($event->getRequest()->getPathInfo(), $route)){
            return;
        }

        if ($author = $this->entityManager->getRepository('App:Author')
            ->findOneByUsername($user->getUsername())){
            $this->session->set('user_is_author', true);
        }
        if (!$author && $this->session->get('pending_user_is_author')){
            $this->session->getFlashBag()->add(
                'warning',
                'Your author access is being set up, this may take up to 30 seconds. Please try again shortly.'
            );

            $route = $this->router->generate('homepage');
        } else {
            $this->session->getFlashBag()->add(
                'warning',
                'You cannot access the author section until you become an author. Please complete the form below to proceed.'
            );
        }

        $event->setController(function () use ($route) {
            return new RedirectResponse($route);
        });
    }
}