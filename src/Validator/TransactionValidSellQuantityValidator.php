<?php

namespace App\Validator;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class TransactionValidSellQuantity
 * @package App\Validator
 * @Annotation
 */
class TransactionValidSellQuantityValidator extends ConstraintValidator
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * TransactionValidSellQuantity constructor.
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $transaction The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($transaction, Constraint $constraint)
    {
        if ($transaction instanceof Transaction) {
            $owned = $this->entityManager
                ->getRepository(Transaction::class)
                ->countStocksInPortfolio($transaction->getPortfolio(), $transaction->getSymbol());

            if ($transaction->getQuantity() > $owned) {
                $this->context->addViolation('You don\'t have such quantity of '.$transaction->getSymbol().' in this portfolio.');
            }
        }
    }
}