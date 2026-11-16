<?php

namespace App\Entity;

use App\Repository\PlatformSettingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

// Single row: platform-wide settings
#[ORM\Entity(repositoryClass: PlatformSettingRepository::class)]
class PlatformSetting
{
    public const DEFAULT_COMMISSION_RATE = 10;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Percentage (10 = 10 %)
    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 100)]
    private ?float $commissionRate = self::DEFAULT_COMMISSION_RATE;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommissionRate(): ?float
    {
        return $this->commissionRate;
    }

    public function setCommissionRate(?float $commissionRate): static
    {
        $this->commissionRate = $commissionRate;

        return $this;
    }
}
