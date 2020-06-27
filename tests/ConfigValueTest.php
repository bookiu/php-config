<?php

namespace Yaxin\TestCase\PHPConfig;


use Yaxin\PHPConfig\PHPConfig;


class ConfigValueTest extends TestCase
{
    public function testConfigValue()
    {
        $expected = array(
            'aaa.services.redis.container_name' => 'redis',
            'abc' => array(
                'name' => 'yaxin',
                'sex' => 'male',
                'age' => 232
            ),
            'bcd.networks' => array(
                'web_runtime' => array(
                    'external' => true
                )
            ),
            'cde.autoupdate.architecture.64bit.url' => 'https://github.com/apex/apex/releases/download/v$version/apex_$version_windows_amd64.tar.gz',
            'xyz.a.b.c' => null,
        );

        $this->runAssert($expected);
    }

    public function testFolderNotKey()
    {
        $expected = array(
            'sub' => null,
            'sub.grand' => null
        );

        $this->runAssert($expected);
    }

    public function testSubFolder()
    {
        $expected = array(
            'sub.grand.efg.name' => 'vendor_name/sub',
            'sub.def.hqx' => 'application/mac-binhex40'
        );

        $this->runAssert($expected);
    }

    protected function runAssert($expected)
    {
        $config = new PHPConfig($this->configPath());
        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $config->get($key));
        }
    }
}