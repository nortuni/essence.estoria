<?php

namespace Nortuni\Essence\EventSourced\Estoria;

use Nortuni\Essence\AggregateId;
use Nortuni\Essence\EventSourced\AggregateHistory;
use Nortuni\Estoria\Stream;

final class EstoriaAggregateHistory implements AggregateHistory
{
    /**
     * @var AggregateId
     */
    private $aggregateId;

    /**
     * @var Stream
     */
    private $stream;

    public function __construct(AggregateId $aggregateId, Stream $stream)
    {
        $this->aggregateId = $aggregateId;
        $this->stream = $stream;
    }

    public function current()
    {
        $event = $this->stream->current();
        $class = str_replace('.', '\\', $event->getEventType());
        $domainEvent = $class::fromPayload($event->getData());
        return $domainEvent;
    }

    public function next()
    {
        $this->stream->next();
    }

    public function key()
    {
        return $this->stream->key();
    }

    public function valid()
    {
        return $this->stream->valid();
    }

    public function rewind()
    {
        $this->stream->rewind();
    }

    /**
     * @return AggregateId
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }
}
