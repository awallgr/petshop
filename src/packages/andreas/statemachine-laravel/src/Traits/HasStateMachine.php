<?php

namespace Andreas\StateMachine\Traits;

use Andreas\StateMachine\StateMachine\State;
use Andreas\StateMachine\StateMachine\Transition;
use Andreas\StateMachine\StateMachine\StateMachine;

trait HasStateMachine
{
    private string $stateMachineName;
    private StateMachine $stateMachine;

    protected static function bootHasStateMachine(): void
    {
        static::retrieved(function ($model) {
            $model->initializeStateMachine();
        });
    }

    public function initializeStateMachine(): void
    {
        $this->stateMachine = new StateMachine();
        $content = file_get_contents(config_path('graph.json'));
        if ($content !== false) {
            $this->setGraph($content);
        }
    }

    public function setGraph(string $graphJson): void
    {
        $graph = $this->parseGraph($graphJson);
        $this->initiateStateAndTransitions($graph);
    }

    public function changeCurrentState(State $state): void
    {
        $this->stateMachine->changeCurrentState($state);
    }

    public function changeCurrentStateFromName(string $stateName): void
    {
        $this->stateMachine->changeCurrentStateFromName($stateName);
    }

    public function getCurrentState(): State
    {
        return $this->stateMachine->getCurrentState();
    }

    /**
     * @return array<Transition>
     */
    public function getNextTransitions(): array
    {
        return $this->stateMachine->getNextTransitions();
    }

    /**
     * @return $this
     */
    public function process(string $transitionName)
    {
        $this->stateMachine->process($transitionName);
        return $this;
    }

    public function isValidNextState(State $state): bool
    {
        return $this->stateMachine->isValidNextState($state);
    }

    public function getStateFromName(string $stateName): ?State
    {
        return $this->stateMachine->getStateFromName($stateName);
    }

    public function getTransitionToState(State $state): ?Transition
    {
        return $this->stateMachine->getTransitionToState($state);
    }

    /**
     * @return array<mixed>
     */
    private function parseGraph(string $graphJson): array
    {
        $graph = json_decode($graphJson, true);
        $this->stateMachineName = $graph['graph'];
        return $graph;
    }

    /**
     * @param array<mixed> $graph
     */
    private function initiateStateAndTransitions(array $graph): void
    {
        $this->initiateStates($graph['states']);
        $this->initiateTransitions($graph['transitions']);
        $this->initiateInitialState(reset($graph['states']));
    }

    /**
     * @param array<mixed> $states
     */
    private function initiateStates(array $states): void
    {
        foreach ($states as $state) {
            $this->stateMachine->addState(new State($state['title'], $state));
        }
    }

    /**
     * @param array<mixed> $transitions
     */
    private function initiateTransitions(array $transitions): void
    {
        foreach ($transitions as $data) {
            $transition = $data[0];

            $fromState = $transition['from'];
            $toStates = $transition['to'];

            foreach ($toStates as $state) {
                $this->stateMachine->addTransition(new Transition(
                    $fromState . "_to_" . $state,
                    $fromState,
                    $state,
                    $transition
                ));
            }
        }
    }

    /**
     * @param array<mixed> $initialState
     */
    private function initiateInitialState(array $initialState): void
    {
        $this->stateMachine->changeCurrentState(new State($initialState['title'], $initialState));
    }
}
