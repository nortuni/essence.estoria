<?php

namespace Nortuni\Essence\EventSourced\Estoria;

use Nortuni\Essence\EventSourced\DomainEvent;
use Nortuni\Estoria\Commit;
use Rhumsaa\Uuid\Uuid;

final class EssenceCommit implements Commit
{
    /**
     * @var DomainEvent[]
     */
    private $domainEvents;

    public function __construct(array $domainEvents)
    {
        $this->domainEvents = $domainEvents;
    }

    public function getEvents()
    {
        return array_map(
            function(DomainEvent $event) {
                return (object) [
                    'eventId' => (string) Uuid::uuid4(),
                    'eventType' => str_replace('\\', '.', get_class($event)),
                    'data' => $event->toPayload(),
                    'metadata' => (object) [],
                ];
            }, $this->domainEvents
        );
    }

    public function equals(Commit $other)
    {
        return $other instanceof self && $this->domainEvents == $other->domainEvents;
    }
}