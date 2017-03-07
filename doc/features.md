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
file. It will report an error otherwise.

For those users behind a proxy server, `melody` now uses the `HTTPS_PROXY`
environment variable.

Run streamed script
-------------------

You can run scripts from every supported streams (list streams with
`stream_get_wrappers`):

* `http`: `http://my.private/snippets/test.php`
* `ftp`: `ftp://user:password@server/public/test.php`
* `php`: `php://stdin`
* `data`: `data://text/plain;base64,SSBsb3ZlIFBIUAo[...]==`
* `phar`: `phar:///opt/resource.phar/test.php`
* `zlib`: `compress.zlib:///opt/resource.gz`
* `bzip2`: `compress.bzip2:///opt/resource.bz2`

Caching
-------

If you ran twice or more a script with the same dependencies, theses
dependencies will be cached.

If you don't want this cache, you can disable the cache from the command line:

```bash
$ melody run --no-cache test.php
```

Debug scripts
-------------

In case you want to have a look whats going on behind the scenes, use the verbose
flag make melody print output produced by Composer:

```bash
$ melody run --vvv test.php
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
$ melody run --prefer-source test.php
```

Arguments
---------

Melody allows you to pass arguments to your script.

The simplest way, is to add your arguments after the name of the script.

```bash
$ melody run test.php arg1 arg2
```

But this method does not works with options starting by `-` or `--`, because
melody will catch them. To use options, you must prepend your options by
` -- `.

```bash
$ melody run test.php -- -a --arg1 arg2
```

Trust
-----

By default, when you run an external resource (ie: a gist) Melody will display
a warning message to let you choose if you want or not run the script.
When you trust the resource, Melody will remember your answer and never
again ask you for confirmation.

```bash
You are running an untrusted resource
  URL:             https://gist.github.com/565752f13499a3fa17d9
  Revision:        #1
  Owner:           lyrixx
  Created at:      Fri, 05 Dec 2014 22:22:28 +0000
  Last update:     Tue, 09 Dec 2014 13:45:02 +0000

What do you want to do (show-code, continue, abort)?
```

But if you trust the resource and don't want to interact with Melody, you
can pass the parameter `--trust` to the command

```bash
$ melody run 565752f13499a3fa17d9 --trust
```

User Configuration
------------------

Melody stores your configuration in a file located in `~/.sensiolabs/melody.yml`.
This file contains:

* a list of trusted resources signatures (see section Trust).
* a list of trusted users.

This file is stored with a YAML syntax. You can manually edit it to complete
the list of trusted user for instance.

```yaml
trust:
  signatures: []
  users:
    - jeremy-derusse
    - lyrixx
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
    - "silex/silex: *"
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

Beware that `CONFIG` section contents must comply with YAML syntax restrictions:

* `- "silex/silex: *"` without quotes is an invalid YAML.
* `- "silex/silex: ~1.2"` without quotes is a YAML object and refused by melody.
* `- "-S"` without quotes is an array of arrays.


Using fork and private repositories
-----------------------------------

If you need to use packages not registered in Packagist repository, you can
specify repositories in the YAML configuration.
See [composer documentation](https://getcomposer.org/doc/05-repositories.md).

```php
<?php

<<<CONFIG
repositories:
    - type: vcs
      url: https://example.com/vendor/my-private-repo.git
packages:
    - "vendor/package-name: 1.0.2"
CONFIG;
```
