<?php

namespace Fruit\BenchKit\Formatter;

use Fruit\BenchKit\Benchmark;

class HighChartSummary extends AbstractSummary
{
    public function format(array $groups)
    {
        $str = '<html><head>';
        $str .= '<meta charset="utf-8" />';
        $str .= '<meta name="viewport" content="width=device-width, initial-scale=1" />';
        $str .= '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
        $str .= '<script src="http://code.highcharts.com/highcharts.js"></script>';
        $str .= '<style>%s</style></head><body>%s<script>%s</script></body></html>';
        $script = '';
        $css = '';
        $body = '';
        $id = 0;
        $type = $this->test('type');
        if ($type) {
            $type = strtolower($type);
        }
        $yTitle = 'Loops per time unit';
        if ($type == 'time') {
            $yTitle = 'milliseconds per loop';
        }
        foreach ($groups as $group => $bs) {
            $gname = $group;
            if ($group == '') {
                $gname = 'Global benchmarks';
            }
            $idstr = sprintf('div%d', $id);
            $body .= sprintf('<div id="%s"></div>', $idstr);
            $data = array();
            foreach ($bs as $b) {
                $tmp = array('name' => $b->name, 'data' => array($b->loops()));
                if ($type == 'time') {
                    $tmp['data'][0] = $b->runTime();
                }
                $data[] = $tmp;
            }
            $conf = array(
                'chart' => array('type' => 'bar'),
                'title' => array('text' => $gname),
                'yAxis' => array('title' => array('text' => $yTitle)),
                'series' => $data,
            );
            $script .= sprintf('$("#%s").highcharts(%s);', $idstr, json_encode($conf));
            $css .= sprintf("%s {width: 100%%; height: %dpx}\n", $idstr, count($bs) * 30);
            $id++;
        }
        echo sprintf($str, $css, $body, $script);
    }
}
