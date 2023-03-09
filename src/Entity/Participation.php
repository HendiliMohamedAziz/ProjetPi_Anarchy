<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;


#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    /**
     * @Groups({"participation_list"})
     */
    #[ORM\ManyToOne(inversedBy: 'participations', cascade: ["persist"])]
    #[SerializedName("id_club")]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Club $id_club = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?User $id_user = null;

    /**
     * @Groups({"participation_list"})
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateDebut = null;
    /**
     * @Groups({"participation_list"})
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateFin = null;

    #[ORM\Column]
    private ?bool $participated = null;

   




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClub(): ?Club
    {
        return $this->id_club;
    }



    public function setIdClub(?Club $id_club): self
    {
        $this->id_club = $id_club;

        return $this;
    }






    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->DateDebut;
    }



    public function setDateDebut(?\DateTimeInterface $DateDebut): self
    {
        $this->DateDebut = $DateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(?\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }


    public function date_to_string($date)
    {
        return $date->format('Y-m-d');
    }

    public function isParticipated(): ?bool
    {
        return $this->participated;
    }

    public function setParticipated(bool $participated): self
    {
        $this->participated = $participated;

        return $this;
    }

   
}
