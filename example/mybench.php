<?php

use Fruit\BenchKit\Benchmark;

function myfunc($i)
{
    return $i;
}

function BenchmarkCUFA(Benchmark $b)
{
    for ($i = 0; $i < $b->n; $i++) {
        call_user_func_array('myfunc', array($i));
    }
}

function BenchmarkCUF(Benchmark $b)
{
    for ($i = 0; $i < $b->n; $i++) {
        call_user_func('myfunc', $i);
    }
}

function BenchmarkDynamic(Benchmark $b)
{
    $a = "myfunc";
    for ($i = 0; $i < $b->n; $i++) {
        $a($i);
    }
}

function BenchmarkStatic(Benchmark $b)
{
    for ($i = 0; $i < $b->n; $i++) {
        myfunc($i);
    }
}

class MyTest
{
    public static function run()
    {
    }
}

class MyBench
{
    public function BenchmarkStatic(Benchmark $b)
    {
        for ($i = 0; $i < $b->n; $i++) {
            MyTest::run();
        }
    }

    public function BenchmarkObject(Benchmark $b)
    {
        for ($i = 0; $i < $b->n; $i++) {
            (new MyTest)->run();
        }
    }

    public function BenchmarkStaticDynamic(Benchmark $b)
    {
        for ($i = 0; $i < $b->n; $i++) {
        $cb = ['MyTest', 'run'];
            $cb();
        }
    }

    public function BenchmarkObjectDynamic(Benchmark $b)
    {
        for ($i = 0; $i < $b->n; $i++) {
            $cb = [new MyTest, 'run'];
            $cb();
        }
    }
}
