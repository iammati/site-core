<?php

declare(strict_types=1);

namespace Site\Core\Configuration;

use Symfony\Component\Dotenv\Dotenv;
use TYPO3\CMS\Core\Core\Environment;

class DotenvLoader
{
    /**
     * The post-autoloaddump callback for composer.
     *
     * @return void
     */
    public static function postAutoloadDump()
    {
        // Loading the .env file using the Symfony DotEnv-Component
        $dotenv = new Dotenv();
        $dotenv->load(self::getPath('.env'));
    }

    /**
     * @param string $node optional additional node-string passable
     *
     * @return string
     */
    protected static function getPath(string $node = '')
    {
        return (Environment::getProjectPath() ?? '').'/'.$node;
    }
}
