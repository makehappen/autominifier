<?php

namespace Makehappen\AutoMinifier;

class Environment
{
    protected $blnIsDev;

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
}
