<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Traits\CommonDate;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    /*operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete()
    ],*/
    normalizationContext: ['groups' => ['read']]
)]
#[Get()]
#[Post(
    inputFormats: ['multipart' => ['multipart/form-data']],
    denormalizationContext: ['groups' => ['write']]
)]
#[GetCollection()]
#[Put()]
#[Patch()]
#[Delete()]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
#[ApiFilter(DateFilter::class, properties: ['publishedAt' => DateFilter::INCLUDE_NULL_BEFORE_AND_AFTER])]
#[ApiFilter(OrderFilter::class, properties: ['id' => OrderFilter::DIRECTION_DESC, 'title' => 'DESC'])]
//#[ApiFilter(SearchFilter::class, properties: ['title' => 'exact', 'author' => 'exact'])]
class Book
{
    use CommonDate;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['write', 'read'])]
    #[ApiFilter(SearchFilter::class, strategy: ('partial'))]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Il faut avoir plus que {{ limit }} caractères',
        maxMessage: 'Il faut avoir moins que {{ limit }} caractères'
    )]
    #[Groups(['write', 'read'])]
    private ?string $author = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['write'])]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 25,
        max: 500,
        minMessage: 'Il faut avoir plus que {{ limit }} caractères',
        maxMessage: 'Il faut avoir moins que {{ limit }} caractères'
    )]
    #[Groups(['write'])]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $userId= null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'book')]
    #[Groups(['read'])]
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    #[Vich\UploadableField(mapping: 'book', fileNameProperty: 'imageName', size: 'imageSize')]
    #[Groups(['write'])]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBook($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBook() === $this) {
                $comment->setBook(null);
            }
        }

        return $this;
    }
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }
}
