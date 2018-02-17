<?php

namespace App\EventListener;

use App\Entity\Timestamp\HasTimestampsInterface;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TimestampsAutoUpdaterEventSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->tryToUpdateTimestamps($args->getEntity());
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->tryToUpdateTimestamps($args->getEntity());
    }

    /**
     * @param mixed $entity
     */
    protected function tryToUpdateTimestamps($entity): void
    {
        if (!$entity instanceof HasTimestampsInterface) {
            return;
        }

        $now = new DateTime();

        $entity->setUpdatedAt($now);

        if (null === $entity->getCreatedAt()) {
            $entity->setCreatedAt($now);
        }
    }
}