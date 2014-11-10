Development
===========

Please read the [Contributing guide](../CONTRIBUTING.md).

Installation
------------

Clone repository and install dependencies:

    git clone git@github.com:sensiolabs/melody.git
    cd melody
    composer install

Running tests
-------------

A script is available to execute all projects tests. It should work after a
fresh `git clone`:

    phpunit

Generating the PHAR
-------------------

You need [box](http://box-project.org/) to build the phar, then

    box build
