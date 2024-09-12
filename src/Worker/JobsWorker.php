<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;

final class JobsWorker implements WorkerInterface
{
    public function start(): void
    {
        $consumer = new Consumer();
        $shouldBeRestarted = false;

        /** @var ReceivedTaskInterface $task */
        while ($task = $consumer->waitTask()) {
            try {
                var_dump($task);
                $task->ack();
            } catch (\Throwable $e) {
                $task->nack($e, $shouldBeRestarted);
            }
        }
    }
}
