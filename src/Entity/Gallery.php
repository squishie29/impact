<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GalleryRepository::class)
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("galleries")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("galleries")
     */
    private $imgpath;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="galleries")
     * @ORM\JoinColumn(nullable=false)
     */
    public $hotel_id;

    public function __toString()
    {
        return (string)$this->imgpath;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImgpath(): ?string
    {
        return $this->imgpath;
    }

    public function setImgpath(string $imgpath): self
    {
        $this->imgpath = $imgpath;

        return $this;
    }

    public function getHotelId(): ?Hotel
    {
        return $this->hotel_id;
    }

    public function setHotelId(?Hotel $hotel_id): self
    {
        $this->hotel_id = $hotel_id;

        return $this;
    }
}
