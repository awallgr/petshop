<?php

namespace Andreas\StateMachine;

use Illuminate\Support\ServiceProvider;

class StateMachineServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/graph.json' => config_path('graph.json'),
        ], 'config');
    }
}
