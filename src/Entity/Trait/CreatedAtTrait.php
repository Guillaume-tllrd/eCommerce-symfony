<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
// je crée un trait qui correspond à la propriété created_at dans coupons par ex, on prend également ces accesseurs . Ensuite je vais pouvoir aller les rajouter dans les entités avec use
trait CreatedAtTrait
{
    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
