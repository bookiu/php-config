<?php

namespace Yaxin\PHPConfig;

use Exception;
use Illuminate\Config\Repository;
use Yaxin\PHPConfig\Exceptions\EnvironmentException;
use Yaxin\PHPConfig\Parser\ParserFactory;


class PHPConfig extends Repository
{
    const CONFIG_KEY_SEP = '.';

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var string
     */
    private $compileCachePath;

    /**
     * @var string Current environment
     */
    private $environment;

    /**
     * @var string[] Supported file extensions
     */
    private $extensions = ['yml', 'yaml', 'php', 'json'];

    /**
     * Loaded config files map
     *
     * @var array
     */
    private $loadedConfigFiles = array();

    /**
     * Loaded namespace map
     *
     * @var array
     */
    private $loadedNamespaces = array();


    public function __construct(string $configPath, string $compileCachePath = null,
                                ?string $environment = null)
    {
        $this->setConfigPath($configPath);
        $this->setCompileCachePath($compileCachePath);
        $this->setEnvironment($environment);

        parent::__construct();
    }

    /**
     * Get the specified configuration value and load configuration file if not exist
     *
     * @param array|string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key = '', $default = null)
    {
        try {
            $this->readConfigFromKey($key);
        } catch (Exception $ex) {
            return $default;
        }
        return parent::get($key, $default);
    }

    /**
     * Set config path
     *
     * @param string $configPath
     */
    protected function setConfigPath(string $configPath)
    {
        $this->configPath = rtrim($configPath, '/\\');
    }

    /**
     * Get config path
     * @return string
     */
    public function configPath()
    {
        return $this->configPath;
    }

    /**
     * Set compiled php path
     *
     * @param string|null $compileCachePath
     */
    protected function setCompileCachePath(?string $compileCachePath)
    {
        $this->compileCachePath = $compileCachePath !== null ? rtrim($compileCachePath, '/\\') : null;
    }

    /**
     * Get compiled php path
     *
     * @return string|null
     */
    public function compileCachePath()
    {
        return $this->compileCachePath;
    }

    /**
     * Set current environment.
     *
     * @param string|null $env
     */
    public function setEnvironment(?string $env)
    {
        $env = $env === null ? $this->detectEnvironment() : $env;
        if ($env === null) {
            throw new EnvironmentException('Environment not set');
        }
        $this->environment = $env;
    }

    /**
     * Get current environment
     *
     * @return string
     */
    public function environment()
    {
        return $this->environment;
    }

    /**
     * Get supported config file extensions
     *
     * @return string[]
     */
    public function extensions()
    {
        return $this->extensions;
    }

    /**
     * @return string|null
     */
    protected function detectEnvironment(): ?string
    {
        $configFiles = $this->getConfigFilePath('app', false);
        if ($configFiles) {
            $data = $this->getConfigFileData($configFiles[0]);
            $env = null;
            foreach (['env', 'environment'] as $k) {
                if (isset($data[$k])) {
                    return (string)$data[$k];
                }
            }
        }
        return null;
    }

    /**
     * Parse config key and get real config file then load it
     *
     * @param string|array $keys
     */
    protected function readConfigFromKey($keys)
    {
        $keys = (array)$keys;
        foreach ($keys as $key) {
            if (!is_string($key) || $this->configKeyCached($key)) {
                continue;
            }
            foreach ($this->getConfigFilePath($key) as $configFile) {
                $this->loadConfigFile($configFile);
            }
        }
    }

    /**
     * Check config key loaded before.
     *
     * @param string $key
     * @return bool
     */
    protected function configKeyCached(string $key): bool
    {
        $keyParts = explode(self::CONFIG_KEY_SEP, $key);
        while (count($keyParts)) {
            $namespace = implode(self::CONFIG_KEY_SEP, $keyParts);
            if ($this->configNamespaceCached($namespace)) {
                return true;
            }
            array_pop($keyParts);
        }
        return false;
    }

    /**
     * Cache namespace of the config key.
     *
     * @param string $namespace
     */
    protected function cacheConfigNamespace(string $namespace)
    {
        $this->loadedNamespaces[$namespace] = true;
    }

    /**
     * @param string $namespace
     * @return bool
     */
    protected function configNamespaceCached(string $namespace)
    {
        return isset($this->loadedNamespaces[$namespace]);
    }

    /**
     * Get config file path from config key.
     *
     * @param $key
     * @return ConfigFile[]
     */
    protected function getConfigFilePath($key, $withEnv = true): array
    {
        $keyParts = explode(self::CONFIG_KEY_SEP, (string)$key);

        $scanDirs = [];
        if ($withEnv) {
            $scanDirs[] = pathJoin($this->configPath(), $this->getEnvironmentFolderName());
        }
        $scanDirs[] = $this->configPath();

        foreach ($scanDirs as $basePath) {
            $segments = $keyParts;
            while ($segments) {
                $path = pathJoin($basePath, call_user_func_array('pathJoin', $segments));

                foreach ($this->extensions() as $ext) {
                    $file = $path . '.' . $ext;
                    if (!is_file($file)) {
                        continue;
                    }
                    $cf = ConfigFile::create(implode(self::CONFIG_KEY_SEP, $segments), $file);
                    return [$cf];
                }
                array_pop($segments);
            }
        }
        return [];
    }

    /**
     * Parse config file and put into data repository
     *
     * @param ConfigFile $configFile
     */
    protected function loadConfigFile(ConfigFile $configFile)
    {
        $filePath = $configFile->filePath();
        if ($this->configFileLoaded($filePath)) {
            return;
        }

        // Load PHP source file directly
        // Check config file cached as php file
        $compiledPHPFile = isPHPFile($filePath) ? null : $this->getCompiledPHPFile($filePath);
        if ($compiledPHPFile !== null && readableFile($compiledPHPFile)) {
            $this->set($configFile->namespace(), require($compiledPHPFile));
            return;
        }

        $data = $this->getConfigFileData($configFile);
        $this->set($configFile->namespace(), $data);

        // Cache every step to optimize
        $this->saveToPHPFile($compiledPHPFile, $data);
        $this->cacheConfigNamespace($configFile->namespace());
        $this->setConfigFileLoaded($filePath);
    }

    /**
     * Get config file data
     *
     * @param ConfigFile $configFile
     * @return array
     */
    protected function getConfigFileData(ConfigFile $configFile): array
    {
        $filePath = $configFile->filePath();
        $parser = ParserFactory::createParserFromFile($filePath);
        return $parser->parseFile($filePath);
    }

    /**
     * Get current environment folder name
     *
     * @return string
     */
    protected function getEnvironmentFolderName()
    {
        return $this->environment();
    }

    /**
     * Check config file had loaded.
     *
     * @param string $file
     * @return bool
     */
    protected function configFileLoaded(string $file)
    {
        return isset($this->loadedConfigFiles[$file]);
    }

    /**
     * Set config file as loaded.
     *
     * @param string $file
     */
    protected function setConfigFileLoaded(string $file)
    {
        $this->loadedConfigFiles[$file] = true;
    }

    /**
     * Get compile php file
     *
     * @param string $configFilePath
     * @return string
     */
    protected function getCompiledPHPFile(string $configFilePath)
    {
        $mtime = (int)filemtime($configFilePath);
        $hash = md5(sprintf('%s_%d', $configFilePath, $mtime));
        $relativeConfigFilePath = substr($configFilePath, strlen($this->configPath()) + 1);
        $filename = sprintf(
            '%s_%s.php',
            str_replace(['\\', '/', '.'], '__', $relativeConfigFilePath),
            $hash
        );

        return pathJoin($this->compileCachePath(), $filename);
    }

    /**
     * Save data as compiled PHP file
     *
     * @param string|null $compiledPHPFile
     * @param array $data
     */
    protected function saveToPHPFile(?string $compiledPHPFile, array $data)
    {
        if ($compiledPHPFile === null) {
            return;
        }
        if (!($this->compileCachePath() !== null && writeableDir($this->compileCachePath()))) {
            return;
        }
        file_put_contents($compiledPHPFile, sprintf('<?php return %s;', var_export($data, true)));
    }
}
