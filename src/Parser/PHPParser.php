<?php

namespace Yaxin\PHPConfig\Parser;

use Exception;
use Yaxin\PHPConfig\Exceptions\ParseException;


class PHPParser implements ParserInterface
{
    public function parse(string $content): array
    {
        throw new ParseException('Unspport parse PHP string');
    }

    public function parseFile(string $filePath): array
    {
        try {
            return require($filePath);
        } catch (Exception $ex) {
            throw new ParseException('Parse PHP file failed');
        }
    }

    public function dump(array $data): string
    {
        return sprintf('<?php return %s;', var_export($data, true));
    }
}