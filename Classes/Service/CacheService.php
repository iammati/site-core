<?php

declare(strict_types=1);

namespace Site\Core\Service;

use Closure;
use Exception;
use Site\Core\Interfaces\CacheInterface;
use Site\Core\Utility\StrUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheService implements CacheInterface
{
    protected string $path = '';
    protected string $fileExtension = '';

    /**
     * Defines the path where the cached files
     * will be created and read from.
     *
     * @param string $path a relative path of the server
     */
    public function setPath(string $path): void
    {
        $this->path = GeneralUtility::getFileAbsFileName($path);
    }

    /**
     * Retrieves the relative path where cached files are placed.
     *
     * @param string $identifier Optional. If given, it'll used the defined $this->path and
     *                           appends the provided $identifier string additionally.
     *                           Must not start with a '/' (slash).
     */
    public function getPath(string $identifier = '')
    {
        $path = $this->path;

        if ($identifier === '') {
            return $path;
        }

        if (str_ends_with($path, '/')) {
            $path .= $identifier;
        } else {
            $path .= '/'.$identifier;
        }

        return $path;
    }

    /**
     * Defines the file-extension of cached files.
     * Could be anything, e.g. '.html' or '.json' etc.
     *
     * @param string $fileExtension The file-extension passed as string and without
     *                              a '.' (dot) - just the extension itself in lowercase.
     */
    public function setFileExtension(string $fileExtension): void
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * Retrieves the file-extension of this cache service.
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * Determines if this cache-service has the given $identifier
     * cached file or not by checking its existance.
     *
     * @param string $identifier Name of cached file without file-extension
     */
    public function has(string $identifier): bool
    {
        return file_exists($this->getPath().$identifier.'.'.$this->getFileExtension());
    }

    public function get(string $identifier, ?Closure $notCachedCallback): bool|string
    {
        $identifier = StrUtility::convertUri($identifier);

        if (!$this->has($identifier)) {
            if (null == $notCachedCallback) {
                return false;
            }

            $data = $this->optimizeData(call_user_func($notCachedCallback));

            $this->add($identifier, $data);

            return $data;
        }

        return $this->optimizeData(
            file_get_contents($this->getPath($identifier.'.'.$this->getFileExtension()))
        );
    }

    public function add(string $identifier, string $content): ?bool
    {
        $identifier = StrUtility::convertUri($identifier);

        if ($this->has($identifier)) {
            return true;
        }

        $cacheFilePath = $this->getPath($identifier.'.'.$this->getFileExtension());
        $content = trim($content);

        $filePutContents = file_put_contents($cacheFilePath, $content);

        if (!$filePutContents) {
            new Exception('Fatal. CacheService was not able to create the "'.$cacheFilePath.'"-file.');
        }

        return null;
    }

    /**
     * Deletes a cache by its identifier if it's present.
     *
     * @param string $identifier The filename used to be called
     */
    public function remove(string $identifier): void
    {
        $cacheFilePath = $this->getPath($identifier.'.'.$this->getFileExtension());

        if ($identifier === '*') {
            $cachedFiles = glob($cacheFilePath);

            foreach ($cachedFiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        } else {
            if (file_exists($cacheFilePath)) {
                unlink($cacheFilePath);
            }
        }
    }

    /**
     * Optimizes the given $data HTML string to minimize the output HTML as a performance optimization.
     *
     * @param string $data The HTML string which should be optimized
     *
     * @todo Find a way to minify / compress the HTML down to an oneliner.
     */
    public function optimizeData(string $data): string
    {
        $data = implode('', explode('\n', $data));

        $search = [
            '/(\n|^)(\x20+|\t)/',
            '/(\n|^)\/\/(.*?)(\n|$)/',
            '/\n/',
            '/\<\!--.*?-->/',
            '/(\x20+|\t)/', // Delete multispace (Without \n)
            '/\>\s+\</', // strip whitespaces between tags
            '/(\'|\')\s+\>/', // strip whitespaces between quotation ('') and end tags
            '/=\s+(\'|\')/', // strip whitespaces between = ''
        ];

        $replace = [
            "\n",
            "\n",
            ' ',
            '',
            ' ',
            '><',
            '$1>',
            '=$1',
        ];

        return preg_replace($search, $replace, $data);
    }
}
