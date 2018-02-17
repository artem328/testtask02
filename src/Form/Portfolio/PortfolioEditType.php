<?php

namespace App\Form\Portfolio;

use Symfony\Component\Form\FormBuilderInterface;

class PortfolioEditType extends PortfolioCreateType
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