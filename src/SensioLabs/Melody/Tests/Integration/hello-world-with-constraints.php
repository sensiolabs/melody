<?php

<<<CONFIG
packages:
    - "twig/twig:1.16.0"
    - "php: >=5.3.0"
    - "ext-pdo: *"
CONFIG;

$twig = new Twig_Environment(new Twig_Loader_Array(array(
    'foo' => 'Hello {{ include("bar") }}',
    'bar' => 'world',
)));

echo $twig->render('foo');
