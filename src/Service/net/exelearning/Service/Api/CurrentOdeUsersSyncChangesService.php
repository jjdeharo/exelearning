<?php

namespace App\Service\net\exelearning\Service\Api;

use App\Entity\net\exelearning\Dto\OdeComponentsSyncDto;
use App\Entity\net\exelearning\Dto\OdeNavStructureSyncDto;
use App\Entity\net\exelearning\Dto\OdePagStructureSyncDto;
use App\Entity\net\exelearning\Entity\CurrentOdeUsers;
use App\Entity\net\exelearning\Entity\CurrentOdeUsersSyncChanges;
use App\Entity\net\exelearning\Entity\OdeComponentsSync;
use App\Entity\net\exelearning\Entity\OdeNavStructureSync;
use App\Entity\net\exelearning\Entity\OdePagStructureSync;
use App\Entity\net\exelearning\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CurrentOdeUsersSyncChangesService implements CurrentOdeUsersSyncChangesServiceInterface
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Activate the user page syncUpdateFlag without being same page.
     *
     * @param string $odeSessionId
     * @param string $odePageId
     * @param User   $user
     * @param string $actionType
     * @param string $userThemeValue
     */
    public function activatePageSyncUpdateFlag($odeSessionId, $odePageId, $user, $actionType, $userThemeValue)
    {
        $currentOdeUsersRepository = $this->entityManager->getRepository(CurrentOdeUsers::class);
        $currentOdeUsers = $currentOdeUsersRepository->getCurrentUsers(null, null, $odeSessionId);
        $user = $user->getUsername();
        $actualCurrentOdeUser = $currentOdeUsersRepository->getCurrentSessionForUser($user);
        foreach ($currentOdeUsers as $currentOdeUser) {
            $concurrentUser = $currentOdeUser->getUser();
            if ($concurrentUser !== $user) {
                $currentOdeUser->setSyncUpdateFlag(true);
                if (isset($odePageId)) {
                    // Generate new entity for each user on current_ode_user_sync_changes table
                    $currentOdeUserSyncChange = new CurrentOdeUsersSyncChanges();

                    $currentOdeUserSyncChange->setOdePageIdUpdate($odePageId);
                    $currentOdeUserSyncChange->setStyleThemeIdUpdate($userThemeValue);
                    $currentOdeUserSyncChange->setActionTypeUpdate($actionType);
                    $currentOdeUserSyncChange->setUserUpdate($concurrentUser);

                    $this->entityManager->persist($currentOdeUser);
                    $this->entityManager->persist($currentOdeUserSyncChange);
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Activate the user syncUpdateFlag.
     *
     * @param string $odeSessionId
     * @param string $odeIdeviceId
     * @param string $odeBlockId
     * @param string $odePageId
     * @param User   $user
     * @param string $actionType
     */
    public function activateSyncUpdateFlag(
        $odeSessionId,
        $odeIdeviceId,
        $odeBlockId,
        $odePageId,
        $user,
        $actionType,
        $destinationPageId,
    ) {
        $currentOdeUsersRepository = $this->entityManager->getRepository(CurrentOdeUsers::class);
        $currentOdeUsers = $currentOdeUsersRepository->getCurrentUsers(null, null, $odeSessionId);
        $user = $user->getUsername();
        $actualCurrentOdeUser = $currentOdeUsersRepository->getCurrentSessionForUser($user);
        $actualCurrentOdeUserPageId = $actualCurrentOdeUser->getCurrentPageId();
        foreach ($currentOdeUsers as $currentOdeUser) {
            $concurrentUser = $currentOdeUser->getUser();
            if ($concurrentUser !== $user) {
                // Same page or if destination page is set same page as destination
                if (
                    $currentOdeUser->getCurrentPageId() == $actualCurrentOdeUserPageId
                    || $currentOdeUser->getCurrentPageId() == $destinationPageId
                ) {
                    // Generate new entity for each user on current_ode_user_sync_changes table
                    $currentOdeUserSyncChange = new CurrentOdeUsersSyncChanges();
                    $currentOdeUser->setSyncUpdateFlag(true);
                    if (isset($odeIdeviceId)) {
                        $currentOdeUserSyncChange->setOdeComponentIdUpdate($odeIdeviceId);
                    }
                    if (isset($odeBlockId)) {
                        $currentOdeUserSyncChange->setOdeBlockIdUpdate($odeBlockId);
                    }
                    if (isset($odePageId)) {
                        $currentOdeUserSyncChange->setOdePageIdUpdate($odePageId);
                    }
                    if (isset($destinationPageId)) {
                        $currentOdeUserSyncChange->setDestinationPageIdUpdate($destinationPageId);
                    }
                    $currentOdeUserSyncChange->setActionTypeUpdate($actionType);
                    $currentOdeUserSyncChange->setUserUpdate($concurrentUser);

                    // Persist sync change for user
                    $this->entityManager->persist($currentOdeUserSyncChange);
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Desactivates update flag and odeComponentIdUpdate from user.
     *
     * @param User $user
     */
    public function desactivateSyncUpdateFlag($user)
    {
        // Get currentOdeUser
        $currentOdeUsersRepository = $this->entityManager->getRepository(CurrentOdeUsers::class);
        $currentSessionForUser = $currentOdeUsersRepository->getCurrentSessionForUser($user->getUsername());

        $currentSessionForUser->setSyncUpdateFlag(false);
        $currentSessionForUser->setOdeComponentIdUpdate(null);
        $currentSessionForUser->setOdeBlockIdUpdate(null);
        $currentSessionForUser->setOdePageIdUpdate(null);
        $currentSessionForUser->setDestinationPageIdUpdate(null);
        $currentSessionForUser->setActionTypeUpdate(null);

        // Delete currentOdeUsersSyncChanges rows by user
        $currentOdeUsersSyncChangesRepository = $this->entityManager->getRepository(CurrentOdeUsersSyncChanges::class);
        $deletedSyncChanges = $currentOdeUsersSyncChangesRepository->removeCurrentOdeUsersSyncChangesByUser($user->getUsername());

        $this->entityManager->persist($currentSessionForUser);
        $this->entityManager->flush();
    }

    /**
     * Empty synchronize rows on BBDD by user.
     *
     * @param User $user
     */
    public function removeSyncActionsByUser($user)
    {
        // Delete currentOdeUsersSyncChanges rows by user
        $currentOdeUsersSyncChangesRepository = $this->entityManager->getRepository(CurrentOdeUsersSyncChanges::class);
        $deletedSyncChanges = $currentOdeUsersSyncChangesRepository->removeCurrentOdeUsersSyncChangesByUser($user->getUsername());

        $this->entityManager->flush();
    }

    /**
     * Get the user current ideviceDto to update.
     *
     * @param User $user
     */
    public function getCurrentIdeviceDto($user, $currentOdeComponentIdForUser)
    {
        $currentOdeUsersRepository = $this->entityManager->getRepository(CurrentOdeUsers::class);
        $currentOdeSessionForUser = $currentOdeUsersRepository->getCurrentSessionForUser(
            $user->getUserIdentifier()
        );

        $odeComponentsSyncRepository = $this->entityManager->getRepository(OdeComponentsSync::class);
        $userCurrentOdeComponentSync = $odeComponentsSyncRepository->findBy(
            ['odeIdeviceId' => $currentOdeComponentIdForUser]
        );

        $userCurrentOdeComponentDto = new OdeComponentsSyncDto();
        $userCurrentOdeComponentDto->loadFromEntity($userCurrentOdeComponentSync[0], true);

        return $userCurrentOdeComponentDto;
    }

    /**
     * Get the user current block to update.
     *
     * @param User $user
     */
    public function getCurrentBlockDto($user, $currentOdeBlockIdForUser)
    {
        $currentOdeUsersRepository = $this->entityManager->getRepository(CurrentOdeUsers::class);
        $currentOdeSessionForUser = $currentOdeUsersRepository->getCurrentSessionForUser(
            $user->getUserIdentifier()
        );

        $odeComponentsSyncRepository = $this->entityManager->getRepository(OdePagStructureSync::class);
        $odePagStructureSync = $odeComponentsSyncRepository->findBy(
            ['odeBlockId' => $currentOdeBlockIdForUser]
        );

        $userCurrentOdeBlockDto = new OdePagStructureSyncDto();
        $userCurrentOdeBlockDto->loadFromEntity($odePagStructureSync[0], true, true, true);

        return $userCurrentOdeBlockDto;
    }

    /**
     * Get the user current page to update.
     *
     * @param User $user
     */
    public function getCurrentPageDto($user, $currentOdePageIdForUser)
    {
        $currentOdeUsersRepository = $this->entityManager->getRepository(CurrentOdeUsers::class);
        $currentOdeSessionForUser = $currentOdeUsersRepository->getCurrentSessionForUser(
            $user->getUserIdentifier()
        );

        $odeComponentsSyncRepository = $this->entityManager->getRepository(OdeNavStructureSync::class);
        $odeNavStructureSync = $odeComponentsSyncRepository->findBy(['odePageId' => $currentOdePageIdForUser]);

        $userCurrentOdePageDto = new OdeNavStructureSyncDto();
        // Check if page isn't root or doesn't exist
        if (!empty($odeNavStructureSync[0])) {
            $userCurrentOdePageDto->loadFromEntity($odeNavStructureSync[0], true, true, true, true, true);
        }

        return $userCurrentOdePageDto;
    }

    /**
     * Get the user first created of the session in order to apply theme to the rest of users.
     *
     * @param User   $user
     * @param string $odeSessionId
     */
    public function getAnotherUserSyncSession($user, $odeSessionId)
    {
        $currentOdeUsersRepository = $this->entityManager->getRepository(CurrentOdeUsers::class);
        $currentOdeUsers = $currentOdeUsersRepository->getCurrentUsers(null, null, $odeSessionId);
        $userRepository = $this->entityManager->getRepository(User::class);
        $username = $user->getUsername();

        foreach ($currentOdeUsers as $currentOdeUser) {
            $concurrentUser = $currentOdeUser->getUser();
            if ($concurrentUser !== $username) {
                // Get user form database user
                $user = $userRepository->findBy(['email' => $concurrentUser]);
                if (!empty($user)) {
                    return $user[0];
                }
            }
        }

        // In case error return same user
        $sameUser = $userRepository->findBy(['email' => $username]);
        if (!empty($sameUser)) {
            return $sameUser[0];
        } else {
            return false;
        }
    }
}
