<?php

namespace Andreas\StateMachine\StateMachine;

class Transition
{
    public string $name;
    public string $from;
    public string $to;
    /**
     * @var array<mixed>
     */
    public array $metadata;

    /**
     * @param array<mixed> $metadata
     */
    public function __construct(string $name, string $from, string $to, array $metadata = [])
    {
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
        $this->metadata = $metadata;
    }
}
