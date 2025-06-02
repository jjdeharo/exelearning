<?php

namespace App\Entity\net\exelearning\Entity;

use App\Repository\net\exelearning\Repository\CrudTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'crud_template')]
#[ORM\Entity(repositoryClass: CrudTemplateRepository::class)]
class CrudTemplate extends BaseEntity
{
    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    protected string $name;

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
