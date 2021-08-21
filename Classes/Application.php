<?php

namespace Site\Core;

/**
 * @author Mati Sediqi <mati_01@icloud.com>
 */
class Application
{
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }
}
