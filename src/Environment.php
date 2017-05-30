<?php

namespace Makehappen\AutoMinifier;

/**
 * Class Environment
 * @package Makehappen\AutoMinifier
 */
class Environment
{
    /**
     * Environment file
     */
    const ENV_FILE = 'env.json';

    /**
     * Configuration file
     */
    const CONFIG_FILE = 'config.json';

    /**
     * Signature file for cache bust
     */
    const SIGNATURE_FILE = 'signature.txt';

    /**
     * Is Development
     * @var
     */
    protected $blnIsDev;

    /**
     * Environment Settings
     * @var
     */
    protected $objEnvironmentSettings;

    /**
     * Environment constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set env to Dev
     *
     * @param bool $bln
     * @return $this
     */
    public function setDev($bln = true)
    {
        // set to provided value
        $this->blnIsDev = $bln;
        return $this;
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

        if ($this->hasDevEnv()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if it has develompent env set
     *
     * @return bool
     */
    public function hasDevEnv()
    {
        // if not set abort
        if (empty($this->objEnvironmentSettings->environment)) {
            return false;
        }

        // determine if it's development
        return 'development' == $this->objEnvironmentSettings->environment;
    }

    /**
     * Create a settings file
     *
     * @param $strFilePath
     * @param $arrSettings
     * @return object
     */
    public function createSettingsFile($strFilePath, $arrSettings)
    {
        // create file
        file_put_contents($strFilePath, json_encode($arrSettings, JSON_PRETTY_PRINT));

        return (object) $arrSettings;
    }

    /**
     * Get settings
     *
     * @param null $strFile
     * @param array $arrSettings
     * @return mixed|object
     */
    public function getSettings($strFile = null, $arrSettings = [])
    {
        // crete and retur if does not exists
        if (!file_exists($strFile)) {
            return $this->createSettingsFile($strFile, $arrSettings);
        }

        // return contents
        return json_decode(file_get_contents($strFile));
    }

    /**
     * Set environment
     *
     * @param null $strFilesFolder
     */
    public function setEnvironment($strFilesFolder = null)
    {
        // default environment
        $arrDefaultEnv = [
            'environment' => 'production'
        ];

        // get env file settings
        $this->objEnvironmentSettings = $this->getSettings($strFilesFolder  . '/' . self::ENV_FILE, $arrDefaultEnv);
    }
}
