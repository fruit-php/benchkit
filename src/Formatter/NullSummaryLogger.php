<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

class NullSummaryLogger implements Summary
{
    public function format(array $bs)
    {
    }
}
