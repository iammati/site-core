<?php

declare(strict_types=1);

namespace Site\Core\Service;

use Site\Core\Interfaces\CacheInterface;
use Site\Core\Utility\ExceptionUtility;
use Site\Core\Utility\StrUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CacheService implements CacheInterface
{
    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $fileExtension = '';

    /**
     * Defines the path where the cached files
     * will be created and read from.
     *
     * @param string $path a relative path of the server
     */
    public function setPath($path)
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

        if ('' == $identifier) {
            return $path;
        }

        if (StrUtility::endsWith($path, '/')) {
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
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * Retrieves the file-extension of this cache service.
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Determines if this cache-service has the given $identifier
     * cached file or not by checking its existance.
     *
     * @param string $identifier Name of cached file without file-extension
     *
     * @return bool
     */
    public function has(string $identifier)
    {
        return file_exists($this->getPath().$identifier.'.'.$this->getFileExtension());
    }

    /**
     * Retrieves a cached file by its identifier.
     *
     * @param string        $identifier        The identifier of the cached file - converted to a friendly readable string
     * @param null|\Closure $notCachedCallback Optional. If provided, this closure will be called
     *                                         in case there's no cached file found by the given identifier,
     *                                         which will then cache it immediately by the returned data.
     *
     * @return bool|string
     */
    public function get(string $identifier, \Closure $notCachedCallback = null)
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

    /**
     * Adds a new cached file into the Caches by using the provided $identifier as filename and $content as the content itself.
     * If the given $identifier exists as a cached file already it'll return a true.
     *
     * @param string $identifier The identifier used to be called
     * @param string $content    The actual content which should be cached
     *
     * @return bool|void
     */
    public function add(string $identifier, string $content)
    {
        $identifier = StrUtility::convertUri($identifier);

        if ($this->has($identifier)) {
            return true;
        }

        $cacheFilePath = $this->getPath($identifier.'.'.$this->getFileExtension());
        $content = trim($content);

        $filePutContents = file_put_contents($cacheFilePath, $content);

        if (!$filePutContents) {
            ExceptionUtility::throw('Fatal. CacheService was not able to create the "'.$cacheFilePath.'"-file.');
        }
    }

    /**
     * Deletes a cache by its identifier if its present.
     *
     * @param string $identifier The filename used to be called
     */
    public function remove(string $identifier)
    {
        $cacheFilePath = $this->getPath($identifier.'.'.$this->getFileExtension());

        if ('*' == $identifier) {
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
     * @todo Find a way to minify / compress it down the HTML as an oneliner.
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
