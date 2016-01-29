Installation
============

Requirements
------------

Melody depends on [composer](https://getcomposer.org/) to install packages. To
do so, it'll need to find an available instance of composer on your machine. It
will search for `composer` and `composer.phar` in the current directory as well
as in your path.

Locally
-------

Download the [melody.phar](http://get.sensiolabs.org/melody.phar) file and store
it somewhere on your computer.

Globally (manual)
-----------------

You can run these commands to easily access melody from anywhere on your system:

```bash
$ sudo sh -c "curl http://get.sensiolabs.org/melody.phar -o /usr/local/bin/melody && chmod a+x /usr/local/bin/melody"
```

Then, just run `melody`.
