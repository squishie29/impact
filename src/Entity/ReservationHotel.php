<?php

namespace App\Entity;

use App\Repository\ReservationHotelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReservationHotelRepository::class)
 */
class ReservationHotel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("reservationH")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="reservationHotels")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("reservationH")
     * @MaxDepth(4)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="reservationHotels")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("reservationH")
     * @MaxDepth(4)
     */
    private $roomId;

    /**
     * @ORM\Column(type="date")
     * @Groups("reservationH")
     */
    private $debut;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan(propertyPath="debut")
     * @Groups("reservationH")
     */
    private $fin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("reservationH")
     */
    private $confirmation="non valide";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?Utilisateur
    {
        return $this->userId;
    }

    public function setUserId(?Utilisateur $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRoomId(): ?Room
    {
        return $this->roomId;
    }

    public function setRoomId(?Room $roomId): self
    {
        $this->roomId = $roomId;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }


    public function getConfirmation(): ?string
    {
        return $this->confirmation;
    }

    public function setConfirmation(string $confirmation): self
    {
        $this->confirmation = $confirmation;

        return $this;
    }

}
