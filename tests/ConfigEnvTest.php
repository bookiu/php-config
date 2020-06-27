<?php

namespace Yaxin\TestCase\PHPConfig;


use Yaxin\PHPConfig\PHPConfig;


class ConfigEnvTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->testData = array(
            'info.name' => array(
                'value' => 'production_yaxin'
            ),
            'sub.xyz.region' => array(
                'value' => 'ap-guangzhou'
            )
        );
    }

    public function testConfigValue()
    {
        $config = new PHPConfig($this->configPath(), $this->cachePath());

        foreach ($this->testData as $k => $v) {
            $this->assertEquals($v['value'], $config->get($k));
        }
    }
}