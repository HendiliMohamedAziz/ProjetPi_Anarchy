<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbr_grp = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $nbr_seance = null;

    #[ORM\OneToMany(mappedBy: 'seance', targetEntity: Reservation::class)]
    private Collection $id_R;

    public function __construct()
    {
        $this->id_R = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbrGrp(): ?int
    {
        return $this->nbr_grp;
    }

    public function setNbrGrp(int $nbr_grp): self
    {
        $this->nbr_grp = $nbr_grp;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNbrSeance(): ?int
    {
        return $this->nbr_seance;
    }

    public function setNbrSeance(int $nbr_seance): self
    {
        $this->nbr_seance = $nbr_seance;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getIdR(): Collection
    {
        return $this->id_R;
    }

    public function addIdR(Reservation $idR): self
    {
        if (!$this->id_R->contains($idR)) {
            $this->id_R->add($idR);
            $idR->setSeance($this);
        }

        return $this;
    }

    public function removeIdR(Reservation $idR): self
    {
        if ($this->id_R->removeElement($idR)) {
            // set the owning side to null (unless already changed)
            if ($idR->getSeance() === $this) {
                $idR->setSeance(null);
            }
        }

        return $this;
    }
}
