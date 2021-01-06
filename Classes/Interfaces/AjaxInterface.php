<?php

declare(strict_types=1);

namespace Site\Core\Interfaces;

interface AjaxInterface
{
    /**
     * Process an incoming ajax call.
     *
     * Processes an incoming ajax call in order to produce a handling.
     * If unable to produce the response itself, it will do nothing and probably,
     * respectively the .htaccess, a 404 or just an empty page.
     */
    public function process();
}
