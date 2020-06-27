<?php

namespace Yaxin\TestCase\PHPConfig;

use Yaxin\PHPConfig\PHPConfig;


class ConfigKeyTest extends TestCase
{
    public function testKeyFile()
    {
        $cases = array(
            'cde.version' => pathJoin(__DIR__, 'config', 'cde.json'),
            'sub.def.csv' => pathJoin(__DIR__, 'config', 'sub', 'def.yml'),
            'sub.grand.efg.name' => pathJoin(__DIR__, 'config', 'sub', 'grand', 'efg.json')
        );

        $getConfigFilePath = self::getMethod(PHPConfig::class, 'getConfigFilePath');

        foreach ($cases as $key => $filepath) {
            $config = new PHPConfig($this->configPath());
            $configFiles = $getConfigFilePath->invokeArgs($config, [$key]);

            $this->assertEquals(count($configFiles), 1);

            $this->assertEquals($configFiles[0]->filepath(), $filepath);
        }
    }

    public function testKeyCached()
    {
        $cases = array(
            'cde.version' => 'cde',
            'sub.def.csv' => 'sub.def',
            'sub.grand.efg.name' => 'sub.grand.efg'
        );

        $configNamespaceCached = self::getMethod(PHPConfig::class, 'configNamespaceCached');

        $config = new PHPConfig($this->configPath());
        foreach ($cases as $case => $namespace) {
            $config->get($case);
        }

        foreach ($cases as $case => $namespace) {
            $this->assertTrue($configNamespaceCached->invokeArgs($config, [$namespace]));
        }
    }
}