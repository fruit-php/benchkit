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

    private static function checkParam($name, $params)
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

    private static function checkFunction($func)
    {
        $f = new ReflectionFunction($func);
        $name = $f->getName();
        if ($f->isClosure()) {
            $name = '(CLOSURE)';
        }
        return self::checkParam($name, $f->getParameters());
    }

    private static function checkMethodArray(array $func)
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
        $ret = array($cls->getName(), $func, self::checkParam($name, $method->getParameters()));
        if ($ret[2] !== '' and !$method->isStatic()) {
            $ret[1][0] = $cls->newInstance();
        }
        return $ret;
    }

    public function registerClass($clsname)
    {
        $cls = new ReflectionClass($clsname);
        $methods = $cls->getMethods();
        foreach ($methods as $m) {
            $fn = $m->getName();
            if ($m->isConstructor() or
                $m->isDestructor() or
                $m->isAbstract() or
                !$m->isPublic()) {

                continue;
            }
            list($group, $func, $name) = self::checkMethodArray(array($clsname, $fn));
            if ($name === '') {
                continue;
            }
            $cb = array($clsname, $m->getName());
            if (!$m->isStatic()) {
                $cb[0] = $cls->newInstance();
            }

            if (!isset($this->victims[$group])) {
                $this->victims[$group] = array();
            }
            $this->victims[$group][] = new Benchmark($name, $func, $this->ttl);
        }
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
            list($group, $func, $name) = self::checkMethodArray($func);
        } elseif (function_exists($func)) {
            $name = self::checkFunction($func);
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
