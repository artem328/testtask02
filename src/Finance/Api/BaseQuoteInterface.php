<?php

namespace App\Finance\Api;

interface BaseQuoteInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getSymbol(): string;
}