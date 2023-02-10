<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'paniers')]
    private Collection $idArticle;

    public function __construct()
    {
        $this->idArticle = new ArrayCollection();
    }

     public function getId(): ?int
    {
        return $this->id;
    }

     /**
      * @return Collection<int, Article>
      */
     public function getIdArticle(): Collection
     {
         return $this->idArticle;
     }

     public function addIdArticle(Article $idArticle): self
     {
         if (!$this->idArticle->contains($idArticle)) {
             $this->idArticle->add($idArticle);
         }

         return $this;
     }

     public function removeIdArticle(Article $idArticle): self
     {
         $this->idArticle->removeElement($idArticle);

         return $this;
     }


}
