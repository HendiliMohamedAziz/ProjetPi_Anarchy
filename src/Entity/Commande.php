<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    /**
     * @Groups({"commande_list"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
/**
     * @Groups({"commande_list"})
     */
    #[ORM\Column]
    private ?int $montant = null;
    /**
     * @Groups({"commande_list"})
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete:"CASCADE")]

    private ?Panier $idPanier = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete:"CASCADE")]
    private ?BillingAddress $idAddress = null;
    /**
     * @Groups({"commande_list"})
     */
    #[ORM\Column]
    private ?bool $ConfirmeAdmin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getIdAddress(): ?BillingAddress
    {
        return $this->idAddress;
    }

    public function setIdAddress(?BillingAddress $idAddress): self
    {
        $this->idAddress = $idAddress;

        return $this;
    }

    public function isConfirmeAdmin(): ?bool
    {
        return $this->ConfirmeAdmin;
    }

    public function setConfirmeAdmin(bool $ConfirmeAdmin): self
    {
        $this->ConfirmeAdmin = $ConfirmeAdmin;

        return $this;
    }
    
}
