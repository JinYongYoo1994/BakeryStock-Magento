<?php
namespace Clyde\Warranty\Model;
use Magento\Framework\App\Filesystem\DirectoryList;

class Logger extends \Magento\Framework\Model\AbstractModel
{
    CONST LOG_FILE_NAME = 'clydeApi.log';
    protected $_filesystem;
    protected $_files;
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $files,
        array $data = array()
    ) {
        $this->_filesystem = $filesystem;
        $this->_files = $files;
        parent::__construct($context, $registry);
    }
    public function getLogDirectory()
    {
        $fileDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath();
        $dirPath =  $fileDirectory . "log/";
        if (!file_exists($dirPath)) 
        {
            $this->_files->mkdir($dirPath, 0777);
        }

        return $dirPath;
    }

    public function wrietLog($prefix, $value)
    {
        $file = $this->getLogDirectory().self::LOG_FILE_NAME;
        file_put_contents($file, $prefix.print_r($value, true).PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function debugLog($message)
    {
        $date = date(DATE_ATOM);
        $prefix = $date.':- DEGUB :';
        $this->wrietLog($prefix, $message);

    }

    public function errorLog($message)
    {
        $date = date(DATE_ATOM);
        $prefix = $date.':- ERROR :';
        $this->wrietLog($prefix, $message);
    }
}