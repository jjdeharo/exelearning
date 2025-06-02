<?php

namespace App\Service\net\exelearning\Service\Api;

interface OdeOperationsLogServiceInterface
{
    /**
     * Get data of ode operation log and insert into bbdd.
     *
     * @param string $odeSessionId
     * @param string $user
     * @param string $actionType
     * @param string $sourceId
     * @param string $destinationId
     * @param string $additionalData
     *
     * @return bool
     */
    public function insertOperation($odeSessionId, $user, $actionType, $sourceId, $destinationId, $additionalData);

    /**
     * Set active flag from last operation to false.
     *
     * @return array $response
     */
    public function setActiveFlagFromLastOdeOperationLog();

    /**
     * Get last operation action and returns a response depending the action.
     *
     * @param User $user
     *
     * @return array $response
     */
    public function getActionFromLastOdeOperationLogFromUser($user);
}
