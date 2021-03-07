<?php

namespace Site\Core\Configuration\Event;

final class AfterCeDefaultTcaRetrievedEvent
{
    /**
     * @var string
     */
    private $showitem;

    public function __construct(string $showitem)
    {
        $this->showitem = $showitem;
    }

    public function getShowitem(): string
    {
        return $this->showitem;
    }

    public function setShowitem(string $showitem)
    {
        $this->showitem = $showitem;
    }
}
