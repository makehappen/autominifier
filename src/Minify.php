<?php

namespace makehappen\autominifier;

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
     * @var
     */
    protected $objConfig;

    /**
     * @var Files
     */
    protected $objFiles;

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

        // set files class
        $this->objFiles = new Files();
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
        $this->loadConfig();
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
            return $this->objFiles->getCacheBust($this->strCacheBustFile);
        }

        // build application file
        $strApplicationFileContents = $this->build();

        // save application file
        file_put_contents($this->getAppFileName(), $strApplicationFileContents);

        // save new cache bust
        return $this->objFiles->saveCacheBust($strApplicationFileContents, $this->strCacheBustFile);
    }

    /**
     * Build file
     *
     * @return string
     */
    public function build()
    {
        // min file contents
        $strMinifiedFileContents = '';

        // loop through all files
        foreach ($this->getFiles() as $strFileName) {
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
     * @return array
     */
    public function getFiles()
    {
        // if set in config file, return them
        if (count($this->objConfig->files)) {
            return $this->objConfig->files;
        }

        // find files in folder
        return $this->objFiles->getFiles($this->getFilesFolder());
    }

    /**
     * Get minified Content
     *
     * @param $strFileName
     * @return bool|string
     */
    public function getMinifiedContent($strFileName)
    {
        // make sure the file name it's valid
        if (!$this->isValidFileName($strFileName)) {
            return '';
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
     * Determine if it's a valid file name
     *
     * @param $strFileName
     * @return bool
     */
    public function isValidFileName($strFileName)
    {
        // get file extension
        $arrFileName = explode('.', $strFileName);
        $strFileExtension = array_pop($arrFileName);

        // must be a listed file type
        if (!in_array($strFileExtension, $this->arrFileTypes[$this->getDestinationExtension()])) {
            return false;
        }

        // must not be the app file
        if ($strFileName == $this->getDestinationFile()) {
            return false;
        }

        return true;
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
     * Load configuration
     */
    public function loadConfig()
    {
        // set config
        $this->objConfig = $this->objEnvironment->getConfig($this->getFilesFolder());

        // set environment
        $this->objEnvironment->setEnvironment($this->getFilesFolder());
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
     * @return $this
     */
    public function setCacheBustFile()
    {
        $this->strCacheBustFile = $this->getFilesFolder() . '/' . Environment::SIGNATURE_FILE;
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
     * @return $this
     */
    public function setDestinationExtensionType($strDestinationExtension)
    {
        $this->strDestinationExtension = $strDestinationExtension;
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
