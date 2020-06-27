# php-config

`php-config`是一个PHP下的配置文件加载库，它可以加载yaml、json和php等格式的配置文件，并且可以根据配置项自动加载所在的配置文件。配置可以通过`get`方法获取，各个层级使用“.”号分割。`php-config`支持多级文件夹，且每级文件夹名最终体现为配置值。

## 安装

`php-config`可以通过`composer`安装. 在`composer.json`文件的**require**下加入`"yaxin/php-config": "^"1.0"`，然后执行`composer update`即可，或者直接执行如下命令：

```bash
composer require yaxin/php-config
```

## 使用

使用`php-config`需要先实例化`PHPConfig`，`PHPConfig`类有三个参数，分别为：

1. 配置文件路径
2. yaml、json等非PHP配置文件缓存路径
3. 应用当前使用的环境

```php
use Yaxin\PHPConfig\PHPConfig;

$config = new PHPConfig('/path/to/config/path', '/path/to/compile_cache/path', 'production');
```

通过`get`方法来获取配置项的值，配置项值以“.”分割配置级别。

## Examples

```text
config directory structure

.
├── app.yaml
├── abc.php
├── bcd.yml
├── cde.json
├── info.php
├── regions
│   └── beijing.yml
├── production
|   └── database.yml
├── testing
|   └── database.yml
└── development
    └── database.yml
```

```php
use Yaxin\PHPConfig\PHPConfig;

$config = new PHPConfig('/path/to/config/path', '/path/to/compile_cache/path', 'production');
$config->get('app.name');
// output is: php-config

$config->get('regions.beijing.name');
// output is: beijing

$config->get('database.default.host');
// output is: 1.2.3.4

$config->get('none.exist.key');
// output is: null

$config->get('none.exist.key', 'php-config');
// output is: php-config
```

## License
[MIT](https://choosealicense.com/licenses/mit/)