<?php

namespace App\Entity\net\exelearning\Entity;

use App\Repository\net\exelearning\Repository\UserPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'user_preferences')]
#[ORM\Index(name: 'fk_user_preferences_1_idx', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: UserPreferencesRepository::class)]
class UserPreferences extends BaseEntity
{
    #[ORM\Column(name: 'user_id', type: 'string', length: 255, nullable: false)]
    protected string $userId;

    #[ORM\Column(name: 'user_preferences_key', type: 'string', length: 255, nullable: false)]
    protected string $key;

    #[ORM\Column(name: 'user_preferences_value', type: 'text', nullable: false)]
    protected string $value;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected ?string $description = null;

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

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

    public function setValue(string $value): self
    {
        $this->value = $value;

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
        string $userId,
        string $userPreferencesConfigKey,
        array $userPreferencesConfigValues,
    ) {
        $this->setUserId($userId);
        $this->setKey($userPreferencesConfigKey);

        $value = isset($userPreferencesConfigValues['value']) ? $userPreferencesConfigValues['value'] : '';
        $this->setValue($value);

        return $this;
    }
}
