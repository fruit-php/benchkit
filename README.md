# BenchKit

This package is part of Fruit Framework.

BenchKit is set of tools helping you benchmark your program.

[![Build Status](https://travis-ci.org/Ronmi/benchkit.svg)](https://travis-ci.org/Ronmi/benchkit)

## Synopsis

See `mybench.php` in `example` folder.

## How to write benchmarks

A benchmark test must be function or public method. It must receive only one parameter with type-hinting.

## Organize your benchmarks

Benchmarks are grouped by their class. So it's suggested to put different group of benchmarks into different files.

Benchmark function are collected into an unnamed group.

## Command line helper

`bench` is command line benchmark runner. It will scan specified directory recursivly, find out all benchmark functions, and run. For example:

```sh
bench run example
```

Thanks to [CLIFramework](https://github.com/c9s/CLIFramework), you can run `bench help run` to see supported command line arguments.

`bench` does not support construct arguments, you have to write your own benchmark executor to register benchmarks and run it.

### XHProf

You can gather xhprof data when running benchmarks with default command line runner by

- enable `--xhprof` option and
- use `Fruit\BenchKit\Formatter\XhprofSummary` summary formatter.

### Passing constructor arguments to formatter

You can pass a string as constructor argument to formatter with `--sa` and `--pa` options.

### Generate multiple summary without running benchmarks several times

Use `Fruit\BenchKit\Formatter\ChainSummary`, and pass constructor argument to specify what formatters you want.

```sh
bench run -s 'Fruit\BenchKit\Formatter\ChainSummary' --sa '{"chain":["Fruit\\BenchKit\\Formatter\\HighChartSummary":"{\"type\":\"time\"}", "Fruit\\BenchKit\\Formatter\\XhprofSummary":""]}' -p 'Fruit\BenchKit\Formatter\NullProgressLogger' example > /tmp/chart.html
```

Beware about shell escaping and php string escaping because builtin formatters accepts argument in JSON format. Use `--argdebug` when in doubt.

## License

Any version of MIT, GPL or LGPL.
