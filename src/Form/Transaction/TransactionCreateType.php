<?php

namespace App\Form\Transaction;

use App\Entity\Portfolio;
use App\Entity\Transaction;
use App\Finance\Api\ApiClientInterface;
use App\Form\EventListener\TransactionEventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionCreateType extends AbstractType
{

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Finance\Api\ApiClientInterface
     */
    private $financeApiClient;

    /**
     * TransactionCreateType constructor.
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Finance\Api\ApiClientInterface $financeApiClient
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $entityManager,
        ApiClientInterface $financeApiClient
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->financeApiClient = $financeApiClient;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('portfolio',
            EntityType::class,
            [
                'class' => Portfolio::class,
                'choices' => $this->entityManager->getRepository(Portfolio::class)->findAllOfUser($options['user']),
                'choice_label' => function (Portfolio $portfolio) {
                    return $portfolio->getName();
                },
            ])
            ->add('operation',
                ChoiceType::class,
                [
                    'choices' => [
                        'Buy' => 'buy',
                        'Sell' => 'sell',
                    ],
                ])
            ->add('symbol', TextType::class)
            ->add('price',
                NumberType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Leave empty to get price automatically (if available)'],
                ])
            ->add('quantity',
                NumberType::class,
                [
                    'attr' => ['min' => 1],
                ])
            ->addEventSubscriber(new TransactionEventSubscriber($this->financeApiClient));
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $token = $this->tokenStorage->getToken();

        $resolver->setDefaults([
            'data_class' => Transaction::class,
            'user' => $token ? $token->getUser() : null,
        ]);
    }
}