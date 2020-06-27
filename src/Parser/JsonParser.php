<?php

namespace Yaxin\PHPConfig\Parser;

use Yaxin\PHPConfig\Exceptions\ParseException;


class JsonParser implements ParserInterface
{
    public function parse(string $content): array
    {
        $data = json_decode($content, true);
        if (null === $data) {
            throw new ParseException('Decode json failed');
        }
        return $data;
    }

    public function parseFile(string $filePath): array
    {
        $data = json_decode(file_get_contents($filePath), true);
        if (null === $data) {
            throw new ParseException('Decode json file failed');
        }
        return $data;
    }

    public function dump(array $data): string
    {
        return json_encode($data);
    }
}