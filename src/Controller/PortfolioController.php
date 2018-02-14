<?php

namespace App\Controller;

use App\Entity\Portfolio;
use App\Form\Portfolio\PortfolioCreateType;
use App\Form\Portfolio\PortfolioDeleteType;
use App\Form\Portfolio\PortfolioEditType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PortfolioController
 * @package App\Controller
 * @Route("/portfolios")
 */
class PortfolioController extends Controller
{

    use HasPaginationTrait;

    /**
     * @Route("/", name="portfolio_index")
     * @Method("GET")
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $portfoliosQuery = $entityManager->getRepository(Portfolio::class)
            ->createQueryBuilder('p')
            ->where('p.user = :user_id')->setParameter('user_id', $this->getUser()->getId())
            ->getQuery();

        $paginator = $this->paginate($portfoliosQuery);

        return $this->render('portfolio/index.html.twig', ['paginator' => $paginator]);
    }

    /**
     * @Route("/create", name="portfolio_create")
     * @Method("GET|POST")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $portfolio = new Portfolio();
        $portfolio->setUser($this->getUser());

        $form = $this->createForm(PortfolioCreateType::class, $portfolio);
        $form->add('create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($portfolio);
            $entityManager->flush();

            return $this->redirectToRoute('portfolio_edit', ['portfolio' => $portfolio->getId()]);
        }

        return $this->render('portfolio/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{portfolio}", name="portfolio_edit")
     * @Method("GET|PATCH")
     *
     * @param \App\Entity\Portfolio $portfolio
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Portfolio $portfolio, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PortfolioEditType::class, $portfolio);
        $form->add('update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($portfolio);
            $entityManager->flush();

            return $this->redirectToRoute('portfolio_edit', ['portfolio' => $portfolio->getId()]);
        }

        $deleteForm = $this->createForm(PortfolioDeleteType::class);
        $deleteForm->add('Delete', SubmitType::class);

        return $this->render('portfolio/edit.html.twig',
            [
                'form' => $form->createView(),
                'deleteForm' => $deleteForm->createView(),
            ]);
    }

    /**
     * @Route("/{portfolio}", name="portfolio_delete")
     * @Method("DELETE")
     *
     * @param \App\Entity\Portfolio $portfolio
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Portfolio $portfolio, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($portfolio);
        $entityManager->flush();

        return $this->redirectToRoute('portfolio_index');
    }

}