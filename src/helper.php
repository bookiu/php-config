<?php

if (!function_exists('arrayRemove')) {
    function arrayRemove($input, $val)
    {
        if (($key = array_search($val, $input)) !== false) {
            array_splice($input, $key, 1);
        }
        return $input;
    }
}

if (!function_exists('pathJoin')) {
    /**
     * Join paths with DIRECTORY_SEPARATOR
     *
     * @return string
     */
    function pathJoin()
    {
        $args = func_get_args();
        if (!$args) {
            return '';
        }
        return implode(DIRECTORY_SEPARATOR, $args);
    }
}

/**
 * File readable check.
 *
 * @param $filePath
 * @return bool
 */
function readableFile($filePath)
{
    return (is_file($filePath) && is_readable($filePath));
}

/**
 * Check dir writeable
 *
 * @param string $dir
 * @return bool
 */
function writeableDir(string $dir)
{
    return (is_dir($dir) && is_writeable($dir));
}

/**
 * Check whether a file is PHP file
 *
 * @param string $file
 * @return bool
 */
function isPHPFile(string $file)
{
    return pathinfo($file, PATHINFO_EXTENSION) === 'php';
}