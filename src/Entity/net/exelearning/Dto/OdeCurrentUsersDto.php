<?php

namespace App\Entity\net\exelearning\Dto;

/**
 * OdeCurrentUsersDto.
 */
class OdeCurrentUsersDto extends BaseDto
{
    /**
     * @var string
     */
    protected $odeId;

    /**
     * @var string
     */
    protected $odeVersionId;

    /**
     * @var string
     */
    protected $odeSessionId;

    /**
     * @var UserDto[]
     */
    protected $currentUsers;

    public function __construct()
    {
        $this->currentUsers = [];
    }

    /**
     * @return string
     */
    public function getOdeId()
    {
        return $this->odeId;
    }

    /**
     * @param string $odeId
     */
    public function setOdeId($odeId)
    {
        $this->odeId = $odeId;
    }

    /**
     * @return string
     */
    public function getOdeVersionId()
    {
        return $this->odeVersionId;
    }

    /**
     * @param string $odeVersionId
     */
    public function setOdeVersionId($odeVersionId)
    {
        $this->odeVersionId = $odeVersionId;
    }

    /**
     * @return string
     */
    public function getOdeSessionId()
    {
        return $this->odeSessionId;
    }

    /**
     * @param string $odeSessionId
     */
    public function setOdeSessionId($odeSessionId)
    {
        $this->odeSessionId = $odeSessionId;
    }

    /**
     * @return array
     */
    public function getCurrentUsers()
    {
        return $this->currentUsers;
    }

    /**
     * @param array $currentUsers
     */
    public function setCurrentUsers($currentUsers)
    {
        $this->currentUsers = $currentUsers;
    }

    /**
     * @param string $currentUserName
     */
    public function addCurrentUser($currentUserName)
    {
        $currentUser = new UserDto();
        $currentUser->setUsername($currentUserName);

        // We set the initial to the first char of userName
        $currentUser->setInitials(mb_substr($currentUser->getUserName(), 0, 1));

        $this->currentUsers[] = $currentUser;

        // Check for duplicates in initials
        $this->setInitialsForUsers();
    }

    /**
     * Checks all the users to set their initials avoiding duplicates.
     */
    private function setInitialsForUsers()
    {
        $currentNumberOfChars = 0;
        $maxNumberOfChars = 1; // maximum number of chars to return for initial

        // If $maxNumberOfChars is 1 we already have the result
        if ($maxNumberOfChars > 1) {
            do {
                $anyCoincidence = false;
                ++$currentNumberOfChars;

                foreach ($this->getCurrentUsers() as $currentUser) {
                    $initialAux = mb_substr($currentUser->getUserName(), 0, $currentNumberOfChars);

                    foreach ($this->getCurrentUsers() as $currentUser2) {
                        // If it is not the same user
                        if ($currentUser->getUserName() != $currentUser2->getUsername()) {
                            $initialAux2 = mb_substr($currentUser2->getUsername(), 0, $currentNumberOfChars);

                            // If initials are equal we add a letter for each of them
                            if ($initialAux == $initialAux2) {
                                $anyCoincidence = true;
                                $newInitialAux = mb_substr($currentUser->getUserName(), 0, $currentNumberOfChars + 1);
                                $currentUser->setInitials($newInitialAux);

                                $newInitialAux2 = mb_substr($currentUser2->getUsername(), 0, $currentNumberOfChars + 1);
                                $currentUser2->setInitials($newInitialAux2);
                            } // end if
                        } // end if
                    } // end foreach
                } // end foreach
            } while (true == $anyCoincidence && $currentNumberOfChars < $maxNumberOfChars - 1);
        } // end if
    }
}
