<?php

namespace App\Form\Portfolio;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PortfolioDeleteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('DELETE');
    }

}