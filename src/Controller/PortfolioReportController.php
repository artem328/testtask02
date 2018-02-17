<?php

namespace App\Controller;

use App\Controller\Traits\ManagesUserOwnedEntitiesTrait;
use App\Entity\Portfolio;
use App\Finance\Api\ApiClientInterface;
use App\Report\HistoryReport;
use App\Report\SummaryReport;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function summary(Portfolio $portfolio, EntityManagerInterface $entityManager): Response
    {
        $this->abortIfNotOwnedByUser($this->getUser(), $portfolio);

        return $this->render('portfolio/report/summary.html.twig',
            [
                'summary' => new SummaryReport($entityManager, $portfolio),
            ]);
    }

    /**
     * @Route("/history", name="portfolio_report_history")
     * @Method("GET")
     *
     * @param \App\Entity\Portfolio $portfolio
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @param \App\Finance\Api\ApiClientInterface $apiClient
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function history(
        Portfolio $portfolio,
        Request $request,
        EntityManagerInterface $entityManager,
        ApiClientInterface $apiClient
    ): Response {
        $this->abortIfNotOwnedByUser($this->getUser(), $portfolio);

        $intervals = HistoryReport::getIntervals();
        $interval = $request->get('interval', reset($intervals));

        return $this->render('portfolio/report/history.html.twig',
            [
                'intervals' => $intervals,
                'history' => new HistoryReport($entityManager, $portfolio, $apiClient, $interval),
            ]);
    }
}