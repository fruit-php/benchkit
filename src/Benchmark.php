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
    private $unit = 1.0;
    private $opu; // operations per time-unit
    private $msop; // milliseconds per operation
    public $name;

    public function __construct($name, callable $func, $unit = 1.0)
    {
        $this->func = $func;
        $this->unit = ($unit > 0)?$unit:1.0;
        $this->name = $name;
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

    /**
     * run time per loop (millisecond)
     */
    public function runTime()
    {
        return $this->msop;
    }

    /**
     * loop running per time unit
     */
    public function loops()
    {
        return $this->opu;
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
        return ceil($this->unit / $t * $n);
    }

    /**
     * Do the benchmark stuff.
     */
    public function Benchmark()
    {
        $this->start = 0;
        $this->waste = 0.0;
        $n = 1;
        $t = 0.0;
        while ($t < $this->unit) {
            $this->Reset();
            $t = $this->run($n);
            $n = $this->predict($n, $t);
        }
        $this->opu = $this->N() / $this->T();
        $this->msop = $this->T() * 1000.0 / $this->N();
        return $this;
    }
}
