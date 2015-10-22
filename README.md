# BenchKit

This package is part of Fruit Framework.

BenchKit is set of tools helping you benchmark your program.

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

`bench` does not support construct arguments, you have to write your own benchmark executor to register benchmarks and run it.

## License

Any version of MIT, GPL or LGPL.
