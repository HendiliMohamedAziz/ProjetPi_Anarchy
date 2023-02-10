<?php

namespace App\Entity;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    private ?Club $id_club = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    private ?User $id_user = null;


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

}
