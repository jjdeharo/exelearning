<?php

namespace App\Entity\net\exelearning\Dto;

use App\Constants;

/**
 * UserDto.
 */
class UserDto extends BaseDto
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $initials;

    /**
     * @var string
     */
    protected $gravatarUrl;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the user's username.
     * Updates the Gravatar URL based on the email.
     *
     * Parameters used in the Gravatar URL:
     * - `s=96`: Sets the image size to 96x96 pixels.
     * - `d=mm`: Uses the 'mystery man' default image if no avatar is found.
     * - `r=g`: Restricts the image to the 'G' rating (safe for all audiences).
     *
     * @param string $username The user's email address
     *
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;

        // Automatically generate the Gravatar URL only if the username and base URL are not empty
        $trimmedUsername = trim($username);
        $baseUrl = trim(Constants::GRAVATAR_BASE_URL);

        if (!empty($trimmedUsername) && !empty($baseUrl)) {
            $hash = md5(strtolower($trimmedUsername));
            $this->gravatarUrl = $baseUrl.$hash.'?s=96&d=mm&r=g';
        } else {
            $this->gravatarUrl = null;
        }
    }

    /**
     * @return string
     */
    public function getInitials()
    {
        return $this->initials;
    }

    /**
     * @param string $initials
     */
    public function setInitials($initials)
    {
        $this->initials = $initials;
    }

    /**
     * Returns the user's Gravatar URL.
     *
     * @return string
     */
    public function getGravatarUrl()
    {
        return $this->gravatarUrl;
    }
}
