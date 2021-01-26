<?php

declare(strict_types=1);

namespace Site\Core\Composer;

use Site\Core\Utility\StrUtility;
use Symfony\Component\Dotenv\Dotenv;

class EnvLoader
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
        $rootPath = realpath($_SERVER['DOCUMENT_ROOT'].'/..');

        if ($rootPath == '/') {
            $rootPath = $_SERVER['PWD'];
        }

        if ($node != '') {
            $rootPath .= '/'.$node;
        }

        if (StrUtility::startsWith($rootPath, '//')) {
            $rootPath = $_SERVER['DOCUMENT_ROOT'].'/'.$node;
        }

        return $rootPath;
    }
}
