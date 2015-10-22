<?php

namespace Fruit\BenchKit\Bin;

use CLIFramework\Command;
use Fruit\PathKit\Path;
use Fruit\BenchKit\Benchmarker;
use Fruit\BenchKit\Formatter\DefaultFormatter;

class RunCommand extends Command
{
    public function options($opt)
    {
        $i = 'run this file to initialize the environment. (setting up db, autoload etc.)';
        $opt->add('i|init?', $i)->isa('file');

        $b = 'running benchmark for at least this amount of time(seconds), default to 1';
        $opt->add('b|base:', $b)->isa('number')->defaultValue(1);
    }

    public function arguments($args)
    {
        $args->add('dir')->isa('dir')->multiple();
    }

    public function execute($dir)
    {
        $entry = "";
        @$entry = $this->options->init;
        $ttl = $this->options->base;
        if ($ttl <= 0) {
            $ttl = 1;
        }
        if (is_file($entry)) {
            require_once($entry);
        }
        $oldFunctions = get_defined_functions();
        $pendingDirs = array((new Path($dir))->normalize());

        while (count($pendingDirs) > 0) {
            $subDirs = $this->requirePHPFiles(array_pop($pendingDirs));
            $pendingDirs = array_merge($pendingDirs, $subDirs);
        }

        $newFunctions = get_defined_functions();
        $funcs = array_diff($newFunctions['user'], $oldFunctions['user']);

        $b = new Benchmarker($ttl);
        foreach ($funcs as $f) {
            if (substr($f, 0, 9) == 'benchmark') {
                $b->register($f);
            }
        }

        $b->run(new DefaultFormatter);
    }

    private function requirePHPFiles($dir)
    {
        $ret = array();

        $children = glob((new Path("*", $dir))->expand());
        foreach ($children as $child) {
            if (is_dir($child)) {
                array_push($ret, $child);
                continue;
            }

            if (substr($child, strlen($child)-4) == '.php') {
                require_once($child);
            }
        }
        return $ret;
    }

}
