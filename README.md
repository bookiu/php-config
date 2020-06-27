# php-config

`php-config` is a config file reader, that can read yaml json and php format config file.

## Installation

`php-config` is available via composer. Just add the following code to your `composer.json` file under **required** section and execute command `composer update` or you can run directly:

```bash
composer require yaxin/php-config
```

## Usage

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