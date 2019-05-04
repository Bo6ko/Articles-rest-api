<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\UserType;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_login"),
     * @Method({"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request) : Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('view_articles');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout"),
     * @Method({"GET", "POST"})
     */
    public function logout()
    {
        //return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/register", name="register"),
     * @Method({"GET", "POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('view_articles');
        }

        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()) {
            try {
                $user = $userForm->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash("addError", "Добавен е нов потребител - " . ucfirst($user->getUsername()) .
                    "! Моля въведете име и парола! ");
            } catch(\Exception $e) {
                $this->addFlash("addError", "Вече съществува потребител с това име " . ucfirst($user->getUsername()));
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/create_user.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
}
