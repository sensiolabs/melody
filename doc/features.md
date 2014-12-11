Available features
==================

Run gists scripts
-----------------

You can easily [create a gist](https://gist.github.com) to share a snippet and
execute it using `melody`. Instead of downloading the file to your computer,
simply pass the URL to `melody`:

```bash
$ melody run https://gist.github.com/lyrixx/565752f13499a3fa17d9
```

Supported formats:

* Gist Id: `565752f13499a3fa17d9`
* Username/Id: `lyrixx/565752f13499a3fa17d9`
* Gist URI: `https://gist.github.com/lyrixx/565752f13499a3fa17d9`

Please note that `melody` can only handle gists which contain a single PHP 
file.

Caching
-------

If you ran twice or more a script with the same dependencies, theses
dependencies will be cached.

If you don't want this cache, you can disable the cache from the command line:

```bash
$ php melody.phar run --no-cache test.php
```

Debug scripts
-------------

You can view Composer install your dependencies. If you want to debug things a
little bit, it might be useful:

```bash
$ php melody.phar run --vvv test.php
```

Download Mode
-------------

There are two ways of downloading a package: `source` and `dist`. By default
Melody will use the `dist` mode.

If `--prefer-source` is enabled, Melody will use the `source` mode and install
from source if there is one. The `--prefer-source` can be used if you don't 
want Composer to download release archives but do `git clone` instead. It's 
very useful if you suffer from API throttling.

Only use this method if you know what you're doing, because `--prefer-source` 
is not efficient at all.

```bash
$ php melody.phar run --prefer-source test.php
```

Arguments
---------

Melody allows you to pass arguments to your script.

The simplest way, is to add your arguments after the name of the script.

```bash
$ php melody.phar run test.php arg1 arg2
```

But this method does not works with options starting by `-` or `--`, because
melody will catch them. To use options, you must prepend your options by
` -- `.

```bash
$ php melody.phar run test.php -- -a -arg1 arg2
```

Front matter
------------

The script you want to run with melody **must** start with a YAML configuration
embedded in a `heredoc` string named `CONFIG`. This config must contain at 
least one package to install.

Optionally you can provide a list of options to pass to php command. It could 
be useful to e.g. start a php web server or define php.ini settings.

```php
<?php
<<<CONFIG
packages:
    - silex/silex
php-options:
    - "-S"
    - "localhost:8000"
CONFIG;

$app = new Silex\Application();
$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});
$app->run();
```

Beware of YAML syntax:

* `- silex/silex: ~1.2` without double quote is an invalid YAML
* `- -S` without double quote is an array of arrays
