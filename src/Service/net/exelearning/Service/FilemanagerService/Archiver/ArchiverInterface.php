<?php

namespace App\Service\net\exelearning\Service\FilemanagerService\Archiver;

use App\Service\net\exelearning\Service\FilemanagerService\Storage\Filesystem;

/**
 * Interface with archive functions.
 */
interface ArchiverInterface
{
    public function createArchive(Filesystem $storage): string;

    public function uncompress(string $source, string $destination, Filesystem $storage);

    public function addDirectoryFromStorage(string $path);

    public function addFileFromStorage(string $path);

    public function closeArchive();

    public function storeArchive($destination, $name);
}
