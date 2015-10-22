<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

interface Summary
{
    public function format(array $b);
}
