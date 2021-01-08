<?php

declare(strict_types=1);

namespace Site\Core\Composer;

class EnvVars
{
    /**
     * Returns content of .env file as exploded array.
     *
     * @return array|void
     */
    protected static function getEnvContent()
    {
        $path = realpath($_SERVER['DOCUMENT_ROOT'].'/../.env');

        if (!$path) {
            $path = realpath($_SERVER['DOCUMENT_ROOT'].'.env');
        }

        if (!$path) {
            $path = realpath('.env');
        }

        if (!$path) {
            echo "\n> The path is not set!\n\n";

            return;
        }

        if (!file_exists($path)) {
            echo "\n> The .env-file does not exists!\n\n";

            return;
        }

        $explodedEnv = explode("\n", file_get_contents($path));

        return $explodedEnv;
    }

    /**
     * The post-autoloaddump callback for composer.
     *
     * @return void
     */
    public static function postAutoloadDump()
    {
        $envContent = self::getEnvContent();

        if (!is_array($envContent)) {
            echo "\n> The envContent is not an array.";

            return;
        }

        $generatedEnvTs = '';

        foreach ($envContent as $key => $line) {
            if ($line != '') {
                $line = str_replace('__', '.', $line);
                // $line = implode('=', explode('=', $line));

                $generatedEnvTs .= $line."\n";
            }
        }

        if (is_dir('./public/typo3conf/ext/' . getenv('FRONTEND_EXT'))) {
            $envTsFilePath = './public/typo3conf/ext/'.getenv('FRONTEND_EXT').'/Configuration/TypoScript/Constants/environment.ts';

            if (file_exists($envTsFilePath)) {
                unlink($envTsFilePath);
            }

            $envTsFile = fopen($envTsFilePath, 'w+');

            fwrite($envTsFile, $generatedEnvTs);
            fclose($envTsFile);
        }
    }

    /**
     * Helper for AdditionalConfiguration.php.
     *
     * @return string|bool|void
     */
    public static function findByKey(string $key)
    {
        $envContent = self::getEnvContent();

        if (!is_array($envContent)) {
            echo "\n> The envContent is not an array.";

            return;
        }

        $found = false;

        foreach ($envContent as $line) {
            $lineSplitted = explode('=', $line);

            $lineKey = $lineSplitted[0];
            $lineValue = $lineSplitted[1] ?? false;

            if ($lineKey == $key) {
                $found = $lineValue;

                return $found;
            }
        }

        return $found;
    }
}
