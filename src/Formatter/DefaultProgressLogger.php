<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

class DefaultProgressLogger implements Progress
{
    public function format(Benchmark $b)
    {
        echo sprintf("%s ... %d loops in %f ms\n",
                     $b->name,
                     $b->N(),
                     $b->T()*1000.0);
    }
}
