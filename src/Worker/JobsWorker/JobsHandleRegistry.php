<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

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

    public function findHandlerByTask(ReceivedTaskInterface $task): ?JobsHandlerInterface
    {
        $result = null;

        foreach ($this->handlers as $handler) {
            if ($handler->isSupported($task)) {
                $result = $handler;
                break;
            }
        }

        return $result;
    }
}
