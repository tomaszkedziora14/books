<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="category", cascade={"persist"})
     */
    private $book;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->book = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBook(): Collection
    {
        return $this->book;
    }

    /**
     * Set all books in the category.
     *
     * @param Book[] $book
     */
    public function setBook($book)
    {
        $this->book->clear();
        $this->book = new ArrayCollection($book);
    }

    /**
     * Add a book in the category.
     *
     * @param $book Book The product to associate
     */
    public function addBook($book)
    {
        if ($this->book->contains($book)) {
            return;
        }

        $this->book->add($book);
        $book->setCategory($this);
    }

    /**
     * @param Book $book
     */
    public function removeBook($book)
    {
        if (!$this->book->contains($book)) {
            return;
        }

        $this->book->removeElement($book);
        $book->removeCategory($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
