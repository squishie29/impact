<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:rooms")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Regex(
     *     pattern="/^[1-4]+$/",
     *     message="maximum number of people in the room between 1 and 4"
     * )
     * @Groups("post:rooms")
     */
    private $nb_personnes;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:rooms")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("post:rooms")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value=0,
     *     message="price must be positive"
     * )
     * @Groups("post:rooms")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="rooms")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("post:rooms")
     * @MaxDepth(1)
     */
    private $idHotel;

    /**
     * @ORM\OneToMany(targetEntity=Options::class, mappedBy="room_id", orphanRemoval=true)
     * @Groups("post:rooms")
     */
    public $options;

    private $id2;

    /**
     * @ORM\OneToMany(targetEntity=ReservationHotel::class, mappedBy="roomId", orphanRemoval=true)
     */
    private $reservationHotels;

    public function __toString()
    {
        return $this->getId2();
    }

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->options_id = new ArrayCollection();
        $this->reservationHotels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getId2(): ?string
    {
        return $this->id;
    }

    public function getNbPersonnes(): ?int
    {
        return $this->nb_personnes;
    }

    public function setNbPersonnes(int $nb_personnes): self
    {
        $this->nb_personnes = $nb_personnes;

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


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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


    public function getIdHotel(): ?hotel
    {
        return $this->idHotel;
    }

    public function setIdHotel(?Hotel $idHotel): self
    {
        $this->idHotel = $idHotel;

        return $this;
    }


    /**
     * @return Collection|Options[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Options $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setRoomId($this);
        }

        return $this;
    }

    public function removeOption(Options $option): self
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getRoomId() === $this) {
                $option->setRoomId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReservationHotel[]
     */
    public function getReservationHotels(): Collection
    {
        return $this->reservationHotels;
    }

    public function addReservationHotel(ReservationHotel $reservationHotel): self
    {
        if (!$this->reservationHotels->contains($reservationHotel)) {
            $this->reservationHotels[] = $reservationHotel;
            $reservationHotel->setRoomId($this);
        }

        return $this;
    }

    public function removeReservationHotel(ReservationHotel $reservationHotel): self
    {
        if ($this->reservationHotels->removeElement($reservationHotel)) {
            // set the owning side to null (unless already changed)
            if ($reservationHotel->getRoomId() === $this) {
                $reservationHotel->setRoomId(null);
            }
        }

        return $this;
    }


}
