<?php

namespace Fruit\BenchKit\Formatter;

use XHProfRuns_Default;

/**
 * XhprofSummary gather xhprof data and save with default XHProfUI.
 *
 * This formatter accepts constructor argument in JSON format.
 *
 * Available arguments:
 *
 * - dir: where you want to save the xhprof data.
 */
class XhprofSummary extends AbstractSummary
{
    public function format(array $groups)
    {
        $dir = $this->test('dir');
        if ($dir) {
            $run = new XHProfRuns_Default($dir);
        } else {
            $run = new XHProfRuns_Default;
        }
        foreach ($groups as $group => $bs) {
            foreach ($bs as $b) {
                if (!$b->X()) {
                    continue;
                }

                $name = $group . '_' . $b->name;
                $run->save_run($b->X(), $name);
            }
        }
    }
}
