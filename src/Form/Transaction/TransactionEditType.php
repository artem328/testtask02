<?php

namespace App\Form\Transaction;

use Symfony\Component\Form\FormBuilderInterface;

class TransactionEditType extends TransactionCreateType
{

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setMethod('PATCH');
    }
}