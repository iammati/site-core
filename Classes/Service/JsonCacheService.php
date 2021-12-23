<?php

declare(strict_types=1);

namespace Site\Core\Service;

class JsonCacheService extends CacheService
{
    public function __construct()
    {
        $this->setPath('fileadmin/Cache/Json/');
        $this->setFileExtension('json');
    }
}
