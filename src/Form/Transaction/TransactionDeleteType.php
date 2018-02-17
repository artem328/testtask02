<?php

namespace App\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TransactionDeleteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('DELETE');
    }

}