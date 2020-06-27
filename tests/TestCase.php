<?php

namespace Yaxin\TestCase\PHPConfig;

use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;


abstract class TestCase extends BaseTestCase
{
    protected $testData = null;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->testData = array(
            'cde.version' => array(
                'value' => '1.0.0-rc3',
                'cacheKey' => 'cde',
                'file' => pathJoin(__DIR__, 'config', 'cde.json'),
            ),
            'sub.def.csv' => array(
                'value' => [
                    'text/x-comma-separated-values',
                    'text/comma-separated-values',
                    'application/octet-stream',
                    'application/vnd.ms-excel',
                    'application/x-csv',
                    'text/x-csv',
                    'text/csv',
                    'application/csv',
                    'application/excel',
                    'application/vnd.msexcel'
                ],
                'cacheKey' => 'sub.def',
                'file' => pathJoin(__DIR__, 'config', 'sub', 'def.yml')
            ),
            'aaa.services.redis.container_name' => array(
                'value' => 'redis',
                'cacheKey' => 'aaa',
                'file' => pathJoin(__DIR__, 'config', 'aaa.yaml')
            ),
            'abc' => array(
                'value' => array(
                    'name' => 'yaxin',
                    'sex' => 'male',
                    'age' => 232
                ),
                'cacheKey' => 'abc',
                'file' => pathJoin(__DIR__, 'config', 'abc.php')
            ),
            'bcd.networks' => array(
                'value' => array(
                    'web_runtime' => array(
                        'external' => true
                    )
                ),
                'cacheKey' => 'bcd',
                'file' => pathJoin(__DIR__, 'config', 'bcd.yml')
            ),
            'cde.autoupdate.architecture.64bit.url' => array(
                'value' => 'https://github.com/apex/apex/releases/download/v$version/apex_$version_windows_amd64.tar.gz',
                'cacheKey' => 'cde',
                'file' => pathJoin(__DIR__, 'config', 'cde.json')
            ),
            'xyz.a.b.c' => array(
                'value' => null,
                'cacheKey' => '',
                'file' => ''
            ),
            'sub.def.hqx' => array(
                'value' => 'application/mac-binhex40',
                'cacheKey' => 'sub.def',
                'file' => pathJoin(__DIR__, 'config', 'sub', 'def.yml')
            ),
            'sub.grand.efg.name' => array(
                'value' => 'vendor_name/subApplication',
                'cacheKey' => 'sub.grand.efg',
                'file' => pathJoin(__DIR__, 'config', 'sub', 'grand', 'efg.json')
            ),
        );

        parent::__construct($name, $data, $dataName);
    }

    protected function configPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'config';
    }

    protected function cachePath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'cache';
    }

    protected static function getMethod(string $class, string $name)
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function tearDown(): void
    {
        $files = glob($this->cachePath() . '/*');
        array_walk($files, function ($file) {
            unlink($file);
        });
        parent::tearDown();
    }
}
