<?php

namespace App\Service\net\exelearning\Service\Api;

use App\Entity\net\exelearning\Entity\User;

interface CurrentOdeUsersSyncChangesServiceInterface
{
    /**
     * Activate the user page syncUpdateFlag without being same page.
     *
     * @param string $odeSessionId
     * @param User   $odePageId
     * @param User   $user
     * @param string $actionType
     * @param string $userThemeValue
     */
    public function activatePageSyncUpdateFlag($odeSessionId, $odePageId, $user, $actionType, $userThemeValue);

    /**
     * Activate the user syncUpdateFlag.
     *
     * @param string $odeSessionId
     * @param string $odeIdeviceId
     * @param string $odeBlockId
     * @param User   $odePageId
     * @param User   $user
     * @param string $actionType
     */
    public function activateSyncUpdateFlag($odeSessionId, $odeIdeviceId, $odeBlockId, $odePageId, $user, $actionType, $destinationPageId);

    /**
     * Desactivates update flag and odeComponentIdUpdate from user.
     *
     * @param User $user
     */
    public function desactivateSyncUpdateFlag($user);

    /**
     * Empty synchronize rows on BBDD by user.
     *
     * @param User $user
     */
    public function removeSyncActionsByUser($user);

    /**
     * Get the user current ideviceDto to update.
     *
     * @param User   $user
     * @param string $currentOdeComponentIdForUser
     */
    public function getCurrentIdeviceDto($user, $currentOdeComponentIdForUser);

    /**
     * Get the user current block to update.
     *
     * @param User $user
     */
    public function getCurrentBlockDto($user, $currentOdeBlockIdForUser);

    /**
     * Get the user current page to update.
     *
     * @param User $user
     */
    public function getCurrentPageDto($user, $currentOdePageIdForUser);

    /**
     * Get another user of the session in order to apply theme to the rest of users.
     *
     * @param User   $user
     * @param string $odeSessionId
     */
    public function getAnotherUserSyncSession($user, $odeSessionId);
}
