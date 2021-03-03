<?php

namespace App\Entity;

use App\Repository\OptionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OptionsRepository::class)
 */
class Options
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Room::class, inversedBy="options")
     *
     */
    private $roomId;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="options_id")
     */
    private $id_room;

    public function __toString()
    {
        return $this->description;
    }

    public function __construct()
    {
        $this->roomId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Room[]
     */
    public function getRoomId(): Collection
    {
        return $this->roomId;
    }

    public function addRoomId(Room $roomId): self
    {
        if (!$this->roomId->contains($roomId)) {
            $this->roomId[] = $roomId;
        }

        return $this;
    }

    public function removeRoomId(Room $roomId): self
    {
        $this->roomId->removeElement($roomId);

        return $this;
    }

    public function getIdRoom(): ?Room
    {
        return $this->id_room;
    }

    public function setIdRoom(?Room $id_room): self
    {
        $this->id_room = $id_room;

        return $this;
    }
}
