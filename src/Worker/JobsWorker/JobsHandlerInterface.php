<?php

namespace FluffyDiscord\RoadRunnerBundle\Worker\JobsWorker;

use Spiral\RoadRunner\Jobs\Task\ReceivedTaskInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

interface JobsHandlerInterface
{
    public function isSupported(ReceivedTaskInterface $task): bool;

    public function handle(ReceivedTaskInterface $task): void;
}
