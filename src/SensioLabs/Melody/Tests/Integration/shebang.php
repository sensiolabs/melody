#!/usr/local/bin/melody run
<?php
<<<CONFIG
packages:
    - "twig/twig:1.16.0"
CONFIG;

$twig = new Twig_Environment(new Twig_Loader_Array(array(
    'foo' => 'Hello {{ include("bar") }}',
    'bar' => 'world'
)));

echo $twig->render('foo');
