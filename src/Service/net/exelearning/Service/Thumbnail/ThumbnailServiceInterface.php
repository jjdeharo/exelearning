<?php

namespace App\Service\net\exelearning\Service\Thumbnail;

interface ThumbnailServiceInterface
{
    /**
     * getThumbnailBinary
     * Create binary content to set on the file.
     *
     * @param string $imagine_filter
     *
     * @return array
     */
    public function getThumbnailBinary($output_file, $imagine_filter = 'default_thumbnail');

    /**
     * createThumbnail
     * Create file with the binary content.
     *
     * @param string $savedFilename
     * @param string $iDeviceDir
     * @param string $thumbnailBinary
     *
     * @return array
     */
    public function createThumbnail($savedFilename, $iDeviceDir, $thumbnailBinary);
}
