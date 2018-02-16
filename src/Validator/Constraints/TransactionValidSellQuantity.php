<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class TransactionValidSellQuantity
 * @package App\Validator
 * @Annotation
 */
class TransactionValidSellQuantity extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return 'transaction_valid_sell_quantity';
    }

    /**
     * @return string
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}