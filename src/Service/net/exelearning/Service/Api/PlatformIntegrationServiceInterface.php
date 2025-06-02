<?php

namespace App\Service\net\exelearning\Service\Api;

use App\Entity\net\exelearning\Entity\User;

interface PlatformIntegrationServiceInterface
{
    /**
     * Petition to upload elp to the platform.
     *
     * @param User   $user
     * @param array  $odeResultParameters
     * @param string $filePathName
     * @param string $jwtToken
     *
     * @return array content
     */
    public function platformPetitionSet($user, $odeResultParameters, $platformIntegrationUrlSet, $filePathName, $jwtToken);

    /**
     * Send petition to get the elp from platform.
     *
     * @param User   $user
     * @param string $odePlatformId
     * @param string $platformIntegrationUrlGet
     * @param string $jwtToken
     *
     * @return array $content
     */
    public function platformPetitionGet($user, $odePlatformId, $platformIntegrationUrlGet, $jwtToken);

    /**
     * Send petitions to get elp from the platform.
     *
     * @param User   $user
     * @param string $odePlatformId
     * @param string $platformIntegrationUrlGet
     * @param array  $secondTypePlatformClient
     *
     * @return array $content
     */
    public function secondTypePlatformPetitionGet($user, $odePlatformId, $platformIntegrationUrlGet, $secondTypePlatformClient);

    /**
     * Send petitions to upload elp to the platform.
     *
     * @param User   $user
     * @param array  $odeResultParameters
     * @param string $platformIntegrationUrlSet
     * @param string $filePathName
     * @param array  $secondTypePlatformClient
     * @param string $symfonyFullUrl
     *
     * @return array $content
     */
    public function secondTypePlatformPetitionSet($user, $odeResultParameters, $platformIntegrationUrlSet, $filePathName, $secondTypePlatformClient, $symfonyFullUrl);
}
