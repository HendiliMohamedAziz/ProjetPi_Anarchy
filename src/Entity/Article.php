<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
     /**
     * @Groups({"panier_list"})
     */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
     /**
     * @Groups({"panier_list"})
     */
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
     /**
     * @Groups({"panier_list"})
     */
    private ?string $description = null;

    #[ORM\Column(length: 255)]
     /**
     * @Groups({"panier_list"})
     */
    private ?string $image = null;

    #[ORM\Column]
     /**
     * @Groups({"panier_list"})
     */
    private ?int $prix = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'idArticle')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'idArticle', targetEntity: PanierArticle::class)]
    private Collection $panierArticles;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->panierArticles = new ArrayCollection();
 
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setArticle($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getArticle() === $this) {
                $commentaire->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addIdArticle($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeIdArticle($this);
        }

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
            $panierArticle->setIdArticle($this);
        }

        return $this;
    }

    public function removePanierArticle(PanierArticle $panierArticle): self
    {
        if ($this->panierArticles->removeElement($panierArticle)) {
            // set the owning side to null (unless already changed)
            if ($panierArticle->getIdArticle() === $this) {
                $panierArticle->setIdArticle(null);
            }
        }

        return $this;
    }





   
}
