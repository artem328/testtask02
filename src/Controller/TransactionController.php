<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\Transaction\TransactionCreateType;
use App\Form\Transaction\TransactionDeleteType;
use App\Form\Transaction\TransactionEditType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransactionController
 * @package App\Controller
 * @Route("/transactions")
 */
class TransactionController extends Controller
{
    use HasPaginationTrait;

    /**
     * @Route("/", name="transaction_index")
     * @Method("GET")
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $portfoliosQuery = $entityManager->getRepository(Transaction::class)
            ->createQueryBuilder('t')
            ->join('t.portfolio', 'p')
            ->where('p.user = :user_id')->setParameter('user_id', $this->getUser()->getId())
            ->getQuery();

        $paginator = $this->paginate($portfoliosQuery);

        return $this->render('transaction/index.html.twig', ['paginator' => $paginator]);
    }

    /**
     * @Route("/create", name="transaction_create")
     * @Method("GET|POST")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();

        $form = $this->createForm(TransactionCreateType::class, $transaction);
        $form->add('create', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_edit', ['transaction' => $transaction->getId()]);
        }

        return $this->render('transaction/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{transaction}", name="transaction_edit")
     * @Method("GET|PATCH")
     *
     * @param \App\Entity\Transaction $transaction
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Transaction $transaction, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TransactionEditType::class, $transaction);
        $form->add('update', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_edit', ['transaction' => $transaction->getId()]);
        }

        $deleteForm = $this->createForm(TransactionDeleteType::class);
        $deleteForm->add('delete', SubmitType::class);

        return $this->render('transaction/edit.html.twig',
            [
                'form' => $form->createView(),
                'deleteForm' => $deleteForm->createView(),
            ]);
    }

    /**
     * @Route("/{transaction}", name="transaction_delete")
     * @Method("DELETE")
     *
     * @param \App\Entity\Transaction $transaction
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($transaction);
        $entityManager->flush();

        return $this->redirectToRoute('transaction_index');
    }

}