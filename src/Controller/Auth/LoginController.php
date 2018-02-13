<?php

namespace App\Controller\Auth;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{

    /**
     * @Route("/login", name="login")
     * @Method("GET|POST")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @param \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $form = $this->get('form.factory')
            ->createNamedBuilder(null)
            ->add('_username', TextType::class, ['required' => true])
            ->add('_password', PasswordType::class, ['required' => true])
            ->add('login', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('danger', $error->getMessage());
        }

        return $this->render('auth/login.html.twig', ['form' => $form->createView(),]);
    }

}