<?php

namespace App\Entity;

use App\Entity\Timestamp\HasTimestampsInterface;
use App\Entity\User\OwnedByUserInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\Table("transactions")
 * @AppAssert\TransactionValidSellQuantity()
 */
class Transaction implements HasTimestampsInterface, OwnedByUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\JoinColumn(name="portfolio_id", referencedColumnName="id", nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Entity\Portfolio")
     * @var \App\Entity\Portfolio
     */
    private $portfolio;

    /**
     * @ORM\Column(type="string", length=5)
     * @Assert\Length(min=1, max=5)
     * @var string
     */
    private $symbol;

    /**
     * @ORM\Column(type="float")
     * @Assert\GreaterThan(0)
     * @var float
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $total;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->total < 0 ? 'sell' : 'buy';
    }

    /**
     * @return float
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \App\Entity\Portfolio
     */
    public function getPortfolio(): ?Portfolio
    {
        return $this->portfolio;
    }

    /**
     * @param \App\Entity\Portfolio $portfolio
     */
    public function setPortfolio(Portfolio $portfolio): void
    {
        $this->portfolio = $portfolio;
    }

    /**
     * @return string
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    /**
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setCreatedAt(DateTime $timestamp): void
    {
        $this->createdAt = $timestamp;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setUpdatedAt(DateTime $timestamp): void
    {
        $this->updatedAt = $timestamp;
    }

    /**
     * @param \App\Entity\User $user
     * @return bool
     */
    public function isOwnedByUser(User $user): bool
    {
        return $this->getPortfolio()->getUser()->getId() === $user->getId();
    }

    /**
     * @param string $operation
     */
    public function setOperation(string $operation): void
    {
        if (!in_array($operation, ['sell', 'buy'])) {
            throw new InvalidArgumentException("Invalid operation [$operation]");
        }

        if ($operation !== $this->getOperation()) {
            $this->setTotal($this->getTotal() * -1);
        }
    }
}
