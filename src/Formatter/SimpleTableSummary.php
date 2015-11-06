<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;
use Fruit\ChartKit\SimpleTable;

/**
 * BarSummaryLogger generates horizontal bar chart in console.
 *
 * This formatter accepts constructor argument in JSON format.
 *
 * Available arguments:
 *
 * - useTime: show time data instead of loop counts.
 */
class SimpleTableSummary implements Summary
{
    public function format(array $bs)
    {
        foreach ($bs as $group => $b) {
            if ($group == '') {
                $group = 'Global benchmarks';
            }

            $data = array();

            foreach ($b as $bench) {
                $data[] = array(
                    $bench->name . " ",
                    sprintf(" %13.7fms ", $bench->runTime()),
                    sprintf(" %13.4floops ", $bench->loops())
                );
            }

            $table = new SimpleTable($data, "right");
            echo "\n" . $table->render($group);
        }
    }
}
