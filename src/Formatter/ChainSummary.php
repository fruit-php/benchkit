<?php

namespace Fruit\BenchKit\Formatter;

use ReflectionClass;

class ChainSummary extends AbstractSummary
{
    private function conf()
    {
        $conf = $this->test('chain');
        if (! is_array($conf)) {
            return array('Fruit\BenchKit\Formatter\DefaultSummaryLogger' => null);
        }
        $ret = array();
        $ret = array_filter($conf, function($k) {
            if (!class_exists($k)) {
                return false;
            }
            $ref = new ReflectionClass($k);
            return $ref->implementsInterface('Fruit\BenchKit\Formatter\Summary');
        }, \ARRAY_FILTER_USE_KEY);
        if (count($ret) == 0) {
            return array('Fruit\BenchKit\Formatter\DefaultSummaryLogger' => null);
        }
        return $ret;
    }

    public function format(array $groups)
    {
        $conf = $this->conf();

        $objs = array();
        foreach ($conf as $cls => $args) {
            $ref = new ReflectionClass($cls);
            if ($args) {
                $objs[] = $ref->newInstance($args);
            } else {
                $objs[] = $ref->newInstance();
            }
        }

        foreach ($objs as $k => $summary) {
            echo "Calling $k\n";
            $summary->format($groups);
        }
    }
}
