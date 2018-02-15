<?php

namespace App\Entity\User;

use App\Entity\User;

interface OwnedByUserInterface
{

    /**
     * @param \App\Entity\User $user
     * @return bool
     */
    public function isOwnedByUser(User $user): bool;
}