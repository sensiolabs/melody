Melody - One-file composer scripts
==================================
[![Build Status](https://api.travis-ci.org/sensiolabs/melody.png?branch=master)](https://travis-ci.org/sensiolabs/melody)

Create a file named `test.php`:

```php
<?php
<<<CONFIG
packages:
    - "symfony/finder: ~2.8"
CONFIG;

$finder = Symfony\Component\Finder\Finder::create()
    ->in(__DIR__)
    ->files()
    ->name('*.php')
;

foreach ($finder as $file) {
    echo $file, "\n";
}
```

And simply run it:

```bash
$ melody run test.php
```

![demo](http://melody.sensiolabs.org/img/melody.gif)

More Information
----------------

Read the [documentation](http://melody.sensiolabs.org) for more information.
