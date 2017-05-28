<?php

namespace Makehappen\AutoMinifier;

use MatthiasMullie\Minify as Minifier;

/**
 * Class Minify
 *
 * Automatically Minify and Concatenate your JS and CSS files and libraries into single files
 * for improved application performance.
 *
 * @package Makehappen\AutoMinifier
 */
class Minify
{
    /**
     * @var Environment
     */
    protected $objEnvironment;

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
     *
     * Minify constructor.
     */
    public function __construct()
    {
        // set class variables
        $this->init();

        // set environment
        $this->objEnvironment = new Environment();
    }


    /**
     * Set Class Variables
     *
     * @return null
     */
    public function init()
    {
        // set public folder location
        $this->setPublicFolder();

        // set file types we want minified
        $this->arrFileTypes = [
            'js' => ['js'],
            'css' => ['css', 'sass', 'scss']
        ];

        return null;
    }

    /**
     * Build minified file
     *
     * @var $strFolder string
     * @var $strFile string
     * @return string
     */
    public function js($strFolder = '/js', $strFile = 'app.min.js')
    {
        return $this->getMinifiedFile($strType = 'js', $strFolder, $strFile);
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
        return $this->getMinifiedFile($strType = 'css', $strFolder, $strFile);
    }
    
    /**
     * Generate minified file
     *
     * @param string $strType
     * @param string $strFolder
     * @param string $strFile
     * @return string
     */
    public function getMinifiedFile($strType = 'css', $strFolder = '/css', $strFile = 'app.min.css')
    {
        $this->setDestinationExtensionType($strType);
        $this->setDestinationFolder($strFolder);
        $this->setDestinationFile($strFile);
        $this->setCacheBustFile();
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
        if (!$this->objEnvironment->isDevEnv()) {
            return $this->getCacheBust();
        }

        // build application file
        $strApplicationFileContents = $this->build();

        // save application file
        file_put_contents($this->getAppFileName(), $strApplicationFileContents);

        // save new cache bust
        return $this->saveCacheBust($strApplicationFileContents);
    }

    /**
     * Build file
     *
     * @return string
     */
    public function build()
    {
        // files folder
        $strFilesFolder = $this->getFilesFolder();

        // abort if folder not found
        if (!is_dir($strFilesFolder)) {
            return $strFilesFolder . ' folder not found';
        }

        // get all files
        $arrFiles = scandir($strFilesFolder);

        // loop through all files
        $strMinifiedFileContents = '';
        foreach ($arrFiles as $strFileName) {
            // get minified content
            $strFileContents = $this->getMinifiedContent($strFileName);

            // don't include empty files
            if (!$strFileContents) {
                continue;
            }

            // add new minified file to concatenated version
            $strMinifiedFileContents .= "\n/* $strFileName */\n" . $strFileContents;
        }

        // returned concatenated version of minifired files
        return $strMinifiedFileContents;
    }

    /**
     * Get minified Content
     *
     * @param $strFileName
     * @return null|string
     */
    public function getMinifiedContent($strFileName)
    {
        // get file extension
        $arrFileName = explode('.', $strFileName);
        $strFileExtension = array_pop($arrFileName);

        // must be an accepted file type
        if (!$this->isAcceptedExtension($strFileExtension)) {
            return null;
        }

        // must not be the app file
        if ($strFileName == $this->getDestinationFile()) {
            return null;
        }

        // build file path and name
        $strFile = $this->getFilesFolder() . '/' . $strFileName;

        // if it's minified already return content
        if (preg_match('/\.min\./', $strFile)) {
            return file_get_contents($strFile);
        }

        // return minified content
        return $this->minifyContent($strFile);
    }

    /**
     * Determine if it's an accepted extension
     *
     * @param $strFileExtension
     * @return bool
     */
    public function isAcceptedExtension($strFileExtension)
    {
        return in_array($strFileExtension, $this->arrFileTypes[$this->getDestinationExtension()]);
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
                return (new Minifier\JS($strFile))->minify();
            case 'css':
                return (new Minifier\CSS($strFile))->minify();
            default:
                return '';
        }
    }

    /**
     * Save Cache Bust
     *
     * @var string $strApplicationFileContents
     * @return null
     */
    public function saveCacheBust($strApplicationFileContents = '')
    {
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
     * Set env to Dev
     *
     * @param bool $bln
     * @return $this
     */
    public function setDev($bln = true)
    {
        $this->objEnvironment->setDev($bln);
        $this->blnIsDev = $bln;
        return $this;
    }

    /**
     * Set Cache bust file
     *
     * @param string $strFile
     * @return $this
     */
    public function setCacheBustFile($strFile = 'autominifier-cache-bust.txt')
    {
        $this->strCacheBustFile = $this->getFilesFolder() . "/$strFile";
        return $this;
    }

    /**
     * Set public folder
     *
     * @param string $strFolder
     * @return $this
     */
    public function setPublicFolder($strFolder = '/../../../../public')
    {
        // set application public path relative to package location
        $this->strPublicFolderPath = __DIR__ . $strFolder;
        return $this;
    }

    /**
     * Set test folder
     *
     * @return $this
     */
    public function setTest()
    {
        // set application public path relative to package location
        $this->setPublicFolder('/../build');

        // cache bust file
        $this->setCacheBustFile();

        return $this;
    }

    /**
     * Set destination folder
     *
     * @param $strFolder
     * @return $this
     */
    public function setDestinationFolder($strFolder)
    {
        $this->strDestinationFolder = $strFolder;
        return $this;
    }

    /**
     * Set destination file
     *
     * @param $strFile
     * @return $this
     */
    public function setDestinationFile($strFile)
    {
        $this->strDestinationFile = $strFile;
        return $this;
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
     * Get Public folder
     *
     * @return string
     */
    public function getPublicFolder()
    {
        return $this->strPublicFolderPath;
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
     * Get application file name
     *
     * @return string
     */
    public function getAppFileName()
    {
        return $this->getFilesFolder() . '/' . $this->getDestinationFile();
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
     * Get files folder
     *
     * @return string
     */
    public function getFilesFolder()
    {
        return $this->getPublicFolder() . '/' . $this->getDestinationFolder();
    }
}
