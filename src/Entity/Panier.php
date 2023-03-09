<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
     /**
     * @Groups({"panier_list"})
     */
    private ?int $id = null;

    #[ORM\Column]
     /**
     * @Groups({"panier_list"})
     */
    private ?int $Prix = null;

    #[ORM\Column]
     /**
     * @Groups({"panier_list"})
     */
    private ?int $Quantity = null;



    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: "CASCADE")]

    private ?Commande $idCommande = null;

    #[ORM\Column]
     /**
     * @Groups({"panier_list"})
     */
    private ?bool $confirme = null;

    #[ORM\OneToMany(mappedBy: 'idPanier', targetEntity: PanierArticle::class)]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private Collection $panierArticles;



    public function __construct()
    {
        $this->panierArticles = new ArrayCollection();
    }

     public function getId(): ?int
    {
        return $this->id;
    }



     public function getPrix(): ?int
     {
         return $this->Prix;
     }

     public function setPrix(int $Prix): self
     {
         $this->Prix = $Prix;

         return $this;
     }

     public function getQuantity(): ?int
     {
         return $this->Quantity;
     }

     public function setQuantity(int $Quantity): self
     {
         $this->Quantity = $Quantity;

         return $this;
     }

     public function getIdCommande(): ?Commande
     {
         return $this->idCommande;
     }

     public function setIdCommande(?Commande $idCommande): self
     {
         $this->idCommande = $idCommande;

         return $this;
     }

     public function isConfirme(): ?bool
     {
         return $this->confirme;
     }

     public function setConfirme(bool $confirme): self
     {
         $this->confirme = $confirme;

         return $this;
     }

     /**
      * @return Collection<int, PanierArticle>
      */
     public function getPanierArticles(): Collection
     {
         return $this->panierArticles;
     }

     public function addPanierArticle(PanierArticle $panierArticle): self
     {
         if (!$this->panierArticles->contains($panierArticle)) {
             $this->panierArticles->add($panierArticle);
             $panierArticle->setIdPanier($this);
         }

         return $this;
     }

     public function removePanierArticle(PanierArticle $panierArticle): self
     {
         if ($this->panierArticles->removeElement($panierArticle)) {
             // set the owning side to null (unless already changed)
             if ($panierArticle->getIdPanier() === $this) {
                 $panierArticle->setIdPanier(null);
             }
         }

         return $this;
     }

     public function __toString()
     {
         return (String)$this->getId() ;
     }


}
