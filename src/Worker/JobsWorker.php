<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker;

use FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker\JobsHandleRegistry;
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
        $consumer = new Consumer();
        $shouldBeRestarted = false;

        $this->kernel->boot();
        $handleRegistry = $this->kernel->getContainer()->get(JobsHandleRegistry::class);
        /** @var JobsHandleRegistry $handleRegistry*/

        /** @var ReceivedTaskInterface $task */
        while ($task = $consumer->waitTask()) {
            try {
                $handler = $handleRegistry->findHandlerByTask($task);

                if (empty($handler)) {
                    var_dump(sprintf('Handler for pipeline not found - %s, %s', $task->getPipeline(), $task->getQueue()));
                } else {
                    $handler->handle($task);
                }

                $task->ack();
            } catch (\Throwable $e) {
                var_dump(sprintf('Error queue %s', $e->getMessage()));
                $task->nack($e, $shouldBeRestarted);
            }
        }
    }
}
