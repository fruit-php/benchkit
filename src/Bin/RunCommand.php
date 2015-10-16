<?php

namespace Fruit\BenchKit\Bin;

use CLIFramework\Command;
use Fruit\PathKit\Path;
use Fruit\BenchKit\Benchmarker;

class RunCommand extends Command
{
    public function options($opt)
    {
        $desc = 'run this file to initialize the environment. (setting up db, autoload etc.)';
        $opt->add('i|init?', $desc)->isa('file');
    }

    public function arguments($args)
    {
        parent::arguments($args);
        $args->add('dir')->isa('dir')->multiple();
    }

    public function execute($dir)
    {
        $entry = "";
        @$entry = $this->options->init;
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

        $b = new Benchmarker;
        foreach ($funcs as $f) {
            $b->register($f);
        }

        $b->run(array($this, 'formatter'));
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

    public function formatter($f, $result)
    {
        $f = substr($f, 9);
        list($n, $t) = $result;

        echo sprintf("%30s  %8d times  %fms/op\n", $f, $n, $t*1000.0/$n);
    }
}
