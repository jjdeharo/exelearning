<?php

namespace App\Entity\net\exelearning\Entity;

use App\Repository\net\exelearning\Repository\CurrentOdeUsersSyncChangesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'current_ode_users_sync_changes')]
#[ORM\Entity(repositoryClass: CurrentOdeUsersSyncChangesRepository::class)]
class CurrentOdeUsersSyncChanges extends BaseEntity
{
    #[ORM\Column(name: 'ode_page_id_update', type: 'string', length: 64, nullable: true)]
    protected ?string $odePageIdUpdate = null;

    #[ORM\Column(name: 'ode_block_id_update', type: 'string', length: 64, nullable: true)]
    protected ?string $odeBlockIdUpdate = null;

    #[ORM\Column(name: 'ode_component_id_update', type: 'string', length: 64, nullable: true)]
    protected ?string $odeComponentIdUpdate = null;

    #[ORM\Column(name: 'destination_page_id_update', type: 'string', length: 64, nullable: true)]
    protected ?string $destinationPageIdUpdate = null;

    #[ORM\Column(name: 'style_theme_id_update', type: 'string', length: 64, nullable: true)]
    protected ?string $styleThemeIdUpdate = null;

    #[ORM\Column(name: 'action_type_update', type: 'string', length: 64, nullable: true)]
    protected ?string $actionTypeUpdate = null;

    #[ORM\Column(name: 'user_update', type: 'string', length: 128, nullable: true)]
    protected ?string $userUpdate = null;

    public function getOdePageIdUpdate(): ?string
    {
        return $this->odePageIdUpdate;
    }

    public function setOdePageIdUpdate(?string $odePageIdUpdate): self
    {
        $this->odePageIdUpdate = $odePageIdUpdate;

        return $this;
    }

    public function getOdeBlockIdUpdate(): ?string
    {
        return $this->odeBlockIdUpdate;
    }

    public function setOdeBlockIdUpdate(?string $odeBlockIdUpdate): self
    {
        $this->odeBlockIdUpdate = $odeBlockIdUpdate;

        return $this;
    }

    public function getOdeComponentIdUpdate(): ?string
    {
        return $this->odeComponentIdUpdate;
    }

    public function setOdeComponentIdUpdate(?string $odeComponentIdUpdate): self
    {
        $this->odeComponentIdUpdate = $odeComponentIdUpdate;

        return $this;
    }

    public function getDestinationPageIdUpdate(): ?string
    {
        return $this->destinationPageIdUpdate;
    }

    public function setDestinationPageIdUpdate(?string $destinationPageIdUpdate): self
    {
        $this->destinationPageIdUpdate = $destinationPageIdUpdate;

        return $this;
    }

    public function getStyleThemeIdUpdate(): ?string
    {
        return $this->styleThemeIdUpdate;
    }

    public function setStyleThemeIdUpdate(?string $styleThemeIdUpdate): self
    {
        $this->styleThemeIdUpdate = $styleThemeIdUpdate;

        return $this;
    }

    public function getActionTypeUpdate(): ?string
    {
        return $this->actionTypeUpdate;
    }

    public function setActionTypeUpdate(?string $actionTypeUpdate): self
    {
        $this->actionTypeUpdate = $actionTypeUpdate;

        return $this;
    }

    public function getUserUpdate(): ?string
    {
        return $this->userUpdate;
    }

    public function setUserUpdate(?string $userUpdate): self
    {
        $this->userUpdate = $userUpdate;

        return $this;
    }
}
