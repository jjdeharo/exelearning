<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * TranslationListDto.
 */
class TranslationListDto extends BaseDto
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string[]
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = [];
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param array $translations
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    /**
     * @param string $key
     * @param string $translation
     */
    public function addTranslation($key, $translation)
    {
        $this->translations[$key] = $translation;
    }
}
