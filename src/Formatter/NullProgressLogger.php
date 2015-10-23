<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

class NullProgressLogger implements Progress
{
    public function format($group, Benchmark $b)
    {
    }
}
