<?php

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HotelRepository::class)
 */
class Hotel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9]([a-zA-Z0-9_])+$/",
     *     message="name only alpha numerals"
     * )
     * @Groups("post:read")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Regex(
     *     pattern="/^[1-7]+$/",
     *     message="stars between 1 and 7"
     * )
     * @Groups("post:read")
     */
    private $stars;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:read")
     */
    private $adress;

    /**
     * @ORM\OneToMany(targetEntity=Room::class, mappedBy="idHotel", orphanRemoval=true)
     * @Groups("post:read")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity=Gallery::class, mappedBy="hotel_id", orphanRemoval=true)
     * @Groups("post:read")
     */
    private $galleries;

    public function __toString()
    {
        return $this->name;
    }
    public function __construct()
    {
        $this->rooms = new ArrayCollection();
        $this->galleries = new ArrayCollection();
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

    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function setStars(int $stars): self
    {
        $this->stars = $stars;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * @return Collection|Room[]
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms[] = $room;
            $room->setIdHotel($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getIdHotel() === $this) {
                $room->setIdHotel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Gallery[]
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(Gallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setHotelId($this);
        }

        return $this;
    }

    public function removeGallery(Gallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getHotelId() === $this) {
                $gallery->setHotelId(null);
            }
        }

        return $this;
    }
}
