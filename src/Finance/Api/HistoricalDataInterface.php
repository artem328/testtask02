<?php

namespace App\Finance\Api;

use DateTime;

interface HistoricalDataInterface extends BaseQuoteInterface
{

    /**
     * @return \DateTime
     */
    public function getDate(): DateTime;

    /**
     * @return float
     */
    public function getOpenPrice(): float;

    /**
     * @return float
     */
    public function getClosePrice(): float;

    /**
     * @return float
     */
    public function getLowPrice(): float;

    /**
     * @return float
     */
    public function getHighPrice(): float;
}