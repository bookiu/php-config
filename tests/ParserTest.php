<?php

namespace Yaxin\TestCase\PHPConfig;

use Yaxin\PHPConfig\Parser\JsonParser;
use Yaxin\PHPConfig\Parser\ParserFactory;
use Yaxin\PHPConfig\Parser\PHPParser;
use Yaxin\PHPConfig\Parser\YamlParser;


class ParserTest extends TestCase
{
    public function testParserType()
    {
        $expected = array(
            'aaa.yaml' => YamlParser::class,
            'bcd.yml' => YamlParser::class,
            'abc.php' => PHPParser::class,
            'cde.json' => JSONParser::class
        );

        foreach($expected as $file => $class) {
            $parser = ParserFactory::createParserFromFile($this->configPath() . '/' . $file);
            $this->assertTrue($parser instanceof $class);
        }
    }
}