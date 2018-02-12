<?php

namespace App\Controller\Auth;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    /**
     * @Route("/login", name="login_form")
     * @Method("GET")
     */
    public function loginForm()
    {
        return new Response($this->renderView('auth/login.html.twig'));
    }

    /**
     * @Route("/login", name="login")
     * @Method("POST")
     */
    public function loginAction()
    {

    }

}