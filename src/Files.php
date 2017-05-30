<?php

namespace Makehappen\AutoMinifier;

class Files
{
    /**
     * Get files to minify and concatenate
     *
     * @var $strFilesFolder
     * @return array
     */
    public function getFiles($strFilesFolder = '')
    {
        // abort if folder not found
        if (!is_dir($strFilesFolder)) {
            return [];
        }

        // get all files
        return scandir($strFilesFolder);
    }

    /**
     * Save Cache Bust
     *
     * @var string $strApplicationFileContents
     * @var string $strCacheBustFile
     * @return null
     */
    public function saveCacheBust($strApplicationFileContents = '', $strCacheBustFile = '')
    {
        // get contents signature
        $strNewCacheBust = md5($strApplicationFileContents);

        // get prior cache bust
        $strPriorCacheBust = $this->getCacheBust($strCacheBustFile);

        // if unchanged, stop here
        if ($strPriorCacheBust == $strNewCacheBust) {
            return $strPriorCacheBust;
        }

        // set new cache bust
        file_put_contents($strCacheBustFile, $strNewCacheBust);

        // return new cache bust
        return $strNewCacheBust;
    }


    /**
     * Get Cache Bust
     *
     * @var string $strCacheBustFile
     * @return bool|string
     */
    public function getCacheBust($strCacheBustFile = '')
    {
        // abort if file not found
        if (!file_exists($strCacheBustFile)) {
            return '';
        }

        // get cache bust file contents
        return file_get_contents($strCacheBustFile);
    }
}
