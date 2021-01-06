<?php

declare(strict_types=1);

namespace Site\Core\Interfaces;

interface CacheInterface
{
    /**
     * Defines the path where the cached files
     * will be created and read from.
     *
     * @param string $path A relative path of the server
     *
     * @return void
     */
    public function setPath($path);

    /**
     * Retrieves the relative path where cached files are placed.
     *
     * @param string $identifier Optional. If given, it'll used the defined $this->path and appends the provided $identifier string additionally.
     *                           Must not start with a '/' (slash).
     *
     * @return void
     */
    public function getPath(string $identifier = '');

    /**
     * Defines the file-extension of cached files.
     * Could be anything, e.g. '.html' or '.json' etc.
     *
     * @param string $fileExtension The file-extension passed as string and without a '.' (dot) - just the extension itself in lowercase.
     *
     * @return void
     */
    public function setFileExtension($fileExtension);

    /**
     * Retrieves the file-extension of this cache service.
     *
     * @return void
     */
    public function getFileExtension();

    /**
     * Retrieves a cached file by its identifier.
     *
     * @param string        $identifier        Name of cached file without file-extension
     * @param \Closure|null $notCachedCallback Optional. If provided, this closure will be called
     *                                         in case there's no cached file found by the given identifier,
     *                                         which will then cache it immediately by the returned data.
     *
     * @return bool|string
     */
    public function get(string $identifier, \Closure $notCachedCallback = null);

    /**
     * Adds a new cache into the HTML-Caches file by using its $identifier as filename and $content as of the HTML-content.
     * If the given $identifier exists as an HTML-Cache already it'll return a true.
     *
     * @param string $identifier The filename used to be called
     * @param string $content    The actual HTML-Content which should be cached
     *
     * @return bool|void
     */
    public function add(string $identifier, string $content);

    /**
     * Deletes a cache by its identifier.
     *
     * @param string $identifier The identifier used to be deleted
     *
     * @return void
     */
    public function remove(string $identifier);
}
