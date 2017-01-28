<?php
declare(strict_types = 1);

namespace Common\DomainModel;

/**
 * This trait should be used to make a domain event (a simple object) *publishable*.
 */
trait PublishableDomainEvent
{
    /**
     * @return string A unique identifier for the *type* of event.
     */
    abstract public function eventType() : string;

    /**
     * @return array A serializable array of data that completely represents this event object.
     */
    public function eventData() : array
    {
        return array_merge(
            get_object_vars($this),
            ['_type' => $this->eventType()]
        );
    }
}
