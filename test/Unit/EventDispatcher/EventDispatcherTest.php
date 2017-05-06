<?php
declare(strict_types=1);

namespace Test\Unit\Common\EventDispatcher;

use Common\EventDispatcher\EventDispatcher;

final class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_dispatches_events_to_generic_subscribers_and_domain_specific_subscribers()
    {
        $dispatcher = new EventDispatcher();
        $event = new \stdClass();

        $notifiedSubscribers = [];
        $subscriber1 = function () use (&$notifiedSubscribers) {
            $notifiedSubscribers[] = 'subscriber1';
        };
        $subscriber2 = function () use (&$notifiedSubscribers) {
            $notifiedSubscribers[] = 'subscriber2';
        };
        $subscriber3 = function () use (&$notifiedSubscribers) {
            $notifiedSubscribers[] = 'subscriber3';
        };

        $dispatcher->registerSubscriber('stdClass', $subscriber1);
        $dispatcher->registerSubscriber('Some\Other\Class', $subscriber2);
        $dispatcher->subscribeToAllEvents($subscriber3);

        $dispatcher->dispatch($event);

        $this->assertSame(
            ['subscriber3', 'subscriber1'],
            $notifiedSubscribers
        );
    }

    /**
     * @test
     */
    public function it_dispatches_all_given_events_()
    {
        $dispatcher = new EventDispatcher();
        $events = [
            new \stdClass(),
            new \stdClass()
        ];

        $receivedEvents = [];
        $subscriber = function ($event) use (&$receivedEvents) {
            $receivedEvents[] = $event;
        };

        $dispatcher->subscribeToAllEvents($subscriber);

        $dispatcher->dispatchAll($events);

        $this->assertSame(
            $events,
            $receivedEvents
        );
    }
}
