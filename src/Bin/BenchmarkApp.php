<?php

namespace Fruit\BenchKit\Bin;

use CLIFramework\Application;

class BenchmarkApp extends Application
{
    public function brief()
    {
        return 'Scan directory and run benchmarks in it.';
    }

    public function init()
    {
        $this->command('run', 'Fruit\BenchKit\Bin\RunCommand');
    }
}
