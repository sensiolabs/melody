<?php

<<<CONFIG
repositories:
    - type: vcs
      url: https://github.com/fabpot/Pimple
packages:
    - "pimple/pimple: 1.0.2"
CONFIG;

$container = new \Pimple(array('key' => 'value'));
echo $container['key'];
