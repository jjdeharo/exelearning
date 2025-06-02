<?php

namespace App\Entity\net\exelearning\Entity;

use App\Repository\net\exelearning\Repository\OdePropertiesSyncRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'ode_properties_sync')]
#[ORM\Index(name: 'fk_ode_properties_1_idx', columns: ['ode_session_id'])]
#[ORM\Entity(repositoryClass: OdePropertiesSyncRepository::class)]
class OdePropertiesSync extends BaseEntity
{
    #[ORM\Column(name: 'ode_session_id', type: 'string', length: 20, nullable: false, options: ['fixed' => true])]
    protected string $odeSessionId;

    #[ORM\Column(name: 'ode_properties_key', type: 'string', length: 255, nullable: false)]
    protected string $key;

    #[ORM\Column(name: 'ode_properties_value', type: 'text', nullable: false)]
    protected string $value;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected ?string $description = null;

    protected string $category;

    protected array $groups;

    protected ?string $multipleId = null;

    protected ?int $multipleIndex = null;

    public function getOdeSessionId(): ?string
    {
        return $this->odeSessionId;
    }

    public function setOdeSessionId(string $odeSessionId): self
    {
        $this->odeSessionId = $odeSessionId;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups;
    }

    public function setGroups(?array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getMultipleId(): ?string
    {
        return $this->multipleId;
    }

    public function setMultipleId(?string $multipleId): self
    {
        $this->multipleId = $multipleId;

        return $this;
    }

    public function getMultipleIndex(): ?int
    {
        return $this->multipleIndex;
    }

    public function setMultipleIndex(?int $multipleIndex): self
    {
        $this->multipleIndex = $multipleIndex;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Loads key and value from properties constants config.
     *
     * @return self
     */
    public function loadFromPropertiesConfig(
        string $odeSessionId,
        string $configKey,
        array $configValues,
    ) {
        $this->setOdeSessionId($odeSessionId);
        $this->setKey($configKey);

        $value = isset($configValues['value']) ? $configValues['value'] : '';
        $this->setValue($value);

        $category = isset($configValues['category']) ? $configValues['category'] : '';
        $this->setCategory($category);

        $groups = isset($configValues['groups']) ? $configValues['groups'] : [];
        $this->setGroups($groups);

        $multipleId = null;
        $this->setMultipleId($multipleId);

        $multipleIndex = null;
        $this->setMultipleIndex($multipleIndex);

        return $this;
    }
}
