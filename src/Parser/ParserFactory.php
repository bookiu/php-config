<?php

namespace Yaxin\PHPConfig\Parser;


use Yaxin\PHPConfig\Exceptions\InvalidFileTypeException;


class ParserFactory
{
    public static function createParserFromFile(string $file): ParserInterface
    {
        $type = pathinfo($file, PATHINFO_EXTENSION);
        return self::createParser($type);
    }

    public static function createParserFromType(string $type): ParserInterface
    {
        return self::createParser($type);
    }

    public static function createParser(string $type): ParserInterface
    {
        switch ($type) {
            case 'php':
                return new PHPParser();
            case 'yaml':
            case 'yml':
                return new YamlParser();
            case 'json':
                return new JsonParser();
            default:
                throw new InvalidFileTypeException('Parser of file type ' . $type . ' not found');
        }
    }
}
