<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Length(min:1,max:2,maxMessage:"Votre champ doit contenir au minimum 1 chiffre et au maximum 2 chiffres.")]

    #[Assert\Positive]
    #[Assert\NotBlank(message:"Veuillez entre le nombre du groupe")]
    private ?int $nbr_grp = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\ExpressionLanguageSyntax(message:"Votre champ  contient des caractères spéciaux.")]
    private ?string $description = null;


    #[Assert\Length(min:1,max:2,maxMessage:"Votre champ doit contenir au minimum 1 chiffre et au maximum 2 chiffres.")]
    #[Assert\Positive]
    #[ORM\Column]
    
    private ?int $nbr_seance = null;

    #[ORM\OneToMany(mappedBy: 'seance', targetEntity: Reservation::class)]
    private Collection $id_R;

    #[ORM\ManyToOne(inversedBy: 'coach')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    public function __construct()
    {
        $this->id_R = new ArrayCollection();
        $this->nbr_grp=0;
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
    public function __toString(): string
    {
        return $this->getId();    
    }
   
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }


}
