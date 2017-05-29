<?php

namespace Makehappen\AutoMinifier;

class Environment
{
    const ENV_FILE = 'env.json';

    const CONFIG_FILE = 'config.json';

    const SIGNATURE_FILE = 'signature.txt';

    protected $blnIsDev;

    protected $objEnv;

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

    public function hasDevEnv()
    {
        // if not set abort
        if (empty($this->objEnv->environment)) {
            return false;
        }

        // determine if it's development
        return 'development' == $this->objEnv->environment;
    }

    public function createSettingsFile($strFilePath, $arrSettings)
    {
        // create file
        file_put_contents($strFilePath, json_encode($arrSettings, JSON_PRETTY_PRINT));

        return (object) $arrSettings;
    }

    public function getSettings($strFile = null, $arrSettings = [])
    {
        // crete and retur if does not exists
        if (!file_exists($strFile)) {
            return $this->createSettingsFile($strFile, $arrSettings);
        }

        // return contents
        return json_decode(file_get_contents($strFile));
    }

    public function setEnvironment($strFilesFolder = null)
    {
        // default environment
        $arrDefaultEnv = [
            'environment' => 'production'
        ];

        // get env file settings
        $this->objEnv = $this->getSettings($strFilesFolder  . '/' . self::ENV_FILE, $arrDefaultEnv);
    }
}
