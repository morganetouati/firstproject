<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, RegistryInterface $registry, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $registry->getEntityManagerForClass(User::class);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'registration/register.html.twig',
            ['form' => $form->createView()]
        );
    }
}
