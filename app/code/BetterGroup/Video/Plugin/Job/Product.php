<?php

namespace BetterGroup\Video\Plugin\Job;

use BetterGroup\Video\Helper\Config as ConfigHelper;
use Magento\Catalog\Model\Product as BaseProductModel;
use Akeneo\Connector\Helper\Import\Product as ProductImportHelper;
use Magento\ProductVideo\Model\Product\Attribute\Media\ExternalVideoEntryConverter;
use BetterGroup\Video\Helper\YoutubeHelper;
use Zend_Db_Expr as Expr;

class Product
{
	protected $entitiesHelper;
    protected $myHelper;
    protected $youtubeHelper;

	public function __construct(ProductImportHelper $entitiesHelper,ConfigHelper $myHelper,YoutubeHelper $youtubeHelper) {
        $this->myHelper = $myHelper;
        $this->entitiesHelper = $entitiesHelper;
        $this->youtubeHelper = $youtubeHelper;
    }

    public function afterImportMedia(\Akeneo\Connector\Job\Product $Product){
        if (!$this->myHelper->configHelper->isMediaImportEnabled()) {
            $Product->setStatus(false);
            $Product->setMessage(__('Media import is not enabled'));
            return;
        }

    	$connection = $this->entitiesHelper->getConnection();

    	$tmpTable = $tmpTable = $this->entitiesHelper->getTableName($Product->getCode());

    	//$gallery = ['video1','video2'];
                                        
        $gallery = $this->myHelper->getVideoImportGalleryColumns();
    	if (empty($gallery)) {
            $Product->setStatus(false);
            $Product->setMessage(__('Akeneo Video Attributes is empty'));
            return;
        }

        $table = $this->entitiesHelper->getTable('catalog_product_entity');

        $columnIdentifier = $this->entitiesHelper->getColumnIdentifier($table);
        /** @var array $data */
        $data = [
            $columnIdentifier => '_entity_id',
            'sku'             => 'identifier',
        ];

        foreach ($gallery as $video) {
            if (!$connection->tableColumnExists($tmpTable, $video)) {
                $Product->setMessage(__('Warning: %1 attribute does not exist', $video));
                continue;
            }
            $data[$video] = $video;
        }

        $select = $connection->select()->from($tmpTable, $data);

        /** @var \Magento\Framework\DB\Statement\Pdo\Mysql $query */
        $query = $connection->query($select);
         /** @var array $row */
        $galleryAttribute = $this->myHelper->configHelper->getAttribute(BaseProductModel::ENTITY, 'media_gallery');

        $galleryTable = $this->entitiesHelper->getTable('catalog_product_entity_media_gallery');
        /** @var string $galleryEntityValueTable */
        $galleryEntityValueTable = $this->entitiesHelper->getTable('catalog_product_entity_media_gallery_value');
        /** @var string $galleryEntityTable */
        $galleryEntityTable = $this->entitiesHelper->getTable('catalog_product_entity_media_gallery_value_to_entity');
        /** @var string $productImageTable */
        $productVideoTable = $this->entitiesHelper->getTable('catalog_product_entity_media_gallery_value_video');

        $Product->setMessage(__('Import Youtube Video'));
        $i = 0;
        while (($row = $query->fetch())) {
            /** @var array $files */
            foreach ($gallery as $video) {
                if (!isset($row[$video])) {
                    continue;
                }

                if (!$row[$video]) {
                    continue;
                }
                $media = $this->youtubeHelper->getVideoDataByUrl($row[$video]);
                // $this->youtubeHelper->debugLog("============Youtube Media==============");
                // $this->youtubeHelper->debugLog(print_r($media,true));
                if (!$media) {
                    continue;
                }
                /** @var string $name */
                $thumbnailUrl = $media['items']['0']['snippet']['thumbnails']['high']['url'];
                $description = $media['items']['0']['snippet']['description'];
                $title = $media['items']['0']['snippet']['title'];
                $videoId = $media['items']['0']['id'];

                $name = $row[$columnIdentifier]."_" . $videoId . ".jpg";

                if (!$this->myHelper->configHelper->mediaFileExists($name)) {
                    $dest_path = $this->myHelper->getMediaFilePath($name);
                    
                    $this->youtubeHelper->getEncodedFileFromHttp($thumbnailUrl,$dest_path);
                    /*
                    if (!$binary) {
                        continue;
                    }*/

                    //$this->myHelper->configHelper->saveMediaFile($name, $binary);
                }

                /** @var string $file */
                $file = $this->myHelper->configHelper->getMediaFilePath($name);

                /** @var int $valueId */
                $valueId = $connection->fetchOne(
                    $connection->select()->from($galleryTable, ['value_id'])->where('value = ?', $file)
                );

                if (!$valueId) {
                    /** @var int $valueId */
                    $valueId = $connection->fetchOne(
                        $connection->select()->from($galleryTable, [new Expr('MAX(`value_id`)')])
                    );
                    $valueId += 1;
                }

                /** @var array $data */
                $data = [
                    'value_id'     => $valueId,
                    'attribute_id' => $galleryAttribute->getId(),
                    'value'        => $file,
                    'media_type'   => ExternalVideoEntryConverter::MEDIA_TYPE_CODE,
                    'disabled'     => 0,
                ];

                $connection->insertOnDuplicate($galleryTable, $data, array_keys($data));

                /** @var array $data */
                $data = [
                    'value_id'        => $valueId,
                    $columnIdentifier => $row[$columnIdentifier],
                    'store_id' => 0,
                    'position' => 0,
                    'disabled' => 0
                ];


                $connection->insertOnDuplicate($galleryEntityValueTable, $data, array_keys($data));

                /** @var array $data */
                $data = [
                    'value_id'        => $valueId,
                    $columnIdentifier => $row[$columnIdentifier],
                ];

                $connection->insertOnDuplicate($galleryEntityTable, $data, array_keys($data));

                $data = [
                    'value_id' => $valueId, 
                    'store_id' => 0, 
                    'provider' => '',
                    'url' => $row[$video],
                    'title' => $title,
                    'description' => $description,
                    'metadata' => ''
                ];

                $connection->insertOnDuplicate($productVideoTable, $data, array_keys($data));
            }
        }
    }

}
