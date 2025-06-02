<?php

namespace App\Service\net\exelearning\Service\FilemanagerService\Storage;

use App\Config\net\exelearning\Config\FilemanagerConfig\Collection;

/**
 * Implements Directory collection to the filemanager, examine the folder.
 */
class DirectoryCollection implements \JsonSerializable
{
    use Collection;

    protected $location;

    public function __construct($location)
    {
        $this->location = $location;
    }

    public function addFile(string $type, string $path, string $name, int $size, int $timestamp)
    {
        if (!in_array($type, ['dir', 'file', 'back'])) {
            throw new \Exception('Invalid file type.');
        }
        $this->add([
            'type' => $type,
            'path' => $path,
            'name' => $name,
            'size' => $size,
            'time' => $timestamp,
        ]);
    }

    public function resetTimestamps($timestamp = 0)
    {
        foreach ($this->items as &$item) {
            $item['time'] = $timestamp;
        }
    }

    public function jsonSerialize(): mixed
    {
        $this->sortByValue('type');

        return [
            'location' => $this->location,
            'files' => $this->items,
        ];
    }
}
