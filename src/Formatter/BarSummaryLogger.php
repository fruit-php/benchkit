<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;
use Fruit\ChartKit\HorizontalBarChart;

class BarSummaryLogger implements Summary
{
    public function format(array $bs)
    {
        foreach ($bs as $group => $b) {
            if ($group == '') {
                $group = 'Global benchmarks';
            }

            $loop = new HorizontalBarChart;
            foreach ($b as $bench) {
                $loop->add($bench->name, round($bench->loops()));
            }
            echo "\n\n$group - loops per time unit";
            echo $loop->render();
        }
    }
}
