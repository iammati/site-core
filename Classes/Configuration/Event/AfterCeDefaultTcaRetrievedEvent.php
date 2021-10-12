<?php

declare(strict_types=1);

namespace Site\Core\Configuration\Event;

final class AfterCeDefaultTcaRetrievedEvent
{
    private ?string $tabName = null;
    private ?string $showitem = null;
    private ?string $CType = null;
    private ?string $fields = null;

    public function getTabName(): string
    {
        return $this->tabName;
    }

    public function setTabName(string $tabName): self
    {
        $this->tabName = $tabName;

        return $this;
    }

    public function setShowitem(string $showitem)
    {
        $this->showitem = $showitem;

        return $this;
    }

    public function getShowitem(): string
    {
        return $this->showitem;
    }

    public function getCType(): string
    {
        return $this->CType;
    }

    public function setCType(string $CType): self
    {
        $this->CType = $CType;

        return $this;
    }

    public function getFields(): string
    {
        return $this->fields;
    }

    public function setFields(string $fields): self
    {
        $this->fields = $fields;

        return $this;
    }
}
