<?php

namespace App\Components;

use Archive7z\Archive7z;

class MyArchive7z extends Archive7z
{
    protected $timeout = 120;
    protected $compressionLevel = 9;
    protected $overwriteMode = self::OVERWRITE_MODE_A;
    
    //PATH
    protected $outputDirectory = APP_DIR . 'storage';
}

class ParseDirectoryComponent
{
    private $archiveType = ['rar', 'zip', '7z'];

    private $fileTypes = ['jpg', 'jpeg', 'png', 'bmp', 'tiff', 'psd', 'raw', 'gif', 'jp2'];

    //PATH
    private $filePath = APP_DIR . "storage\\";

    public function run()
    {
        $scan = scandir($this->filePath);

        foreach ($scan as $s) {

            if (in_array(pathinfo($s, PATHINFO_EXTENSION), $this->archiveType)) {
                $this->unpack($s);
            }
        }
    }

    private function unpack($name)
    {
        $rootName = pathinfo($name, PATHINFO_FILENAME);

        if(!is_dir($this->filePath . $rootName)){
            mkdir($this->filePath . $rootName );
        }

        $obj = new MyArchive7z($this->filePath . $name);

        if (!$obj->isValid()) {
            throw new \RuntimeException('Incorrect archive');
        }

        foreach ($obj->getEntries() as $entry) {

            echo $entry->getPath() . " - \033[32m OK \033[0m" . PHP_EOL;

            if (in_array(pathinfo($entry->getPath(), PATHINFO_EXTENSION), $this->archiveType)) {
                $basename = pathinfo($entry->getPath(), PATHINFO_BASENAME);
                $entry->extractTo( $this->filePath);
                $this->unpack($basename);
            }

            if (pathinfo($entry->getPath(), PATHINFO_EXTENSION) === 'max') {
                $entry->extractTo( $this->filePath);
                $basename = pathinfo($entry->getPath(), PATHINFO_FILENAME);
                rename($this->filePath . $basename . '.max',str_replace($basename, $rootName, $this->filePath . $basename . '.max'));
            }

            if (in_array(pathinfo($entry->getPath(), PATHINFO_EXTENSION), $this->fileTypes)) {
                $entry->extractTo( $this->filePath . $rootName);
            }
        }
    }
}