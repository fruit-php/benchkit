<?php

namespace Fruit\BenchKit;

use Fruit\BenchKit\Formatter\Formatter;
use Exception;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionException;

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

    private function checkParam($name, $params)
    {
        if (count($params) != 1) {
            return '';
        }
        return $name;
    }

    private function checkFunction($func)
    {
        $f = new ReflectionFunction($func);
        $name = $f->getName();
        if ($f->isClosure()) {
            $name = '(CLOSURE)';
        }
        return $this->checkParam($name, $f->getParameters());
    }

    private function checkMethodArray(array $func)
    {
        if (count($funct) != 2) {
            return '';
        }
        $cls = null;
        $method = null;
        try {
            $cls = new ReflectionClass($func[0]);
            $method = $cls->getMethod($func[1]);
        } catch (ReflectionException $e) {
            return '';
        }

        $name = $cls->getName() . '::' . $method->getName();
        return $this->checkParam($name, $method->getParameters());
    }

    /**
     * Register a benchmark function.
     *
     * The benchmark function has following restrictions:
     * - Receive exactly one parameter, which will be an instance of Fruit\Benchmark
     * - "Class::StaticMethod" format is not acceptable. Use array format instead.
     *
     * Add type-hinting on the parameter is suggested.
     *
     * @param $func callable
     * @return boolean true on success, false otherwise
     */
    public function register(callable $func)
    {
        $name = '';
        if (function_exists($func)) {
            $name = $this->checkFunction($func);
        } elseif (is_array($func)) {
            // method
            $name = $this->checkMethodArray($func);
        }

        if ($name == '') {
            return false;
        }

        $this->victims[$func] = new Benchmark($name, $func, $this->ttl);
        return true;
    }

    /**
     * Run all benchmarks.
     *
     * @param $formatter callable to format the benchmark result. The formatter
     *                   will receive registered function, loops it run, and time it costs.
     * @return an associative array maps the benchmark function to their benchmark result.
     */
    public function run(Formatter $formatter)
    {
        foreach ($this->victims as $f => $b) {
            $formatter->format($b->Benchmark());
        }
        $formatter->formatAll($this->victims);
    }
}
