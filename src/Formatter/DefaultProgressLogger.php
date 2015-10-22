<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

class DefaultProgressLogger implements Progress
{
    public function format($group, Benchmark $b)
    {
        if ($group != '') {
            $group .= '::';
        }
        echo sprintf("%s ... %d loops in %f ms\n",
                     $group . $b->name,
                     $b->N(),
                     $b->T()*1000.0);
    }
}
