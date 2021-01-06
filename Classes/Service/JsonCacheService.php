<?php

declare(strict_types=1);

namespace Site\Core\Service;

class JsonCacheService extends CacheService
{
    /**
     * Defines caching path and file extension of this class.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setPath('fileadmin/Cache/Json/');
        $this->setFileExtension('json');
    }
}
