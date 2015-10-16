<?php

use Fruit\BenchKit\Benchmark;

function myfunc($i)
{
    return $i;
}

function BenchmarkCUFA(Benchmark $b)
{
    for ($i = 0; $i < $b->N(); $i++) {
        call_user_func_array('myfunc', array($i));
    }
}

function BenchmarkCUF(Benchmark $b)
{
    for ($i = 0; $i < $b->N(); $i++) {
        call_user_func('myfunc', $i);
    }
}

function BenchmarkDynamic(Benchmark $b)
{
    $a = "myfunc";
    for ($i = 0; $i < $b->N(); $i++) {
        $a($i);
    }
}

function BenchmarkStatic(Benchmark $b)
{
    for ($i = 0; $i < $b->N(); $i++) {
        myfunc($i);
    }
}
