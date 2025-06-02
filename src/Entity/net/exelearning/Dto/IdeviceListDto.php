<?php

namespace App\Entity\net\exelearning\Dto;

use App\Constants;

/**
 * IdeviceListDto.
 */
class IdeviceListDto extends BaseDto
{
    /**
     * @var IdeviceDto[]
     */
    protected $iDevices;

    public function __construct()
    {
        $this->iDevices = [];
    }

    /**
     * @return multitype:\App\Entity\net\exelearning\Dto\IdeviceDto
     */
    public function getIdevices()
    {
        return $this->iDevices;
    }

    /**
     * @param multitype:\App\Entity\net\exelearning\Dto\IdeviceDto  $iDevices
     */
    public function setIdevices($iDevices)
    {
        $this->iDevices = $iDevices;
    }

    /**
     * @param IdeviceDto $iDevice
     */
    public function addIdevice($iDevice)
    {
        $this->iDevices[] = $iDevice;
    }

    /**
     * @param string $name
     */
    public function removeIdevice($name, $user, $iDeviceHelper, $fileHelper)
    {
        $response = ['name' => $name, 'found' => false, 'deleted' => false, 'path' => ''];
        foreach ($this->iDevices as $idevice) {
            if ($idevice->getName() == $name) {
                $response['found'] = true;
                // Idevice dir
                $ideviceDir = $iDeviceHelper->getIdeviceDir(
                    $idevice->getDirName(),
                    Constants::IDEVICE_TYPE_USER,
                    $user
                );
                $response['path'] = $ideviceDir;
                // Delete idevice dir
                try {
                    $fileHelper->deleteDir($ideviceDir);
                    $response['deleted'] = true;
                } catch (\Exception $e) {
                }
            }
        }

        return $response;
    }
}
