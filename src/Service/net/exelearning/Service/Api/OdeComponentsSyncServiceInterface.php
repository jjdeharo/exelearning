<?php

namespace App\Service\net\exelearning\Service\Api;

interface OdeComponentsSyncServiceInterface
{
    /**
     * Gets the broken links on the list of ode components passed.
     *
     * @param string $symfonyFullUrl
     * @param array  $odeComponentsSync
     * @param string $csv
     * @param string $resourceReport
     *
     * @return array $cleanBrokenLinks
     */
    public function getBrokenLinks($symfonyFullUrl, $odeComponentsSync, $csv = 'false', $resourceReport = 'false');
}
