<?php

namespace Andreas\StateMachine\Tests;

use Tests\TestCase;
use Andreas\StateMachine\StateMachine\StateMachine;
use Andreas\StateMachine\StateMachine\State;
use Andreas\StateMachine\StateMachine\Transition;
use Andreas\StateMachine\Traits\HasStateMachine;

class StateMachineTest extends TestCase
{
    public function testStateMachineCanLoadGraph()
    {
        $mock = new class {
            use HasStateMachine;
        };
        $mock->order_status_id = 0;
        $mock->initializeStateMachine();

        $this->assertEquals("open", $mock->getCurrentState()->name);
        $this->assertEquals(1, count($mock->getNextTransitions()));
    }

    public function testStateMachineCanTransitionNextState()
    {
        $mock = new class {
            use HasStateMachine;
        };
        $mock->order_status_id = 0;
        $mock->initializeStateMachine();

        $transition = $mock->getNextTransitions()[0];
        $mock->process($transition->name);

        $this->assertEquals("pending payment", $mock->getCurrentState()->name);
        $this->assertEquals(3, count($mock->getNextTransitions()));
    }


    public function testStateMachineFullOrderFlow()
    {
        $mock = new class {
            use HasStateMachine;
        };
        $mock->order_status_id = 0;
        $mock->initializeStateMachine();

        $transition = $mock->getNextTransitions()[0];
        $this->assertEquals("open", $mock->getCurrentState()->name);

        $this->assertEquals("open_to_pending payment", $transition->name);
        $mock->process($transition->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("pending payment", $mock->getCurrentState()->name);
        $this->assertEquals(3, count($transitions));

        $this->assertEquals("pending payment_to_paid", $transitions[1]->name);
        $mock->process($transitions[1]->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("paid", $mock->getCurrentState()->name);
        $this->assertEquals(1, count($transitions));

        $this->assertEquals("paid_to_shipped", $transitions[0]->name);
        $mock->process($transitions[0]->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("shipped", $mock->getCurrentState()->name);
        $this->assertEquals(0, count($transitions));
    }

    public function testStateMachinePaymentErrorFlow()
    {
        $mock = new class {
            use HasStateMachine;
        };
        $mock->order_status_id = 0;
        $mock->initializeStateMachine();

        $transition = $mock->getNextTransitions()[0];
        $this->assertEquals("open", $mock->getCurrentState()->name);

        $this->assertEquals("open_to_pending payment", $transition->name);
        $mock->process($transition->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("pending payment", $mock->getCurrentState()->name);
        $this->assertEquals(3, count($transitions));

        $this->assertEquals("pending payment_to_open", $transitions[0]->name);
        $mock->process($transitions[0]->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("open", $mock->getCurrentState()->name);
        $this->assertEquals(1, count($transitions));

        $this->assertEquals("open_to_pending payment", $transitions[0]->name);
    }

    public function testStateMachineCancellationOrderFlow()
    {
        $mock = new class {
            use HasStateMachine;
        };
        $mock->order_status_id = 0;
        $mock->initializeStateMachine();

        $transition = $mock->getNextTransitions()[0];
        $this->assertEquals("open", $mock->getCurrentState()->name);

        $this->assertEquals("open_to_pending payment", $transition->name);
        $mock->process($transition->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("pending payment", $mock->getCurrentState()->name);
        $this->assertEquals(3, count($transitions));

        $this->assertEquals("pending payment_to_cancelled", $transitions[2]->name);
        $mock->process($transitions[2]->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("cancelled", $mock->getCurrentState()->name);
        $this->assertEquals(0, count($transitions));
    }


    public function testStateMachineExceptionOnWrongTransition()
    {
        $mock = new class {
            use HasStateMachine;
        };
        $mock->order_status_id = 0;
        $mock->initializeStateMachine();

        $transition = $mock->getNextTransitions()[0];
        $mock->process($transition->name);

        $transitions = $mock->getNextTransitions();
        $this->assertEquals("pending payment", $mock->getCurrentState()->name);
        $this->assertEquals(3, count($transitions));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid transition: ' . $transition->name);
        $mock->process($transition->name);
    }
}
