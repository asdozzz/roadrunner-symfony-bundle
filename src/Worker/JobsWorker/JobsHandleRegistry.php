<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final class JobsHandleRegistry
{
    /**
     * @var iterable<JobsHandlerInterface>
     * */
    private iterable $handlers;

    public function __construct(#[TaggedIterator('roadrunner_jobs.handler')] iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    public function findHandlerByQueueName(string $queueName): ?JobsHandlerInterface
    {
        $result = null;

        foreach ($this->handlers as $handler) {
            if ($handler->isSupportedQueue($queueName)) {
                $result = $handler;
            }
        }

        return $result;
    }
}
