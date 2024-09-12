<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker;

final class JobsHandleRegistry
{
    /**
     * @var iterable<JobsHandlerInterface>
     * */
    private iterable $handlers;

    public function __construct(iterable $handlers = [])
    {
        $this->handlers = $handlers;
    }

    public function findHandlerByQueueName(string $queueName): ?JobsHandlerInterface
    {
        $result = null;

        foreach ($this->handlers as $handler) {
            if ($handler->isSupportedQueue($queueName)) {
                $result = $handler;
                break;
            }
        }

        return $result;
    }
}
