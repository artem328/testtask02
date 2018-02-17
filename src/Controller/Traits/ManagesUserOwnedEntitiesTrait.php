<?php

namespace App\Controller\Traits;

use App\Entity\User;
use App\Entity\User\OwnedByUserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ManagesUserOwnedEntitiesTrait
{

    /**
     * @param \App\Entity\User $user
     * @param \App\Entity\User\OwnedByUserInterface $entity
     */
    public function abortIfNotOwnedByUser(User $user, OwnedByUserInterface $entity)
    {
        if (!$entity->isOwnedByUser($user)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, "Entity is not owned by user");
        }
    }
}