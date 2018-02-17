<?php

namespace App\Entity\Timestamp;

use DateTime;

interface HasTimestampsInterface
{
    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime;

    /**
     * @param DateTime $timestamp
     */
    public function setCreatedAt(DateTime $timestamp): void;

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime;

    /**
     * @param DateTime $timestamp
     */
    public function setUpdatedAt(DateTime $timestamp): void;
}