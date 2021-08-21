<?php

declare(strict_types=1);

namespace Site\Core\Service;

class HtmlCacheService extends CacheService
{
    /**
     * Defines caching path and file extension of this class.
     */
    public function __construct()
    {
        $this->setPath('fileadmin/Cache/Html/');
        $this->setFileExtension('html');
    }
}
