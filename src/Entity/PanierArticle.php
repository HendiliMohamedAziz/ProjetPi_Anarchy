<?php

namespace App\Entity;

use App\Repository\PanierArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierArticleRepository::class)]
class PanierArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'panierArticles')]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private ?Panier $idPanier = null;

    #[ORM\Column]
    private ?int $Quantity = null;

    #[ORM\ManyToOne(inversedBy: 'panierArticles')]
    private ?Article $idArticle = null;



    public function __construct()
    {
    }




    public function getId(): ?int
    {
        return $this->id;
    }


    public function getIdPanier(): ?Panier
    {
        return $this->idPanier;
    }

    public function setIdPanier(?Panier $idPanier): self
    {
        $this->idPanier = $idPanier;

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

    public function getIdArticle(): ?Article
    {
        return $this->idArticle;
    }

    public function setIdArticle(?Article $idArticle): self
    {
        $this->idArticle = $idArticle;

        return $this;
    }







}
