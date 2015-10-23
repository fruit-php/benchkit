<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

class DefaultSummaryLogger implements Summary
{
    public function format(array $bs)
    {
        foreach ($bs as $group => $b) {
            $this->f($group, $b);
        }
    }

    private function f($group, array $bs)
    {
        if ($group == '') {
            $group = "Global benchmarks";
        }
        echo "\n$group:\n";
        $maxNameLength = 0;
        usort($bs, function($a, $b){
            $x = $a->runTime();
            $y = $b->runTime();

            if ($x > $y) {
                return 1;
            }
            if ($x < $y) {
                return -1;
            }
            return 0;
        });

        foreach ($bs as $b) {
            $sz = strlen($b->name);
            if ($sz > $maxNameLength) {
                $maxNameLength = $sz;
            }
        }

        $baseline = $bs[0]->runTime();
        foreach ($bs as $k => $b) {
            $t = $b->runTime();
            $msg = 'baseline';
            if ($k != 0) {
                $msg = sprintf('%d%% slower', round($t * 100.0 / $baseline) - 100);
            }
            echo sprintf('%' . $maxNameLength . "s  %16.6f ms/op  %16.6f ops  %s\n",
                         $b->name, $t, $b->loops(),
                         $msg);
        }
    }
}
