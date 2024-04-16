<?php

namespace App\Entity;

use App\Repository\PrintIORepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrintIORepository::class)]
class PrintIO
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2048)]
    private ?string $input = null;

    #[ORM\Column(length: 65535, type: 'text',  nullable: true, options: ['default' => null])]
    private ?string $output = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
    private ?\DateTimeInterface $end = null;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'printios', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?printjob $printjob = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function setInput(string $input): static
    {
        $this->input = $input;

        return $this;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(?string $output): static
    {
        $this->output = $output;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPrintjob(): ?printjob
    {
        return $this->printjob;
    }

    public function setPrintjob(?printjob $printjob): static
    {
        $this->printjob = $printjob;

        return $this;
    }
}
