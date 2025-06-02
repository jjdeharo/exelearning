<?php

namespace App\Service\net\exelearning\Service\Thumbnail;

use App\Constants;
use App\Helper\net\exelearning\Helper\FileHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ThumbnailService implements ThumbnailServiceInterface
{
    public const LIIP_DATA_MAMAGER = 'liip_imagine.data.manager';
    public const LIIP_FILTER_MAMAGER = 'liip_imagine.filter.manager';
    public const THUMBNAIL_NAME_INDICATOR = '_thumb';

    private FileHelper $fileHelper;

    private $container;

    public function __construct(
        FileHelper $fileHelper,
        ContainerInterface $container,
        TranslatorInterface $translator,
    ) {
        $this->fileHelper = $fileHelper;
        $this->container = $container;
    }

    /**
     * getThumbnailBinary
     * Create binary content to set on the file.
     *
     * @param string $imagine_filter
     *
     * @return array
     */
    public function getThumbnailBinary($output_file, $imagine_filter = 'default_thumbnail')
    {
        $data = [];
        // Extract filesDir from the path
        $filesDir = $this->fileHelper->getFilesDir();
        $output_file = str_replace($filesDir, '', $output_file);

        // Find the filter and load a binary without filter
        $liipDataManager = $this->container->get(self::LIIP_DATA_MAMAGER);
        //      $liipDataManager = $liipDataManager->getLoader($this->defaultThumbnailConfig());
        $processedImage = $liipDataManager->find($imagine_filter, $output_file);

        // Apply the filters to the binary and return te content
        $liipFilterManager = $this->container->get(self::LIIP_FILTER_MAMAGER);
        $newimage = $liipFilterManager->applyFilter($processedImage, $imagine_filter, $this->filterConfig());
        $newimage_string = $newimage->getContent();

        $data['content'] = $newimage_string;

        return $data;
    }

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
    public function createThumbnail($savedFilename, $iDeviceDir, $thumbnailBinary)
    {
        $data = [];

        $indexExtImg = strripos($savedFilename, Constants::FILE_EXTENSION_SEPARATOR);
        $subExtImg = substr($savedFilename, 0, $indexExtImg);
        $extImg = substr($savedFilename, $indexExtImg);

        // Thumbnail name
        $thumb_name = $subExtImg.self::THUMBNAIL_NAME_INDICATOR.$extImg;

        // Thumbnail absolute path
        $thumb_path = $iDeviceDir.$thumb_name;

        // Open the thumbnail path for writing
        $thumbnailFile = fopen($thumb_path, 'wb');

        fwrite($thumbnailFile, $thumbnailBinary['content']);

        $data['thumb_name'] = $thumb_name;

        return $data;
    }

    /**
     * filterConfig
     * Personaliced filter config.
     *
     * @return array
     */
    private function filterConfig()
    {
        $data = [];

        $data = [
            'quality' => Constants::THUMBNAIL_FILTER_PROPERTIES[Constants::DEFAULT_THUMBNAIL_FILTER]['quality'],
            'filters' => [
                'upscale' => [
                    'min' => Constants::THUMBNAIL_FILTER_PROPERTIES[Constants::DEFAULT_THUMBNAIL_FILTER]['upscale']['min'],
                ],
                'thumbnail' => [
                    'size' => Constants::THUMBNAIL_FILTER_PROPERTIES[Constants::DEFAULT_THUMBNAIL_FILTER]['thumbnail']['size'],
                    'mode' => Constants::THUMBNAIL_FILTER_PROPERTIES[Constants::DEFAULT_THUMBNAIL_FILTER]['thumbnail']['mode'],
                ],
            ],
        ];

        return $data;
    }
}
