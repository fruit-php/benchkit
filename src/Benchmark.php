<?php

namespace Fruit\BenchKit;

/**
 * Benchmark is helper for you to write benchmark functions.
 *
 * The benchmark function will receive a Fruit\Benchmark instance,
 * you MUST use a for-loop to run your code for Fruit\Benchmark::N() times.
 *
 * The benchmark function might be called several times, be sure to setup
 * the environment properly, and clear it before exiting.
 */
class Benchmark
{
    private $n;

    private $start;
    private $waste;
    private $func;
    private $ttl = 1.0;

    public function __construct(callable $func, $ttl = 1.0)
    {
        $this->func = $func;
        $this->ttl = ($ttl > 0)?$ttl:1.0;
    }

    /**
     * Get number of times your test code should run.
     */
    public function N()
    {
        return $this->n;
    }

    /**
     * Get how much time costs to run N loops.
     */
    public function T()
    {
        return $this->waste;
    }

    /**
     * Reset the internal timer.
     *
     * If you have to do some stuff to setup the environment, and
     * that costs some time, you can call this to reset the timer before
     * really doing test stuff.
     */
    public function Reset()
    {
        $this->waste = 0.0;
        $this->start = microtime(true);
    }

    /**
     * Temporary stop the timer.
     */
    public function Pause()
    {
        $now = microtime(true);
        if ($this->start > 0.0) {
            $this->waste += $now - $this->start;
        }
        $this->start = 0.0;
    }

    /**
     * Resume the timer.
     */
    public function Resume()
    {
        if ($this->start <= 0.0) {
            $this->start = microtime(true);
        }
    }

    private function run($n)
    {
        $this->n = $n;
        $this->start = microtime(true);
        call_user_func($this->func, $this);
        $this->Pause();
        return $this->waste;
    }

    private function predict($n, $t)
    {
        if ($t == 0) {
            return $n * 10;
        }
        return ceil($this->ttl / $t * $n);
    }

    /**
     * Do the benchmark stuff.
     *
     * @return array of result in [$run_n_times, $cost_n_miliseconds] format
     */
    public function benchmark()
    {
        $this->start = 0;
        $this->waste = 0.0;
        $n = 1;
        $t = 0.0;
        while ($t < $this->ttl) {
            $this->Reset();
            $t = $this->run($n);
            $n = $this->predict($n, $t);
        }
        return array($n, $t);
    }
}
