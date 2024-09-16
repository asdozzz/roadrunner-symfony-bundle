<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker;

use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

interface JobsHandlerInterface
{
    const ATTEMPT_HEADER = 'attempts';
    const RETRY_DELAY_HEADER = 'retry-delay';
    public function isSupported(ReceivedTaskInterface $task): bool;

    public function handle(ReceivedTaskInterface $task): void;
}
