<?php

namespace Fruit\BenchKit;

use Exception;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

/**
 * This is a helper for benchmarking your application.
 *
 * You can register your benchmark function to it, then run the run()
 * method.
 *
 * @see Fruit\BenchKit\Benchmarker::register()
 * @see Fruit\BenchKit\Benchmarker::run()
 */
class Benchmarker
{
    private $victims;
    private $ttl;

    public function __construct($ttl = 1.0)
    {
        $this->ttl = ($ttl > 0)?$ttl:1.0;
        $this->victims = array();
    }

    private function checkParam($params)
    {
        if (count($params) != 1) {
            return false;
        }
        return true;
    }

    private function checkFunction($func)
    {
        $f = new ReflectionFunction($func);
        if (substr($f->getName(), 0, 9) != 'Benchmark') {
            return false;
        }
        return $this->checkParam($f->getParameters());
    }

    /**
     * Register a benchmark function.
     *
     * The benchmark function MUST be:
     * - A function, method is not acceptable.
     * - Receive exactly one parameter, which will be an instance of Fruit\Benchmark
     * - Prefix "Benchmark" in its name.
     *
     * Add type-hinting on the parameter is suggested.
     *
     * @param $func callable
     * @return boolean true on success, false otherwise
     */
    public function register(callable $func)
    {
        $valid = false;
        if (function_exists($func)) {
            $valid = $this->checkFunction($func);
        }

        if (! $valid) {
            return false;
        }

        $this->victims[$func] = new Benchmark($func, $this->ttl);
        return true;
    }

    /**
     * Run all benchmarks.
     *
     * @param $formatter callable to format the benchmark result. The formatter
     *                   will receive registered function, loops it run, and time it costs.
     * @return an associative array maps the benchmark function to their benchmark result.
     */
    public function run(callable $formatter = null)
    {
        $ret = array();
        foreach ($this->victims as $f => $b) {
            $data = $b->benchmark();
            $ret[$f] = $data;
            if ($formatter != null) {
                $formatter($f, $data);
            }
        }
        return $ret;
    }
}
