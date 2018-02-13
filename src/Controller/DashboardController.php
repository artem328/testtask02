<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        return $this->render('dashboard.html.twig', ['user' => $this->getUser()]);
    }
}