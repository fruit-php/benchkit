<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

interface Formatter
{
    /**
     * @param $name string benchmark name
     */
    public function format(Benchmark $b);

    public function formatAll(array $b);
}
