<?php

<<<CONFIG
packages:
    - "pimple/pimple: 1.0.2"
CONFIG;

$container = new \Pimple(array('key' => 'value'));
echo $container['key'];
