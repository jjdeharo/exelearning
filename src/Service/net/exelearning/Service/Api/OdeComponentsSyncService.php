<?php

namespace App\Service\net\exelearning\Service\Api;

use App\Constants;
use App\Controller\net\exelearning\Controller\Api\DefaultApiController;
use App\Entity\net\exelearning\Dto\OdeComponentsSyncBrokenLinksDto;
use App\Entity\net\exelearning\Dto\OdeComponentsSyncDto;
use App\Entity\net\exelearning\Dto\OdeComponentsSyncUsedFilesDto;
use App\Helper\net\exelearning\Helper\FileHelper;
use App\Util\net\exelearning\Util\FileUtil;
use App\Util\net\exelearning\Util\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OdeComponentsSyncService implements OdeComponentsSyncServiceInterface
{
    private $entityManager;
    private $container;
    private $fileHelper;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

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
    public function getBrokenLinks($symfonyFullUrl, $odeComponentsSync, $csv = 'false', $resourceReport = 'false')
    {
        // Init Dto
        $cleanBrokenLinks = new OdeComponentsSyncDto();
        $listUsedFiles = new OdeComponentsSyncDto();

        foreach ($odeComponentsSync as $odeComponentSync) {
            $htmlView = $odeComponentSync->getHtmlView();
            $odeComponentOrder = $odeComponentSync->getOdeComponentsSyncOrder();
            $odeComponentType = $odeComponentSync->getOdeIdeviceTypeName();
            $odePagComponent = $odeComponentSync->getOdePagStructureSync();

            // Check odePagComponent is not null
            if (null != $odePagComponent) {
                $odePagComponentBlockName = $odePagComponent->getBlockName();

                $odeNavComponent = $odePagComponent->getOdeNavStructureSync();

                //  Check odeNavComponent is not null
                if (null != $odeNavComponent) {
                    $odeNavComponentPagName = $odeNavComponent->getPageName();
                } else {
                    $odeNavComponentPagName = '';
                }
            } else {
                $odePagComponentBlockName = '';
            }

            if (!empty($htmlView)) {
                $matches = $this->getOdeHtmlLinks($htmlView);

                // Remove aphostrophes and count repeated
                $matches = $this->getOdeHtmlLinksWithoutAphostrophes($matches);

                // Remove links with blank spaces
                $matches = $this->getOdeHtmlLinksClean($matches);

                // Remove repeated links
                $matches = $this->getOdeHtmlLinksWithoutRepeated($matches);

                // Finally put the result in dto
                for ($i = 0; $i < count($matches['link']); ++$i) {
                    if (isset($matches['link'][$i])) {
                        // Variables Name and Times
                        $brokenLinkName = $matches['link'][$i];
                        $brokenLinkTimes = $matches['nTimes'][$i];

                        // Internal links
                        if ($this->isInternalLink($matches['link'][$i])) {
                            $internalLinkSource = $matches['link'][$i];

                            // Path to files dir
                            $filesDir = $this->fileHelper->getFilesDir();

                            // Path to the file
                            $relativeResourceFilePath = substr($internalLinkSource, 6);
                            $resourceFilePath = $filesDir.$relativeResourceFilePath;

                            $httpresponse = $this->getInternalFileHttpResponse($resourceFilePath);

                            if ('true' == $resourceReport) {
                                if (null == $httpresponse) {
                                    $this->getOdeSessionUsedFilesList(
                                        $listUsedFiles,
                                        $internalLinkSource,
                                        $resourceFilePath,
                                        $odePagComponentBlockName,
                                        $odeNavComponentPagName,
                                        $odeComponentOrder,
                                        $odeComponentType
                                    );
                                }
                            }
                        } else {
                            // Checks with a curlrequest if a link is broken or not
                            if (str_starts_with($matches['link'][$i], 'exe-node:')) {
                                $httpresponse = $this->isValidExeNodeLink($matches['link'][$i]) ? null : Util::CURL_ERROR_3;
                            } else {
                                $httpresponse = Util::sendCurlHttpRequest($matches['link'][$i]);
                            }
                        }

                        // Csv file dto broken links
                        if ('true' == $csv && 'false' == $resourceReport) {
                            $this->getOdeSessionBrokenLinks(
                                $cleanBrokenLinks,
                                $brokenLinkName,
                                $brokenLinkTimes,
                                $httpresponse,
                                $odePagComponentBlockName,
                                $odeNavComponentPagName,
                                $odeComponentOrder,
                                $odeComponentType
                            );
                        }

                        // Modal dto broken links
                        if ('false' == $csv && null != $httpresponse && 'false' == $resourceReport) {
                            $this->getOdeSessionBrokenLinks(
                                $cleanBrokenLinks,
                                $brokenLinkName,
                                $brokenLinkTimes,
                                $httpresponse,
                                $odePagComponentBlockName,
                                $odeNavComponentPagName,
                                $odeComponentOrder,
                                $odeComponentType
                            );
                        }
                    }
                }
            }
        }
        if ('true' == $resourceReport) {
            return $listUsedFiles;
        } else {
            if ([] === $cleanBrokenLinks->getBrokenLinks()) {
                $noBrokenLinksDto = new OdeComponentsSyncBrokenLinksDto();
                $noBrokenLinksDto->setBrokenLinks('No broken links found');
                $noBrokenLinksDto->setNTimesBrokenLinks(null);
                $noBrokenLinksDto->setBrokenLinksError(null);
                $noBrokenLinksDto->setPageNamesBrokenLinks('');
                $noBrokenLinksDto->setBlockNamesBrokenLinks('');
                $noBrokenLinksDto->setTypeComponentSyncBrokenLinks('');
                $noBrokenLinksDto->setOrderComponentSyncBrokenLinks('');

                $cleanBrokenLinks->addBrokenLinks($noBrokenLinksDto);
            }

            return $cleanBrokenLinks;
        }
    }

    /**
     * Check whether link is internal or not.
     *
     * @param string $link
     *
     * @return bool
     */
    private function isInternalLink($link)
    {
        if (Constants::FILES_DIR_NAME == substr($link, 0, 5)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get used file dto.
     *
     * @param object $listUsedFiles
     * @param string $internalLinkSource
     * @param string $resourceFilePath
     * @param string $odePagComponentBlockName
     * @param string $odeNavComponentPagName
     * @param string $odeComponentOrder
     * @param string $odeComponentType
     */
    private function getOdeSessionUsedFilesList($listUsedFiles, $internalLinkSource, $resourceFilePath, $odePagComponentBlockName, $odeNavComponentPagName, $odeComponentOrder, $odeComponentType)
    {
        $usedFiles = new OdeComponentsSyncUsedFilesDto();

        // Get Filename
        $dirPositionUsedFile = strrpos($internalLinkSource, '/');
        $usedFileName = substr($internalLinkSource, $dirPositionUsedFile + 1);

        // Get path to used file
        $filesStringPositionUsedFile = strpos($internalLinkSource, '/');
        $relativeSourceUsedFiles = substr($internalLinkSource, $filesStringPositionUsedFile + 1);
        $usedFilePath = $this->fileHelper->getFilesDir().$relativeSourceUsedFiles;

        // Get filesize
        $fileSize = filesize($resourceFilePath);
        $convertedFileSize = FileUtil::formatFilesize($fileSize);

        // set used file
        $usedFiles->setUsedFiles($usedFileName);
        $usedFiles->setUsedFilesPath($internalLinkSource);
        $usedFiles->setUsedFilesSize($convertedFileSize);
        $usedFiles->setPageNamesUsedFiles($odeNavComponentPagName);
        $usedFiles->setBlockNamesUsedFiles($odePagComponentBlockName);
        $usedFiles->setTypeComponentSyncUsedFiles($odeComponentType);
        $usedFiles->setOrderComponentSyncUsedFiles($odeComponentOrder);

        $listUsedFiles->addUsedFiles($usedFiles);
    }

    /**
     * Get used file dto.
     *
     * @param object $cleanBrokenLinks
     * @param string $brokenLinkName
     * @param string $brokenLinkTimes
     * @param string $httpresponse
     * @param string $odePagComponentBlockName
     * @param string $odeNavComponentPagName
     * @param string $odeComponentOrder
     * @param string $odeComponentType
     */
    private function getOdeSessionBrokenLinks($cleanBrokenLinks, $brokenLinkName, $brokenLinkTimes, $httpresponse, $odePagComponentBlockName, $odeNavComponentPagName, $odeComponentOrder, $odeComponentType)
    {
        $brokenLinks = new OdeComponentsSyncBrokenLinksDto();

        // set broken link
        $brokenLinks->setBrokenLinks($brokenLinkName);
        $brokenLinks->setNTimesBrokenLinks($brokenLinkTimes);
        $brokenLinks->setBrokenLinksError($httpresponse);
        $brokenLinks->setPageNamesBrokenLinks($odeNavComponentPagName);
        $brokenLinks->setBlockNamesBrokenLinks($odePagComponentBlockName);
        $brokenLinks->setTypeComponentSyncBrokenLinks($odeComponentType);
        $brokenLinks->setOrderComponentSyncBrokenLinks($odeComponentOrder);

        $cleanBrokenLinks->addBrokenLinks($brokenLinks);
    }

    /**
     * Get Links in HtmlView.
     *
     * @param string $htmlView
     *
     * @return array
     */
    private function getOdeHtmlLinks($htmlView)
    {
        // Check if have links
        preg_match_all('/(href|src)=("[^"]*")/', $htmlView, $match);

        // Catch matches
        $matches = [
            'link' => array_pop($match),
            'nTimes' => [],
        ];

        return $matches;
    }

    /**
     * Remove aphostrophes and count repeated.
     *
     * @param array $matches
     *
     * @return array
     */
    private function getOdeHtmlLinksWithoutAphostrophes($matches)
    {
        // Removes aphostrophes and counts the number of times a url is repeated
        for ($i = 0; $i < count($matches['link']); ++$i) {
            $matches['link'][$i] = str_replace('"', '', $matches['link'][$i]);
            $url = $matches['link'][$i];
            $nTimes = 0;

            foreach ($matches['link'] as $match) {
                if ($match == $url) {
                    ++$nTimes;
                }
            }

            $matches['nTimes'][$i] = $nTimes;
        }

        return $matches;
    }

    /**
     * Remove blank spaces.
     *
     * @param array $matches
     *
     * @return array
     */
    private function getOdeHtmlLinksClean($matches)
    {
        // Removes url and nTimes if is a space blank and reorder the array
        for ($i = count($matches['link']) - 1; $i >= 0; --$i) {
            $url = $matches['link'][$i];

            // Remove url if empty value or # value
            if ('' == $matches['link'][$i] || '#' == substr($matches['link'][$i], 0, 1)) {
                $key = array_search($url, $matches['link']);

                unset($matches['link'][$key]);
                unset($matches['nTimes'][$key]);
            }

            // Reorder array
            $matches['link'] = array_values($matches['link']);
            $matches['nTimes'] = array_values($matches['nTimes']);
        }

        return $matches;
    }

    /**
     * Remove repeated links in Idevice.
     *
     * @param array $matches
     *
     * @return array
     */
    private function getOdeHtmlLinksWithoutRepeated($matches)
    {
        // Removes a url and nTimes in case the link is repeated on the same Idevice, removes the first time it appears
        for ($i = 0; $i < count($matches['link']); ++$i) {
            if (isset($matches['link'][$i])) {
                foreach ($matches['link'] as $match) {
                    // Checks the keys to the repeated values in the array
                    $nTimesLink = array_keys($matches['link'], $match);

                    if (count($nTimesLink) > 1) {
                        foreach ($nTimesLink as $key) {
                            // Unset lower nTimes on matches
                            if ($matches['nTimes'][$key] < count($nTimesLink)) {
                                unset($matches['link'][$key]);
                                unset($matches['nTimes'][$key]);
                            }
                        }
                    }
                }
            }

            // Reorder array
            $matches['link'] = array_values($matches['link']);
            $matches['nTimes'] = array_values($matches['nTimes']);
        }

        return $matches;
    }

    /**
     * Get httpresponse of internal files.
     *
     * @param string $resourceFilePath
     *
     * @return int|null
     */
    private function getInternalFileHttpResponse($resourceFilePath)
    {
        // Check wheter file exist or not
        if (!file_exists($resourceFilePath)) {
            $httpresponse = DefaultApiController::STATUS_CODE_NOT_FOUND;
        } else {
            $httpresponse = null;
        }

        return $httpresponse;
    }

    /**
     * Validates internal exe-node:{id} link using Doctrine.
     */
    private function isValidExeNodeLink(string $url): bool
    {
        if (!str_starts_with($url, 'exe-node:')) {
            return true; // not an internal exe-node link
        }

        $id = substr($url, strlen('exe-node:'));

        try {
            $count = $this->entityManager
                ->getConnection()
                ->fetchOne('SELECT COUNT(*) FROM ode_nav_structure_sync WHERE ode_page_id = :id', ['id' => $id]);

            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
