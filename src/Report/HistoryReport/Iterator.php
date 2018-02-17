<?php

namespace App\Report\HistoryReport;

use DateTime;
use Iterator as IteratorInterface;

class Iterator implements IteratorInterface
{

    /**
     * @var \App\Report\HistoryReport\Item
     */
    private $currentItem;

    /**
     * @var \App\Report\HistoryReport\Item
     */
    private $firstItem;

    /**
     * Iterator constructor.
     * @param \App\Report\HistoryReport\Item $firstItem
     */
    public function __construct(Item $firstItem)
    {
        $this->firstItem = $firstItem;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current(): ?Item
    {
        return $this->currentItem;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        $this->currentItem = $this->currentItem->getNext();
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): ?DateTime
    {
        return $this->currentItem ? $this->currentItem->getDate() : null;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return null !== $this->currentItem;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->currentItem = $this->firstItem;
    }
}