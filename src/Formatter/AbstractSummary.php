<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

abstract class AbstractSummary implements Summary
{
    protected $conf;

    public function __construct($json = '')
    {
        $this->conf = json_decode($json, true);
    }

    protected function test($key)
    {
        if (isset($this->conf[$key])) {
            return $this->conf[$key];
        }
        return false;
    }

    abstract public function format(array $bs);
}
