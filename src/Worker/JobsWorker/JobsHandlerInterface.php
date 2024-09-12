<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker;

use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('roadrunner_jobs.handler')]
interface JobsHandlerInterface
{
    public function isSupportedQueue(string $queueName): bool;

    public function handle(ReceivedTaskInterface $task): void;
}
