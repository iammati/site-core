<?php

declare(strict_types=1);

namespace Site\Core\Configuration;

use Symfony\Component\Dotenv\Dotenv;

class DotenvLoader
{
    public static function postAutoloadDump()
    {
        // Loading the .env file using the Symfony DotEnv-Component
        $dotenv = new Dotenv();
        $dotenv->load(self::getPath('.env'));
    }

    protected static function getPath(string $node = ''): string
    {
        $rootPath = realpath($_SERVER['DOCUMENT_ROOT'].'/..');

        if ('/' == $rootPath) {
            $rootPath = $_SERVER['PWD'];
        }

        if ('' != $node) {
            $rootPath .= '/'.$node;
        }

        if (str_starts_with($rootPath, '//')) {
            $rootPath = $_SERVER['DOCUMENT_ROOT'].'/'.$node;
        }

        return $rootPath;
    }
}
