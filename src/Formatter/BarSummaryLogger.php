<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;
use Fruit\ChartKit\HorizontalBarChart;

/**
 * BarSummaryLogger generates horizontal bar chart in console.
 *
 * This formatter accepts constructor argument in JSON format.
 *
 * Available arguments:
 *
 * - useTime: show time data instead of loop counts.
 */
class BarSummaryLogger extends AbstractSummary
{
    public function format(array $bs)
    {
        foreach ($bs as $group => $b) {
            if ($group == '') {
                $group = 'Global benchmarks';
            }
            $useTime = $this->test('useTime');
            $title = 'loops per time unit';
            if ($useTime) {
                $title = 'milliseconds per loop';
            }

            $loop = new HorizontalBarChart;
            foreach ($b as $bench) {
                $val = $bench->loops();
                if ($useTime) {
                    $val = $bench->runTime();
                }
                $loop->add($bench->name, $val);
            }
            echo $loop->render("$group - $title");
        }
    }
}
