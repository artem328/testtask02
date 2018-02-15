<?php

namespace App\Form\EventListener;

use App\Finance\Api\ApiClientInterface;
use App\Finance\Api\ApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TransactionEventSubscriber implements EventSubscriberInterface
{

    /**
     * @var \App\Finance\Api\ApiClientInterface
     */
    private $financeApiClient;

    /**
     * TransactionEventSubscriber constructor.
     * @param \App\Finance\Api\ApiClientInterface $financeApiClient
     */
    public function __construct(ApiClientInterface $financeApiClient)
    {
        $this->financeApiClient = $financeApiClient;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSetData',
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $transaction = $event->getData();
        $form = $event->getForm();
        $symbol = $transaction['symbol'];

        if (!$transaction['price'] && $symbol) {
            try {
                if ($quote = $this->financeApiClient->getQuote($symbol)) {
                    $transaction['price'] = $quote->getMarketPrice();
                }
            } catch (ApiException $exception) {
            }
        }

        if ($transaction['price'] && $transaction['quantity']) {
            $multiplier = 'sell' === $transaction['operation'] ? -1 : 1;
            $form->add('total', NumberType::class);
            $transaction['total'] = (float)($transaction['price'] * $transaction['quantity']) * $multiplier;
        }

        if (!$transaction['price']) {
            // If we couldn't find price from stock api
            // then let user insert price manually
            $transaction['price'] = 0;
        }

        $event->setData($transaction);
    }
}