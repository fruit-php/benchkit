<?php

namespace Fruit\BenchKit;

use Fruit\BenchKit\Formatter\Progress;
use Fruit\BenchKit\Formatter\Summary;
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
    private $xhprof;

    public function __construct($ttl = 1.0, $xhprof = false)
    {
        $this->ttl = ($ttl > 0)?$ttl:1.0;
        $this->victims = array();
        $this->xhprof = $xhprof == true;
    }

    private function checkParam($name, $params)
    {
        if (count($params) != 1) {
            return '';
        }

        list($p) = $params;
        $t = $p->getClass();
        if ($t == null or $t->getName() != 'Fruit\BenchKit\Benchmark') {
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
        if (count($func) != 2) {
            return array('', '');
        }
        $cls = null;
        $method = null;
        try {
            $cls = new ReflectionClass($func[0]);
            $method = $cls->getMethod($func[1]);
        } catch (ReflectionException $e) {
            return array('', '');
        }

        $name = $method->getName();
        return array($cls->getName(), $this->checkParam($name, $method->getParameters()));
    }

    /**
     * Register a benchmark function.
     *
     * The benchmark function has following restrictions:
     * - Receive exactly one parameter, which will be an instance of Fruit\Benchmark
     * - "Class::StaticMethod" format is not acceptable. Use array format instead.
     * - Must have type-hinting.
     *
     * @param $func callable
     * @return boolean true on success, false otherwise
     */
    public function register(callable $func)
    {
        $name = '';
        $group = '';
        if (is_array($func)) {
            // method
            list($group, $name) = $this->checkMethodArray($func);
        } elseif (function_exists($func)) {
            $name = $this->checkFunction($func);
        }

        if ($name == '') {
            return false;
        }

        if (!isset($this->victims[$group])) {
            $this->victims[$group] = array();
        }
        $this->victims[$group][] = new Benchmark($name, $func, $this->ttl);
        return true;
    }

    /**
     * Run all benchmarks.
     *
     * @param $formatter callable to format the benchmark result. The formatter
     *                   will receive registered function, loops it run, and time it costs.
     * @return an associative array maps the benchmark function to their benchmark result.
     */
    public function run(Summary $sum, Progress $pro)
    {
        foreach ($this->victims as $group => $bs) {
            foreach ($bs as $b) {
                $pro->format($group, $b->Benchmark($this->xhprof));
            }
        }
        $sum->format($this->victims);
    }
}
