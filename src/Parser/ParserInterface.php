<?php

namespace Yaxin\PHPConfig\Parser;


interface ParserInterface
{
    /**
     * Parse string content to array.
     *
     * @param string $content string content to parse
     * @return array
     */
    public function parse(string $content): array;

    /**
     * Parse file to array.
     *
     * @param string $filePath file to parse
     * @return array
     */
    public function parseFile(string $filePath): array;

    /**
     * Dump array data to string.
     *
     * @param array $data array data to encode
     * @return string
     */
    public function dump(array $data): string;
}