<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
// Mtn pour rajouter un slug dans une entité jen 'ai plus qu'a le mettre dans l'entité voulu avec le use
trait SlugTrait
{
    #[ORM\Column(length: 255)]
    private ?string $slug;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
