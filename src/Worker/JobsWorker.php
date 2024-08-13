<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker;

use FluffyDiscord\RoadRunnerBundle\Worker\WorkerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

final class JobsWorker implements WorkerInterface
{
    public function __construct(private KernelInterface $kernel) {

    }
    public function start(): void
    {
        $this->kernel->boot();
        $container = $this->kernel->getContainer();
        $consumer = new Consumer();
        $shouldBeRestarted = false;
        $logger = $container->get(LoggerInterface::class);

        /** @var ReceivedTaskInterface $task */
        while ($task = $consumer->waitTask()) {
            try {
                $logger->info(sprintf('Starting job %d %s', $task->getId(), $task->getName()));
                $task->ack();
            } catch (\Throwable $e) {
                $task->nack($e, $shouldBeRestarted);
            }
        }
    }
}
