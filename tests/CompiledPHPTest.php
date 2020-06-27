<?php

namespace Yaxin\TestCase\PHPConfig;

use Yaxin\PHPConfig\PHPConfig;


class CompiledPHPTest extends TestCase
{
    protected $config = null;

    protected function setUp(): void
    {
        $this->config = new PHPConfig($this->configPath(), $this->cachePath());
        parent::setUp();
    }

    /**
     * Test compiled php file exists
     */
    public function testCompiledPHPFileExists()
    {
        foreach($this->testData as $k => $v) {
            $this->config->get($k);
        }

        foreach($this->testData as $k => $v) {
            if ($v['value'] === null) {
                continue;
            }
            $file = $v['file'];
            if (isPHPFile($file)) {
                continue;
            }

            $pattern = str_replace(['/', '\\', '.'], '__', substr($file, strlen($this->configPath()) + 1));
            $pattern = pathJoin($this->cachePath(), $pattern . '_*.php');

            $result = glob($pattern);
            $this->assertTrue(is_array($result));
            if (count($result) == 0) {
                print_r($pattern . "\n");
                print_r($result . "\n");
            }
            $this->assertTrue(count($result) > 0);
        }
    }

    public function testCompiledPHPFileContent()
    {
        foreach($this->testData as $k => $v) {
            $this->config->get($k);
        }

        foreach($this->testData as $k => $v) {
            if ($v['value'] === null) {
                continue;
            }
            $file = $v['file'];
            $pattern = str_replace(['/', '\\', '.'], '__', substr($file, strlen($this->configPath()) + 1));
            $pattern = pathJoin($this->cachePath(), $pattern . '_*.php');

            foreach (glob($pattern) as $item) {
                $this->assertEquals($this->config->get($v['cacheKey']), require($item));
            }
        }
    }
}