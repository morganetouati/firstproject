<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\UserFormType;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="register")
     */
    public function register(Request $request, RegistryInterface $registry, UserPasswordEncoderInterface $passwordEncoder)
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
        //En cas d'erreur on reste sur le formulaire
        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}