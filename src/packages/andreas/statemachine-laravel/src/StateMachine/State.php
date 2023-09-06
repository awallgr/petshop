<?php

namespace Andreas\StateMachine\StateMachine;

class State
{
    public string $name;
    /**
     * @var array<mixed>
     */
    public array $metadata;

    /**
     * @param array<mixed> $metadata
     */
    public function __construct(string $name, array $metadata = [])
    {
        $this->name = $name;
        $this->metadata = $metadata;
    }
}
