<?php

namespace App\Entity;

use App\Entity\PrintIO;
use App\Repository\PrintJobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrintJobRepository::class)]
class PrintJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $device = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    /**
     * @var Collection<int, Printio>
     */
    #[ORM\OneToMany(targetEntity: Printio::class, mappedBy: 'printjob', orphanRemoval: true)]
    private Collection $printios;

    public function __construct()
    {
        $this->printios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): static
    {
        $this->device = $device;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return Collection<int, Printio>
     */
    public function getPrintios(): Collection
    {
        return $this->printios;
    }

    public function addPrintio(Printio $printio): static
    {
        if (!$this->printios->contains($printio)) {
            $this->printios->add($printio);
            $printio->setPrintjob($this);
        }

        return $this;
    }

    public function removePrintio(Printio $printio): static
    {
        if ($this->printios->removeElement($printio)) {
            // set the owning side to null (unless already changed)
            if ($printio->getPrintjob() === $this) {
                $printio->setPrintjob(null);
            }
        }

        return $this;
    }
}
