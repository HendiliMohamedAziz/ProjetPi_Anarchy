<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @Groups({"club_list","club_details"})
     */
    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 4, minMessage: 'la longueur doit etre superieur a 4')]
    #[Assert\Regex(pattern: "/^[A-Za-z\s]+$/", message: "le nom ne peut contenir que des lettres et espaces.")]
    private ?string $nom = null;

    /**
     * @Groups({"club_list"})
     */
    #[ORM\Column(length: 255)]
    private ?string $localisation = null;
    /**
     * @Groups({"club_list","club_details"})
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'id_club', targetEntity: Participation::class)]
    private Collection $participations;
    /**
     * @Groups({"club_list"})
     */
    #[ORM\Column(length: 255)]
    private ?string $type_activite = null;

    #[ORM\ManyToOne(inversedBy: 'clubs')]
    private ?User $id_clubOwner = null;
    /* message="Le numéro de téléphone doit être au format international et commencer par le code de pays (+XXX)."*/
    /**
     * @Groups({"club_list"})
     */
    #[ORM\Column(length: 255)]

    #[Assert\Length(min: 10, minMessage: 'numero tres court')]
    #[Assert\Length(max: 15, maxMessage: 'longueur max dépassé')]
    #[Assert\Regex(pattern: "/^\+\d{1,3}\d{6,14}$/", message: "le numéro de telephone doit commencer par +XXX")]
    private ?string $telephone = null;
    /**
     * @Groups({"club_list"})
     */
    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column(length: 255)]
    private ?string $Prix = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?float $latitude = null;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
    public function getTypeActivite(): ?string
    {
        return $this->type_activite;
    }

    public function setTypeActivite(string $type_activite): self
    {
        $this->type_activite = $type_activite;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setIdClub($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getIdClub() === $this) {
                $participation->setIdClub(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->getNom();
    }

    public function getIdClubOwner(): ?User
    {
        return $this->id_clubOwner;
    }

    public function setIdClubOwner(?User $id_clubOwner): self
    {
        $this->id_clubOwner = $id_clubOwner;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->Prix;
    }

    public function setPrix(string $Prix): self
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
