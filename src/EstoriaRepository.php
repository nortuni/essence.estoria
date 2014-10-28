<?php

namespace Nortuni\Essence\EventSourced\Estoria;

use Nortuni\Essence\AggregateId;
use Nortuni\Essence\AggregateNotFound;
use Nortuni\Essence\EventSourced\AggregateHistory;
use Nortuni\Essence\EventSourced\EventSourcedAggregate;
use Nortuni\Estoria\EventStore;
use Nortuni\Estoria\StreamId;
use Nortuni\Estoria\StreamNotFound;

abstract class EstoriaRepository
{
    /**
     * @var EventStore
     */
    protected $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @param AggregateId $aggregateId
     * @return StreamId
     */
    public static function getStreamIdForAggregateId(AggregateId $aggregateId)
    {
        return new StreamId((string)$aggregateId);
    }

    /**
     * @param AggregateId $aggregateId
     * @return AggregateHistory
     * @throws AggregateNotFound
     */
    protected function findAggregateHistory(AggregateId $aggregateId)
    {
        try {
            $stream = $this->eventStore->getStream(self::getStreamIdForAggregateId($aggregateId));
            return new EstoriaAggregateHistory($aggregateId, $stream);
        } catch (StreamNotFound $exception) {
            throw new AggregateNotFound("$aggregateId not found", 0, $exception);
        }
    }

    /**
     * @param EventSourcedAggregate $eventSourcedAggregate
     * @param StreamId $streamId
     */
    protected function addToStream(EventSourcedAggregate $eventSourcedAggregate, StreamId $streamId)
    {
        $stream = $this->eventStore->getOrCreateStream($streamId);
        $domainEvents = $eventSourcedAggregate->getRecordedEvents();
        $stream->append(new EssenceCommit($domainEvents));
        $eventSourcedAggregate->clearRecordedEvents();
    }
}
