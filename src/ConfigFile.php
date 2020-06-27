<?php

namespace Yaxin\PHPConfig;


class ConfigFile
{
    private $namespace;

    private $filePath;

    public function __construct(string $namespace, string $filePath)
    {
        $this->namespace = $namespace;
        $this->filePath = $filePath;
    }

    public function namespace()
    {
        return $this->namespace;
    }

    public function filePath()
    {
        return $this->filePath;
    }

    public static function create(string $namespace, string $filePath)
    {
        return new self($namespace, $filePath);
    }
}