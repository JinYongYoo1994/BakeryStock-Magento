<?php

namespace BetterGroup\Video\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Akeneo\Connector\Helper\Serializer;
use Akeneo\Connector\Helper\Config as configHelper;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Framework\App\Filesystem\DirectoryList;

class Config extends AbstractHelper
{
    /** Config keys */
    const PRODUCT_VIDEO_GALLERY = 'akeneo_connector/product/video_gallery';

    protected $serializer;

    public $configHelper;
    /**
     * This variable contains a MediaConfig
     *
     * @var MediaConfig $mediaConfig
     */
    protected $mediaConfig;

    protected $directoryList;

    public function __construct(Context $context,Serializer $serializer,configHelper $configHelper,MediaConfig $mediaConfig,DirectoryList $directoryList) {
        parent::__construct($context);
        $this->serializer = $serializer;
        $this->configHelper = $configHelper;
        $this->mediaConfig = $mediaConfig;
        $this->directoryList = $directoryList;
    }
    /**
     * Retrieve Video Gallery attribute column
     *
     * @return array
     */
    public function getVideoImportGalleryColumns()
    {
        /** @var array $images */
        $videos = [];
        /** @var string $config */
        $config = $this->scopeConfig->getValue(self::PRODUCT_VIDEO_GALLERY);
        if (!$config) {
            return $videos;
        }

        /** @var array $media */
        $media = $this->serializer->unserialize($config);
        if (!$media) {
            return $videos;
        }

        foreach ($media as $video) {
            if (!isset($video['attribute'])) {
                continue;
            }
            $videos[] = $video['attribute'];
        }

        return $videos;
    }
    /**
     * Get media file path
     *
     * @param string $filename
     *
     * @return bool
     */
    public function getMediaFilePath($filename)
    {   
        return $this->directoryList->getPath('media')."/".$this->mediaConfig->getMediaPath($this->configHelper->getMediaFilePath($filename));
    }


}
