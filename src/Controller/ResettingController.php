<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ResettingType;
use App\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route("/repeat-password")
 */
class ResettingController extends AbstractController
{
    /**
     * @Route("/requete", name="request_resetting")
     */
    public function request(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank(),
                ],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->loadUserByUsername($form->getData()['email']);
            if (!$user) {
                $request->getSession()->getFlashBag()->add('warning', "this email doesn't exist");

                return $this->redirectToRoute('request_resetting');
            }

            $user->setToken($tokenGenerator->generateToken());
            $user->setPasswordRequestedAt(new \DateTime());
            $em->flush();

            $bodyMail = $mailer->createBodyMail('resetting/mail.html.twig', [
               'user' => $user,
            ]);
            $mailer->sendMessage('from@email.com', $user->getEmail(), 'renewal of password', $bodyMail);
            $request->getSession()->getFlashBag()->add('success', 'An email will be sent to you so that you can renew your password. The link you will receive will be valid 24h');

            return $this->redirectToRoute('login');
        }

        return $this->render('resetting/request.html.twig', ['form' => $form->createView()]);
    }

    private function isRequestInTime(\DateTime $passwordRequestedAt = null)
    {
        if (null === $passwordRequestedAt) {
            return false;
        }
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();
        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $response = true;

        return $response;
    }

    /**
     * @Route("/{id}/{token}", name="resetting")
     *
     * @param User $user
     * @param $token
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function resetting(User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if (null === $user->getToken() || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt())) {
            throw new AccessDeniedException();
        }
        $form = $this->createForm(ResettingType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'password has been renewed');

            return $this->redirectToRoute('login');
        }

        return $this->render('resetting/index.html.twig', ['form' => $form->createView()]);
    }
}
