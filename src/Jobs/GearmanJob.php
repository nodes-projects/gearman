<?php

namespace NodesGearman\Jobs;

use GearmanWorker;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;

class GearmanJob extends Job implements JobContract {

    protected $worker;

    protected $job;

    protected $rawPayload = '';

    protected $attempts = 0;

    public function __construct(Container $container, GearmanWorker $worker, $queue, array $job)
    {
        $this->worker = $worker;
        $this->job = $job;
        $this->queue = $queue;
        $this->container = $container;

        $this->attempts = $this->attempts + 1;

        $this->worker->addFunction($queue, [$this, 'onGearmanJob']);
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        \Log::listen(function($level, $message, $context)
        {
            print '[' . $level . '] ' . $message;
            print "\n";
        });

        while($this->worker->work() || $this->worker->returnCode() == GEARMAN_TIMEOUT) {

        }
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->rawPayload;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();
    }

    /**
     * Release the job back into the queue.
     *
     * @todo missing implementation
     *
     * @param  int   $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->attempts;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return base64_encode($this->job);
    }

    /**
     * Get the IoC container instance.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function getGearmanWorker() {
        return $this->worker;
    }

    public function onGearmanJob(\GearmanJob $job) {
        $this->rawPayload = $job->workload();

        $this->resolveAndFire(json_decode($this->rawPayload, true));
    }
}
