<?php

namespace Makehappen\AutoMinifier;

use MatthiasMullie\Minify as Minifier;

/**
 * Class Minify
 * @package Makehappen\AutoMinifier
 */
class Minify
{
    /**
     * Storage folder path
     *
     * @var string
     */
    protected $strStorageFolder;

    /**
     * Cache bust file path
     *
     * @var string
     */
    protected $strCacheBustFile;

    /**
     * Public folder path
     *
     * @var string
     */
    protected $strPublicFolderPath;

    /**
     * File types supported
     *
     * @var array
     */
    protected $arrFileTypes;

    /**
     * Destination file extension
     *
     * @var string
     */
    protected $strDestinationExtension;

    /**
     * Destination folder
     *
     * @var
     */
    protected $strDestinationFolder;

    /**
     * Destination file
     *
     * @var
     */
    protected $strDestinationFile;

    /**
     * Set env to dev
     *
     * @var
     */
    protected $blnIsDev;

    /**
     * Create a new Minifier Instance
     */
    public function __construct()
    {
        // set class variables
        $this->setClassVars();
    }

    /**
     * Set env to Dev
     *
     * @param bool $bln
     * @return $this
     */
    public function setDev($bln = true)
    {
        $this->blnIsDev = $bln;
        return $this;
    }

    /**
     * Set Class Variables
     *
     * @return null
     */
    public function setClassVars()
    {
        // set storage folder
        $this->strStorageFolder = __DIR__ . '/../../../../storage';

        // set application public path relative to package location
        $this->strPublicFolderPath = __DIR__ . '/../../../../public';

        // cache bust file
        $this->strCacheBustFile = $this->strStorageFolder . '/autominifier-cache-bust.txt';

        // set file types we want minified
        $this->arrFileTypes = [
            'js' => ['js'],
            'css' => ['css', 'sass', 'scss']
        ];

        return null;
    }

    /**
     * Set test folder
     *
     * @return $this
     */
    public function setTest()
    {
        // set storage folder
        $this->strStorageFolder = __DIR__ . '/../build/storage';

        // set application public path relative to package location
        $this->strPublicFolderPath = __DIR__ . '/../build';

        // cache bust file
        $this->strCacheBustFile = $this->strStorageFolder . '/autominifier-cache-bust.txt';

        return $this;
    }

    /**
     * Get Public folder
     *
     * @return string
     */
    public function getPublicFolder()
    {
        return $this->strPublicFolderPath;
    }

    /**
     * Get storage folder
     *
     * @return string
     */
    public function getStorageFolder()
    {
        return $this->strStorageFolder;
    }

    /**
     * Build JS minified file
     *
     * @var $strFolder string
     * @var $strFile string
     * @return string
     */
    public function js($strFolder = '/js', $strFile = 'app.min.js')
    {
        $this->setDestinationExtensionType('js');
        $this->setDestinationFolder($strFolder);
        $this->setDestinationFile($strFile);
        return $strFolder . '/' . $strFile .'?' . $this->process();
    }

    /**
     * Build CSS minified file
     *
     * @var $strFolder string
     * @var $strFile string
     * @return bool|null|string
     */
    public function css($strFolder = '/css', $strFile = 'app.min.css')
    {
        $this->setDestinationExtensionType('css');
        $this->setDestinationFolder($strFolder);
        $this->setDestinationFile($strFile);
        return $strFolder . '/' . $strFile .'?' . $this->process();
    }

    /**
     * Process Built
     *
     * @return bool|null|string
     */
    public function process()
    {
        // return last cache bust in non development environments
        if (!$this->isDevEnv()) {
            return $this->getCacheBust();
        }

        // build application file
        $strApplicationFileContents = $this->build($this->arrFileTypes[$this->getDestinationExtension()]);

        // save application file contents
        $strApplicationFileContents = $this->createApplicationFile($strApplicationFileContents);

        // save new cache bust
        return $this->saveCacheBust($strApplicationFileContents);
    }

    /**
     * Set destination folder
     *
     * @param $strFolder
     */
    public function setDestinationFolder($strFolder)
    {
        $this->strDestinationFolder = $strFolder;
    }

    /**
     * Set destination file
     *
     * @param $strFile
     */
    public function setDestinationFile($strFile)
    {
        $this->strDestinationFile = $strFile;
    }

    /**
     * Get destination folder
     *
     * @return mixed
     */
    public function getDestinationFolder()
    {
        return $this->strDestinationFolder;
    }

    /**
     * Set destination file
     *
     * @return mixed
     */
    public function getDestinationFile()
    {
        return $this->strDestinationFile;
    }

    /**
     * Save Cache Bust
     *
     * @var string $strApplicationFileContents
     * @return null
     */
    public function saveCacheBust($strApplicationFileContents = '')
    {
        // if we don't have a storage folder create it
        if (!is_dir($this->strStorageFolder)) {
            mkdir($this->strStorageFolder);
        }

        // get contents signature
        $strNewCacheBust = md5($strApplicationFileContents);

        // get prior cache bust
        $strPriorCacheBust = $this->getCacheBust();

        // if unchanged, stop here
        if ($strPriorCacheBust == $strNewCacheBust) {
            return $strPriorCacheBust;
        }

        // set new cache bust
        file_put_contents($this->strCacheBustFile, $strNewCacheBust);

        // return new cache bust
        return $strNewCacheBust;
    }

    /**
     * Get Cache Bust
     *
     * @return bool|string
     */
    public function getCacheBust()
    {
        // abort if file not found
        if (!file_exists($this->strCacheBustFile)) {
            return '';
        }

        // get cache bust file contents
        return file_get_contents($this->strCacheBustFile);
    }

    /**
     * Determine if we are in development environment
     *
     * @return bool
     */
    public function isDevEnv()
    {
        // if set to dev stop here
        if ($this->blnIsDev) {
            return true;
        }

        // domain extension is .dev
        if ($this->isDevDomain()) {
            return true;
        }

        // localhost
        if ($this->isLocalhost()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if it's .dev domain
     *
     * @return bool
     */
    public function isDevDomain()
    {
        $arrDomain = explode('.', $_SERVER['HTTP_HOST']);
        return array_pop($arrDomain) == 'dev';
    }

    /**
     * Is is localhost
     *
     * @return bool
     */
    public function isLocalhost()
    {
        // must have HTTP_HOST set
        if (empty($_SERVER['HTTP_HOST'])) {
            return false;
        }

        return $_SERVER['HTTP_HOST'] == 'localhost';
    }

    /**
     * Create minified and concatenated file
     *
     * @param $strApplicationFileContents
     * @return string
     */
    public function createApplicationFile($strApplicationFileContents)
    {
        file_put_contents($this->getAppFileName(), $strApplicationFileContents);
        return $strApplicationFileContents;
    }

    /**
     * Get application file name
     *
     * @return string
     */
    public function getAppFileName()
    {
        return $this->strPublicFolderPath . $this->getDestinationFolder() . '/' . $this->getDestinationFile();
    }

    /**
     * Set destination file extension
     *
     * @param $strDestinationExtension
     */
    public function setDestinationExtensionType($strDestinationExtension)
    {
        $this->strDestinationExtension = $strDestinationExtension;
    }

    /**
     * Get destination file type extension
     *
     * @return mixed
     */
    public function getDestinationExtension()
    {
        return $this->strDestinationExtension;
    }

    /**
     * Build file
     *
     * @param $arrOriginExtensions
     * @return string
     */
    public function build($arrOriginExtensions)
    {
        $strPublicFolder = $this->strPublicFolderPath . '/' . $this->getDestinationFolder();

        // abort if folder not found
        if (!is_dir($strPublicFolder)) {
            return $strPublicFolder . ' folder not found';
        }

        // get all files
        $arrFiles = scandir($strPublicFolder);

        // loop through all files
        $strMinifiedFileContents = '';
        foreach ($arrFiles as $strFileName) {
            // get file extension
            $arrFileName = explode('.', $strFileName);
            $strFileExtension = array_pop($arrFileName);

            // must be a listed file type
            if (!in_array($strFileExtension, $arrOriginExtensions)) {
                continue;
            }

            // must not be the app file
            if ($strFileName = $this->getDestinationFile()) {
                continue;
            }

            // add new minified file to concatenated version
            $strMinifiedFileContents .=
                "\n/* $strFileName */\n" .
                $this->getMinifiedContent($strPublicFolder . '/' . $strFileName)
            ;
        }

        // returned concatenated version of minifired files
        return $strMinifiedFileContents;
    }

    /**
     * Get minified Content
     *
     * @param $strFile
     * @return bool|string
     */
    public function getMinifiedContent($strFile)
    {
        // if it's minified already return content
        if (preg_match('/\.min\./', $strFile)) {
            return file_get_contents($strFile);
        }

        // return minified content
        return $this->minifyContent($strFile);
    }

    /**
     * Minify content
     *
     * @param  $strFile
     * @return bool|string
     */
    public function minifyContent($strFile)
    {
        // minify based on file type
        switch ($this->getDestinationExtension()) {
            case 'js':
                return $this->minifyJs($strFile);
            case 'css':
                return $this->minifyCss($strFile);
            default:
                return '';
        }
    }

    /**
     * Minify JS
     *
     * @param $strFile
     * @return bool|string
     */
    public function minifyJs($strFile)
    {
        return (new Minifier\JS($strFile))->minify();
    }

    /**
     * Minify CSS
     *
     * @param $strFile
     * @return bool|string
     */
    public function minifyCss($strFile)
    {
        return (new Minifier\CSS($strFile))->minify();
    }
}
