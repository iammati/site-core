<?php

namespace Site\Core;

/**
 * @author Mati Sediqi <mati_01@icloud.com>
 */
class Application
{
    protected string $basePath;

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }
}
