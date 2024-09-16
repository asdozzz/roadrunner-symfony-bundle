<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker;

use FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker\JobsHandleRegistry;
use FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker\JobsHandlerInterface;
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

        /** @var ReceivedTaskInterface $task */
        while ($task = $consumer->waitTask()) {
            try {
                $this->kernel->boot();
                $handleRegistry = $this->kernel->getContainer()->get(JobsHandleRegistry::class);
                /** @var JobsHandleRegistry $handleRegistry*/
                $handler = $handleRegistry->findHandlerByTask($task);

                if (empty($handler)) {
                    var_dump(sprintf('Handler for pipeline not found - %s, %s', $task->getPipeline(), $task->getQueue()));
                } else {
                    var_dump(sprintf('Handler: %s', $handler::class));
                    $handler->handle($task);
                }

                $task->ack();
            } catch (\Throwable $e) {
                var_dump(sprintf('Error queue %s', $e->getMessage()));
                $attempt = (int)$task->getHeaderLine(JobsHandlerInterface::ATTEMPT_HEADER) - 1;
                $delay = (int)$task->getHeaderLine(JobsHandlerInterface::RETRY_DELAY_HEADER);

                if (!empty($attempt) && $attempt > 0) {
                    $task->withHeader(JobsHandlerInterface::ATTEMPT_HEADER, $attempt)->withDelay($delay)->requeue($e);
                } else {
                    $task->nack($e, false);
                }
            }
        }
    }
}
