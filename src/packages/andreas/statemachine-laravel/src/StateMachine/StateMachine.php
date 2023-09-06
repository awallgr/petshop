<?php

namespace Andreas\StateMachine\StateMachine;

class StateMachine
{
    private State $currentState;
    /** @var State[] $states */
    private array $states = [];
    /** @var Transition[] $transitions */
    private array $transitions = [];

    public function addState(State $state): void
    {
        $this->states[$state->name] = $state;
    }

    public function addTransition(Transition $transition): void
    {
        $this->transitions[$transition->name] = $transition;
    }

    public function changeCurrentState(State $state): void
    {
        $this->currentState = $state;
    }

    public function changeCurrentStateFromName(string $stateName): void
    {
        $state = $this->getStateFromName($stateName);
        if ($state !== null) {
            $this->changeCurrentState($state);
        }
    }

    public function getCurrentState(): State
    {
        return $this->currentState;
    }

    /**
     * @return Transition[]
     */
    public function getNextTransitions(): array
    {
        $availableTransitions = [];
        foreach ($this->transitions as $transition) {
            if ($transition->from === $this->currentState->name) {
                $availableTransitions[] = $transition;
            }
        }
        return $availableTransitions;
    }

    public function process(string $transitionName): void
    {
        $transition = $this->getTransitionFromName($transitionName);
        if ($transition !== null && $this->isValidNextTransition($transition)) {
            $this->changeCurrentStateFromTransition($transition);
        } else {
            throw new \Exception("Invalid transition: " . $transitionName);
        }
    }

    public function isValidNextTransition(Transition $transition): bool
    {
        if ($transition == null) {
            return false;
        }

        $allowedTransitionNames = array_map(fn ($transition) => $transition->name, $this->getNextTransitions());
        return in_array($transition->name, $allowedTransitionNames);
    }

    public function isValidNextState(State $state): bool
    {
        return $this->getNextTransitionFromStateName($state->name) !== null ? true : false;
    }

    public function getStateFromName(string $stateName): ?State
    {
        return $this->states[$stateName] ?? null;
    }

    public function getTransitionFromName(string $transitionName): ?Transition
    {
        return $this->transitions[$transitionName] ?? null;
    }

    public function getTransitionToState(State $state): ?Transition
    {
        return $this->getNextTransitionFromStateName($state->name);
    }

    private function changeCurrentStateFromTransition(Transition $transition): void
    {
        $state = $this->states[$transition->to];
        if ($state !== null) {
            $this->changeCurrentState($state);
        }
    }

    private function getNextTransitionFromStateName(string $stateName): ?Transition
    {
        foreach ($this->getNextTransitions() as $transition) {
            if ($transition->to === $stateName) {
                return $transition;
            }
        }
        return null;
    }
}
