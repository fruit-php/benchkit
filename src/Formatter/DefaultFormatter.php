<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

class DefaultFormatter implements Formatter
{
    public function format(Benchmark $b)
    {
        echo sprintf("%s ... %d loops in %f ms\n",
                     $b->name,
                     $b->N(),
                     $b->T()*1000.0);
    }

    public function formatAll(array $bs)
    {
        echo "\n";
        $maxNameLength = 0;
        usort($bs, function($a, $b){
                $x = $a->N()/$a->T();
                $y = $b->N()/$a->T();

                if ($x > $y) {
                    return -1;
                }
                if ($x < $y) {
                    return 1;
                }
                return 0;
        });

        foreach ($bs as $b) {
            $sz = strlen($b->name);
            if ($sz > $maxNameLength) {
                $maxNameLength = $sz;
            }
        }

        $baseline = $bs[0]->T()*1000.0/$bs[0]->N();
        foreach ($bs as $k => $b) {
            $t = $b->T()*1000.0/$b->N();
            $msg = 'baseline';
            if ($k != 0) {
                $msg = sprintf('%d%% slower', round($t * 100.0 / $baseline) - 100);
            }
            echo sprintf('%' . $maxNameLength . "s  %16.6f ms/op  %16.6f op/s  %s\n",
                         $b->name, $t, $b->N()/$b->T(),
                         $msg);
        }
    }
}
