<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    /** Taux de TVA français applicables (en %). Float pour la comparaison stricte avec la valeur en base. */
    public const TVA_RATES = [20.0, 10.0, 5.5, 2.1];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Donnez un nom à votre produit.')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Décrivez votre produit (matière, coupe, entretien…).')]
    #[Assert\Length(min: 20, minMessage: 'La description doit faire au moins {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Indiquez un prix.')]
    #[Assert\Positive(message: 'Le prix doit être supérieur à 0.')]
    #[Assert\LessThan(value: 100000, message: 'Le prix doit être inférieur à {{ compared_value }} €.')]
    private ?float $prix_ht = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Choisissez un taux de TVA.')]
    #[Assert\Choice(choices: self::TVA_RATES, message: 'Taux de TVA invalide.')]
    private ?float $TVA = null;
    /**
     * @var list<string>
     */
     #[ORM\Column]
    private array $image = [];

    /**
     * @var Collection<int, Tags>
     */
    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'products')]
    private Collection $Tags;

    #[ORM\ManyToOne(inversedBy: 'Products')]
    #[Assert\NotNull(message: 'Choisissez une catégorie.')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?User $user = null;

    /**
     * @var Collection<int, Color>
     */
    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'products')]
    private Collection $colors;

    public function __construct()
    {
        $this->Tags = new ArrayCollection();
        $this->colors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getPrixHt(): ?float
    {
        return $this->prix_ht;
    }

    public function setPrixHt(?float $prix_ht): static
    {
        $this->prix_ht = $prix_ht;

        return $this;
    }

    public function getTVA(): ?float
    {
        return $this->TVA;
    }

    public function setTVA(?float $TVA): static
    {
        $this->TVA = $TVA;

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->Tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->Tags->contains($tag)) {
            $this->Tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        $this->Tags->removeElement($tag);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(array $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Color>
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(Color $color): static
    {
        if (!$this->colors->contains($color)) {
            $this->colors->add($color);
        }

        return $this;
    }

    public function removeColor(Color $color): static
    {
        $this->colors->removeElement($color);

        return $this;
    }
}
