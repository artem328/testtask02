<?php

namespace App\Controller;

use App\Controller\Traits\ManagesUserOwnedEntitiesTrait;
use App\Entity\Portfolio;
use App\Entity\Transaction;
use App\Report\SummaryReport;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PortfolioReportController
 * @package App\Controller
 * @Route("/portfolios/{portfolio}/reports")
 */
class PortfolioReportController extends Controller
{
    use ManagesUserOwnedEntitiesTrait;

    /**
     * @Route("/summary", name="portfolio_report_summary")
     * @Method("GET")
     *
     * @param \App\Entity\Portfolio $portfolio
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summary(Portfolio $portfolio, EntityManagerInterface $entityManager): Response
    {
        $this->abortIfNotOwnedByUser($this->getUser(), $portfolio);

        return $this->render('portfolio/report/summary.html.twig',
            [
                'portfolio' => $portfolio,
                'summary' => new SummaryReport($entityManager, $portfolio),
            ]);
    }
}