Installation
============

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

Then, just run `melody`

Installation from Source
------------------------

To run tests, or develop Melody itself, you must use the sources and not the phar
file as described above.

1. Run `git clone https://github.com/sensiolabs/melody.git`
2. Download the [`melody.phar`](http://get.sensiolabs.org/melody.phar) executable
3. Run Melody to get the dependencies: `cd melody && php ../composer.phar install`

You can now run Melody by executing the `bin/melody` script: `php /path/to/melody/bin/melody`
