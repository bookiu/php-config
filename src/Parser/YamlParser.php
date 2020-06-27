<?php

namespace Yaxin\PHPConfig\Parser;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException as YamlParseException;
use Yaxin\PHPConfig\Exceptions\ParseException;


class YamlParser implements ParserInterface
{
    public function parse(string $content): array
    {
        try {
            return Yaml::parse($content);
        } catch (YamlParseException $ex) {
            throw new ParseException($ex->getMessage());
        }
    }

    public function parseFile(string $filePath): array
    {
        try {
            return Yaml::parseFile($filePath);
        } catch (YamlParseException $ex) {
            throw new ParseException($ex->getMessage());
        }
    }

    public function dump(array $data): string
    {
        return Yaml::dump($data);
    }
}